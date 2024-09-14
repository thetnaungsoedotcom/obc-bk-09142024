/**
 * WP Force SSL
 * https://wpforcessl.com/
 * (c) WebFactory Ltd, 2019 - 2022
 */

jQuery(document).ready(function($){
    if (typeof wp_force_ssl_rebranding  == 'undefined') {
      return;
    }
  
    if($('[data-slug="wp-force-ssl"]').length > 0){
        $('[data-slug="wp-force-ssl"]').children('.plugin-title').children('strong').html('<strong>' + wp_force_ssl_rebranding.name + '</strong>');
    }
  
  });
  