<?php get_header(); ?>
<?php
$home_field = get_fields();
$guide_box = $home_field['guide_box'];
$quality_box = $home_field['quality_box'];
$member_slider = $home_field['member_slider'];
$news = $home_field['news'];
$portfolio_slider = $home_field['portfolio_slider'];
$home_banners = $home_field['home_page_banner'];
?>

<!-- Home Banner -->
<!--
<section class="banner">
   <div class="container">
      <div class="row banner-row">
         <div class="col-lg-6 offset-lg-6">
            <div class="banner-image">
               <div class="banner-youtube-icon">
                  <?php //if ( $home_banners ) { ?>
                     <section class="banner-slide-wrap">
                           Additional required wrapper 
                           <div id="carouselFadeHome" class="carousel slide carousel-fade" data-bs-ride="carousel">
                              <div class="carousel-indicators">
                                 <button type="button" data-bs-target="#carouselFadeHome" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                 <button type="button" data-bs-target="#carouselFadeHome" data-bs-slide-to="1" aria-label="Slide 2"></button>
                                 <button type="button" data-bs-target="#carouselFadeHome" data-bs-slide-to="2" aria-label="Slide 3"></button>
                              </div>
                              <div class="carousel-inner">
                                 <?php 
                                 //foreach ($home_banners  as $slider_key => $home_banner ) {
                                    //if ($home_banner['url']) {
                                 ?>
                                    <div class="carousel-item <?php //echo ($slider_key == 0) ? 'active' : ''; ?>">
                                       <img src="<?php //echo $home_banner['url']; ?>" class="d-block w-100" alt="<?php //echo $home_banner['caption']; ?>">
                                    </div>
                                 <?php// } } ?>
                              </div>
                           </div>
                     </section>
                  <?php //} ?>
                  <?php //if ($home_field['youtube_icon']) { ?>
                    <div class="youtube-icon">
                        <img src="<?php //echo $home_field['youtube_icon'] ; ?>" class="youtube-icon" alt="<?php //bloginfo('name'); ?>"> 
                     </div> -->
                  <?php //} ?>
               </div>
            </div>
         </div>
         <?php// if ($home_field['banner_title']) { ?>
            <!-- <div class="col-12">
               <div class="banner-text-overlay">
                  <h1 class="banner-title txt-left"><?php// echo nl2br($home_field['banner_title']); ?></h1>
               </div>
            </div> -->
         <?php //} ?>
      <!-- </div>
   </div>
</section> -->
<!--
 |---------------------------------------------------------
 | Slide
 |---------------------------------------------------------
 |
-->
<section class="slider"><?php echo do_shortcode($home_field['slider_shortcode']); ?></section>


<!-- Guide Section -->
<section class="section-wrap guide">
   <div class="container">

      <?php if ($home_field['main_guide_title'] != null) { ?>
         <h3 class="main-guide-title"><?php echo nl2br($home_field['main_guide_title']); ?></h3>
      <?php } ?>

      <?php if ($home_field['guide_box']) { ?>
         <div class="row">
            <?php 
            foreach ($home_field['guide_box'] as $guide) {
               if ($guide['guide_content'] != null) {
            ?>
                  <div class="col-md-4">
                     <div class="card guide-box">
                        <div class="card-body">
                           <p class="card-text"><?php echo nl2br($guide['guide_content']); ?></p>
                           <a href="<?php echo $guide_detail_link; ?>" title="<?php echo $home_field['main_guide_title'] ;?>" class="btn btn-primary">MORE</a>
                        </div>
                     </div>
                  </div>
            <?php } } ?>
         </div>
      <?php } ?>
   </div>
</section>

<!-- Quality Section -->
<?php if ($home_field['quality_box']) { ?>
   <section class="section-wrap quality pd-bottom">
      <div class="container">
         <div class="row row-cols-md-3 row-cols-sm-2 row-cols-2 g-0">
            <?php 
            foreach ($home_field['quality_box'] as $q_key => $quality) {
               $quality_box_image = aq_resize($quality['quality_box_image'], 480, 480, true, true, true ,true);
            ?>
               <div class="col">
                  <div class="quality-box">
                        <?php if ($quality_box_image) { ?>
                           <img src="<?php echo $quality_box_image; ?>" class="quality-img" alt="<?php echo $quality['quality_box_title']; ?>">
                        <?php } ?>
                        <?php if ($quality['quality_box_title']) { ?>
                           <p class="quality-title"><?php echo nl2br($quality['quality_box_title']); ?></p>
                        <?php } ?>
                        <?php if ($quality['quality_count']) { ?>
                           <div class="quality-count"><span><?php echo $quality['quality_count']; ?></span></div>
                        <?php } ?>
                     <div class="opacity"></div>
                  </div>
               </div>
            <?php } ?>
         </div>
      </div>
   </section>
<?php } ?>

<!-- Work Section -->
<section class="work">
   <?php if ($home_field['work_image']) { ?>
      <div class="row work-row">
         <div class="col-lg-6 work-img" style="background: url(<?php echo $home_field['work_image']; ?>) no-repeat center center; background-size: cover;"></div>
      </div>
   <?php } ?>

   <div class="container work-info-wrap">
      <div class="row">
         <div class="col-lg-6"></div>
         <div class="col-lg-6">
            <div class="work-box-wrap">
               <div class="work-box">
                  <?php if ($home_field['work_main_title']) { ?>
                     <h2 class="section-title work-title"><?php echo $home_field['work_main_title']; ?></h2>
                  <?php } ?>
                  <?php if ($home_field['work_content']) { ?>
                     <p class="work-content"><?php echo nl2br($home_field['work_content']); ?></p>
                  <?php } ?>
                  <a title="<?php echo $home_field['work_main_title']; ;?>" href="#" class="btn btn-primary">MORE</a>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Chirs Section -->
<!-- <section class="chirs" style="background: url(<?php //echo $home_field['chirs_image']; ?>) no-repeat fixed center center; background-size: cover;">
   <div class="container chirs-container">
      <?php //if ($home_field['chirs_main_tilte']) { ?>
         <div class="chirs-content">
            <h1 class="banner-title txt-left chirs-title"><?php //echo $home_field['chirs_main_tilte']; ?></h1>
         </div>
      <?php// } ?>
   </div>
</section> -->

<!-- Team Member Section -->
<!-- <?php //if ($member_slider) { ?> 
   <section class="section-wrap team-member pd-top">
      <div class="container">
         <div class="swiper team-slider">
            <div class="swiper-wrapper">
               <?php 
              // foreach ($member_slider as $slider) {
                  //$member_image = aq_resize($slider['member_image'], 320, 320,true,true);
               ?>
                  <div class="swiper-slide">
                     <div class="card member-box h-100">
                        <?php// if ($member_image) { ?>
                           <figure>
                              <img class="member-img" src="<?php// echo $member_image; ?>" alt="<?php //echo $slider['member_name']; ?>">
                           </figure>
                        <?php //} ?>
                        <div class="card-body inner-box">
                           <?php //if ($slider['member_name']) { ?>
                              <h4 class="member-name"><?php //echo $slider['member_name']; ?></h4>
                           <?php //} ?>
                           <?php //if ($slider['member_position']) { ?>
                              <h6 class="member-position"><?php// echo $slider['member_position']; ?></h6>
                           <?php// } ?>
                           <a href="#" class="btn btn-primary" title="<?php// echo $slider['member_name'] ;?>">MORE</a>
                        </div>
                     </div>
                  </div>
               <?php// } ?>
            </div>
            <div class="member-arrow">
               <div class="swiper-button-next team-next"><i class="fa-thin fa-arrow-right-long"></i></div>
               <div class="swiper-button-prev team-prev"><i class="fa-thin fa-arrow-left-long"></i></div>
            </div>
         </div>
      </div>
   </section>
<?php //} ?> -->

<!-- Principle Section -->
<section class="section-wrap principle">
   <div class="container">
      <h1 class="section-title principle-main-title"><?php echo _e("Our Work's Principle"); ?></h1>
      <div class="whole-circle whole-circle">
         <div class="col-lg-10 offset-lg-1">
            <div class="row d-flex align-items-center">
               <!-- <div class="col-md-6 col-sm-4 d-flex justify-content-end"> -->
               <div class="col-md-6 col-sm-4">
                  <div class="imgc-circle-right"><img class="circle" src="<?php echo ASSET_URL.'images/circle.png'; ?>"></div>
               </div>
               <div class="col-md-6 col-sm-8">
                  <div class="outer-circle">
                     <div class="right-circle">
                        <div class="d-flex align-items-center">
                           <div class="flex-shrink-0">
                              <span class="principle-no">01</span>
                           </div>
                           <div class="flex-grow-1 ms-3">
                              <h3 class="principle-title"><?php echo _e("The first principle"); ?></h3>
                           </div>
                        </div>
                        <?php if ($home_field['principle_content']) { ?>
                           <p class="principle-content"><?php echo $home_field['principle_content']; ?></p>
                        <?php } ?>
                        <a title="<?php echo  $member_name ;?>" href="#" class="btn btn-primary ">MORE</a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
<!-- Portfolio Section -->

<!-- Slider main container -->
<?php if ($portfolio_slider) { ?>
   <section class="section-wrap portfolio-slider">
      <h1 class="section-title txt-center"><?php echo _e("Portfolio"); ?></h1>
      <div class="swiper-container portfolio">
         <!-- Additional required wrapper -->
         <div class="swiper-wrapper">
            <?php 
            foreach ($portfolio_slider as $slider_key => $port_slider) {
               $member_image = aq_resize($port_slider['portfolio_image'], 720, 465, true, true, true, true);
            ?>
               <div class="swiper-slide">
                  <div class="image">
                     <?php if ($member_image) { ?>
                        <img class="portfolio-img" src="<?php echo $member_image; ?>" alt="<?php echo $port_slider['portfolio_title']; ?>">
                     <?php } ?>
                     <div class="content">
                        <?php if ($port_slider['portfolio_title']) { ?>
                           <h3 class="portfolio-title"><?php echo $port_slider['portfolio_title']; ?></h3>
                        <?php } ?>
                        <?php if ($port_slider['portfolio_content']) { ?>
                           <p class="portfolio-content"><?php echo nl2br($port_slider['portfolio_content']); ?></p>
                        <?php } ?>
                     </div>
                  </div>
               </div>
               <?php $slider_key++; ?>
            <?php } ?>
         </div>
         <div class="swiper-pagination"></div>
      </div>
   </section>
<?php } ?>

<!-- News Section -->
<?php if ($news) { ?>
   <section class="section-wrap news">
      <div class="container">
         <h1 class="section-title news-main-title"><?php echo _e("News"); ?></h1>
         <div class="swiper news-slider">
            <div class="swiper-wrapper">
               <?php foreach ($news as $new_slider) {
                  $images = wp_get_attachment_image_src( get_post_thumbnail_id($new_slider->ID), 'medium' );  
                  $news_image = aq_resize($images[0], 480, 410, true, true, true);
                  $news_link = get_permalink($new_slider->ID);
               ?>
                  <div class="swiper-slide">
                     <div class="news-box">
                        <div class="card h-100">
                           <?php if ($news_image) { ?>
                              <a href="<?php echo $news_link; ?>" title="<?php echo $new_slider->post_title; ?>">
                                 <img src="<?php echo $news_image; ?>" class="card-img-top news-img" alt="<?php echo $new_slider->post_title; ?>">
                              </a>
                           <?php } ?>
                           <div class="card-body">
                              <?php if ($new_slider->post_title) { ?>
                                 <h3 class="card-text">
                                    <a href="<?php echo $news_link; ?>" title="<?php echo $new_slider->post_title; ?>"><?php echo $new_slider->post_title; ?></a>
                                 </h3>
                              <?php } ?>
                              <a href="<?php echo $news_link; ?>" class="news-btn"><i class="fa-light fa-chevron-down"></i></a>
                           </div>
                        </div>
                     </div>
                  </div>
               <?php } ?>
            </div>
            <!-- <div class="swiper-button-next news-next"></div>
            <div class="swiper-button-prev news-prev"></div> -->
         </div>
      </div>
   </section>
<?php } ?>
<?php get_footer(); ?>