    <?php 
    if ($site_info['logo']) {
        $logo = $site_info['logo'];
    } else {
        $logo = ASSET_URL.'images/olive-footer-logo.png';
    }
    $social = get_field('social_media_links','option');
    $facebook = $social['facebook'];
    $instagram = $social['instagram'];
    $telegram = $social['telegram'];
	$linkedin = $social['linkedin'];
    $qr_code = $social['qr_code'];
    $site_info = get_field('general_setting','option');
    $contact_address = $site_info['contact_address'];
    $ph_no = $site_info['contact_number'];
    $email = $site_info['contact_email'];
    ?>
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-10">
                    <div class="row">
                        <div class="col-lg-3 footer-block">
                            <img class="f-logo" src="<?php echo $logo; ?>">
                        </div>
                        <div class="col-lg-2 col-md-6 footer-block">
                            <h4 class="green-title">ABOUT</h4>
                            <?php 
                            wp_nav_menu(array(
                                'theme_location' => 'footer_nav_1',
                                'menu' => 'footer',
                                'depth' => 0,
                                'menu_class' => 'footer-nav',
                                'container' => 'ul',
                            ));
                            ?>
                        </div>
                        <div class="col-lg-3 col-md-6 footer-block">
                            <h4 class="green-title">STRATEGIES</h4>
                            <?php  
                            wp_nav_menu(array(
                                'theme_location' => 'footer_nav_2',
                                'menu' => 'footer2',
                                'depth' => 0,
                                'menu_class' => 'footer-nav',
                                'container' => 'ul',
                            ));
                            ?>
                        </div>
                        <div class="col-lg-4 footer-block">
                            <h4 class="green-title">CONTACT</h4>
                            <ul class="address">
                                 <?php if ($site_info['contact_address']) { ?>
                                    <div class="d-flex mb">
                                        <div class="flex-shrink-0"><i class="fa-regular fa-location-dot"></i></div>
                                        <div class="flex-grow-1 ms-3"><p><?php echo nl2br($site_info['contact_address']); ?></p></div>
                                    </div>
                                <?php } ?>

                                <?php if ($site_info['contact_email']) { ?>
                                    <div class="d-flex mb">
                                        <div class="flex-shrink-0"><i class="far fa-envelope"></i></div>
                                        <div class="flex-grow-1 ms-3"><p><?php contact_link($email, 'mailto:'); ?></p></div>
                                    </div>
                                <?php } ?>

                                <?php if ($site_info['contact_number']) { ?>
                                    <div class="d-flex mb">
                                        <div class="flex-shrink-0"><i class="far fa-phone"></i></div>
                                        <div class="flex-grow-1 ms-3"><p><?php contact_link( $ph_no  , 'tel:'); ?></p></div>
                                    </div>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 footer-social-wrap">
                    <h4 class="green-title">SOCIAL</h4>
                    <div class="d-flex align-items-center social-icon-wrap">
                        <div class="flex-shrink-0">
                            <ul class="social-icon">
                                <li>
                                    <?php if ($facebook) { ?>
                                        <a href="<?php echo $facebook; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                    <?php } ?>
                                </li>
                                <li>
                                    <?php if ($instagram) { ?>
                                        <a href="<?php echo $instagram; ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                                    <?php } ?>
                                </li>
                                <li>
                                    <?php if ($telegram) { ?>
                                        <a href="<?php echo $telegram; ?>" target="_blank"><i class="fab fa-telegram"></i></a>
                                    <?php } ?>
                                </li>
								 <li>
                                    <?php if ($linkedin) { ?>
                                        <a href="<?php echo $linkedin; ?>" target="_blank"><i class="fab fa-linkedin"></i></a>
                                    <?php } ?>
                                </li>
                            </ul>
                        </div>
                        <div class="flex-grow-2 qr-code-wrap ms-3">
                            <img src="<?php echo $qr_code ; ?>" class="qr-code" alt="QR CODE">
                        </div>
                    </div>
                </div>
                <div class="copyright-container">
                    <p class="all-right">
                        ALL RIGHT RESERVED TO OLIVE BRIGHT CONSULTING FIRM
                    </p>
                    <p class="copyright-wrap">
                        <a href="//www.b360mm.com/" target="_blank" title="B360 Website Development Service in Yangon, Myanmar">
                        Developed by <span class="service-owner"> B360</span>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </footer>
<?php wp_footer(); ?>
</body>
</html>