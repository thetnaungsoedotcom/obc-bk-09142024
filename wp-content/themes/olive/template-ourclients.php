<?php 
// Template Name: Our Clients Page 
get_header(); 
get_template_part('partials/inner-banner'); 

$client_field = get_fields();
$clients_logos = $client_field['clients_logo'];
$testimonials = $client_field['testimonials'];
?>
<?php if ($clients_logos) { ?>
    <section class="clients-logo section-wrap">
        <div class="container">
            <h3 class="section-title"><?php echo _e("Our Client's Logo"); ?></h3>
            <div class="swiper logoSwiper">
                <div class="swiper-wrapper">
                    <?php foreach ($clients_logos as $clients_logo) {?>
                        <div class="swiper-slide">
                            <img class="logo-img" src="<?php echo WP_HOME.$clients_logo; ?>" alt="<?php bloginfo('name'); ?>">
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="swiper-pagination logo-pag"></div>
        </div>
    </section>
<?php } ?>

<?php if ($testimonials) { ?>
    <section class="testimonals section-wrap">
        <div class="container">
            <h3 class="section-title"><?php echo _e("Testimonals"); ?></h3>
            <div class="swiper TestSwiper">
                <div class="swiper-wrapper">
                    <?php 
                    foreach ($testimonials as $test) {
                        $testimonails_image = aq_resize($test['testimonails_image'], 80, 80, true, true, true,true);
                    ?>
                        <div class="swiper-slide">
                            <div class="card h-100 test-box">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <?php if ($testimonails_image) { ?>
                                            <div class="flex-shrink-0">
                                                <img src="<?php echo WP_HOME.$testimonails_image; ?>" alt="<?php echo $testimonails_name; ?>">
                                            </div>
                                        <?php } ?>
                                        <?php if ($test['testimonails_name']) { ?>
                                            <div class="flex-grow-1 ms-3">
                                                <h4><?php echo $test['testimonails_name']; ?></h4>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <?php if ($test['testimonails_content']) { ?>
                                        <p>
                                            <span class="quote-top">
                                                <i class="fa-solid fa-quote-left"></i>
                                            </span>
                                            <?php echo nl2br($test['testimonails_content']); ?>
                                            <span class="quote-bottom">
                                                <i class="fa-solid fa-quote-right"></i>
                                            </span>
                                        </p>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
<?php } ?>
<?php get_footer(); ?>