<?php
/*
 * WP Force SSL PRO
 * LetsEncrypt Certificate Functions
 * (c) WebFactory Ltd, 2015 - 2022
 */

// include only file
if (!defined('ABSPATH')) {
    die('Do not open this file directly.');
}

// Importing the classes.
use LEClient\LEClient;
use LEClient\LEOrder;

class WP_Force_SSL_LetsEncrypt
{
    function generate_ssl_certificate($email, $domain, $challenge_dir, $ssl_dir)
    {
        global $wp_force_ssl;

        ini_set('max_execution_time', 180);
        require_once('vendor/autoload.php');
        
        $email = array($email);
        $basename = $domain;
        $domains = array($domain);
        try{
            $client = new LEClient($email, LEClient::LE_PRODUCTION, false, $ssl_dir);
        } catch (Exception $e){
            $wp_force_ssl->generate_certificate_log($e->getMessage(), true);
            return new WP_Error(1, 'Error connecting to LetsEncrypt ' . $e->getMessage());
        }
        
        $wp_force_ssl->generate_certificate_log('LetsEncrypt: Requesting certificate order');
        $order = $client->getOrCreateOrder($basename, $domains);
        
        if (!$order->allAuthorizationsValid()) {
            $wp_force_ssl->generate_certificate_log('LetsEncrypt: Checking for pending authorizations');
            $pending = $order->getPendingAuthorizations(LEOrder::CHALLENGE_TYPE_HTTP);
            
            if (!empty($pending)) {
                $wp_force_ssl->generate_certificate_log('LetsEncrypt: Pending authorization HTTP challenges found');
                foreach ($pending as $challenge) {
                    $wp_force_ssl->generate_certificate_log('LetsEncrypt: Creating challenge file ' . trailingslashit($challenge_dir) . $challenge['filename']);
                    file_put_contents(trailingslashit($challenge_dir) . $challenge['filename'], $challenge['content']);
                    
                    $wp_force_ssl->generate_certificate_log('LetsEncrypt: Verify challenge');
                    $order->verifyPendingOrderAuthorization($challenge['identifier'], LEOrder::CHALLENGE_TYPE_HTTP);
                }
            }
        }
        
        $wp_force_ssl->generate_certificate_log('LetsEncrypt: Recheck for pending authorizations');
        if ($order->allAuthorizationsValid()) {
            $wp_force_ssl->generate_certificate_log('LetsEncrypt: All pending authorizations verified');
            if (!$order->isFinalized()) {
                $wp_force_ssl->generate_certificate_log('LetsEncrypt: Finalizing order');
                $order->finalizeOrder();
            }
            if ($order->isFinalized()) {
                $order->getCertificate();
                $wp_force_ssl->generate_certificate_log('LetsEncrypt: Certificate generated');
            } else {
                $wp_force_ssl->generate_certificate_log('LetsEncrypt: Certificate generation failed, order not finalized', true);
            }
        } else {
            $wp_force_ssl->generate_certificate_log('LetsEncrypt: Authorizations could not be verified. Please check that ' . trailingslashit($domain) . $challenge['filename'] . ' is publicly accessible', true);
            return new WP_Error(1, 'LetsEncrypt: Domain verification failed. Please check that <strong>' . trailingslashit($domain) . $challenge['filename'] . '</strong> is publicly accessible');
        }
    }
} // WP_Force_SSL_LetsEncrypt
