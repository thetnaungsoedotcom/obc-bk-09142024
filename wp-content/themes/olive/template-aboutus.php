<?php 
// Template Name: About Page 
get_header(); 
$about_field = get_fields();
$choose_us_list = $about_field['choose_us_list'];
$member_slider  = $about_field['member_slider'];
$certificate_gallery = $about_field['certificate_gallery'];

get_template_part('partials/inner-banner'); 
?>

<section class="who-we-are section-wrap">
   <div class="container">
      <div class="row">
         <div class="col-sm-12 col-lg-6">
            <?php if ($about_field['who_we_are_main_title']) { ?>
               <h2 class="page-title we-are-title"><?php echo $about_field['who_we_are_main_title']; ?></h2>
            <?php } ?>
            <?php if ($about_field['who_we_are_content']) { ?>
               <p class="we-are-content"><?php echo nl2br($about_field['who_we_are_content']); ?></p>
            <?php } ?>
            <?php if ($about_field['who_we_are_list']) { ?>
               <div class="row">
                  <div class="col-sm-12">
                     <ul>
                        <?php foreach ($about_field['who_we_are_list'] as $lists) { ?>
                           <li class="we-list">
                              <i class="fa-solid fa-badge-check"></i><span class="list"> <?php echo $lists['who_we_are_list']; ?></span></i>
                           </li>
                        <?php } ?>
                     </ul>
                  </div>
               </div>
            <?php } ?>
         </div>
         <div class="col-sm-12 col-lg-6">
            <div class="image-box">
               <?php if ($about_field['who_we_are_image']) { ?>
                  <img class="who-we-are-img" src="<?php echo $about_field['who_we_are_image']; ?>" alt="<?php echo $who_we_are_title; ?>">
               <?php } ?>
               <div class="experience">
                  <?php if ($about_field['experienced_years']) { ?>
                     <h3 class="color-white"><?php echo $about_field['experienced_years']; ?></h3>
                  <?php } ?>
                  <?php if ($about_field['experienced_text']) { ?>
                     <h5 class="color-white"><?php echo $about_field['experienced_text']; ?></h5>
                  <?php } ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<section class="why-choose-us section-wrap">
   <div class="container">
      <h3 class="section-title main-title-center"><?php echo $about_field['choose_us_main_title']; ?></h3>
      <?php if($choose_us_list) { ?>
      <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4">
         <?php foreach ($choose_us_list as $choose_list) { 
            $choose_us_image = $choose_list['choose_us_image'];
            $choose_us_title = $choose_list['choose_us_title'];
            $choose_us_content = $choose_list['choose_us_content'];
            ?>
         <div class="col">
            <div class="choose-us-box">
               <figure>
                  <img class="choose-us-img" src="<?php echo  $choose_us_image; ?>" alt="<?php echo  $choose_us_title; ?>">
               </figure>
               <a class="choose-us-title" title ="<?php echo  $choose_us_title; ?>" href="<?php echo $choose_us_link ;?>">
                  <h4 class="choose-us-title"><?php echo  $choose_us_title; ?></h4>
               </a>
               <p><?php echo $choose_us_content;?></p>
               <!-- <a title ="<?php //echo  $service_title; ?>" href="<?php //echo $services_link; ?>" class="features-btn">Read More +</a> -->
            </div>
         </div>
         <?php }  ?>
      </div>
      <?php }  ?>
   </div>
</section>

<section class="ceo-message section-wrap">
   <div class="container">
      <h3 class="section-title main-title-center">CEO's Message</h3>
      <div class="row">
         <div class="col-lg-8 col-md-10 offset-lg-2 offset-md-1">
            <div class="author-thumb">
               <img class="ceo-image" src="<?php echo $about_field['ceo_image']; ?>" alt="<?php echo $about_field['ceo_name']; ?>">
            </div>
            <div class="content">
               <p>
                  <span class="quote-top">
                  <i class="fa-solid fa-quote-left"></i>
                  </span>
                  <?php echo $about_field['ceo_message']; ?>
                  <span class="quote-bottom">
                  <i class="fa-solid fa-quote-right"></i>
                  </span>
               </p>
            </div>
            <div class="author">
               <h4><?php echo $about_field['ceo_name']; ?></h4>
               <span><?php echo $about_field['ceo_position']; ?></span>
            </div>
         </div>
      </div>
   </div>
</section>

<section class="our-mission-vission pd-top">
   <div class="container">
      <div class="row">
         <div class="col-md-6 g-0"style="background: url(<?php echo $about_field['mission_image']; ?>) no-repeat center center; background-size: cover;"></div>
         <div class="col-md-6 g-0">
            <div class="mission-box">
               <h3 class="section-title"><?php echo $about_field['mission_title']; ?></h3>
               <p class="mission-content"><?php echo nl2br($about_field['mission_content']); ?></p>
            </div>
         </div>
      </div>
      <div class="row reverse">
         <div class="col-md-6 g-0">
            <div class="vision-box">
               <h3 class="section-title"><?php echo $about_field['vision_title']; ?></h3>
               <p class="vision-content"><?php echo nl2br($about_field['vision_content']); ?></p>
            </div>
         </div>
         <div class="col-md-6 g-0"style="background: url(<?php echo $about_field['vision_image']; ?>) no-repeat center center; background-size: cover;"></div>
      </div>
   </div>
</section>

<!-- <?php //if ($member_slider) { ?>
   <section class="section-wrap team-member pd-top">
      <div class="container">
         <div class="swiper team-slider">
            <div class="swiper-wrapper">
               <?php 
              // foreach ($member_slider as $slider) {
                 // $member_image = aq_resize($slider['member_image'], 320, 320,true,true);
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
                              <h6 class="member-position"><?php //echo $slider['member_position']; ?></h6>
                           <?php //} ?>
                           <a href="#" class="btn btn-primary" title="<?php //echo $slider['member_name'] ;?>">MORE</a>
                        </div>
                     </div>
                  </div>
               <?php //} ?>
            </div>
            <div class="member-arrow">
               <div class="swiper-button-next team-next"><i class="fa-thin fa-arrow-right-long"></i></div>
               <div class="swiper-button-prev team-prev"><i class="fa-thin fa-arrow-left-long"></i></div>
            </div>
         </div>
      </div>
   </section>
<?php// } ?> -->

<section class="certificate section-wrap">
   <div class="container">
      <div class="row">
         <div class="col-sm-12 col-lg-12">
            <h2 class="section-title main-title-center"><?php echo $about_field['certificate_title']; ?></h2>
         </div>
         <p class="certificate-content"><?php echo $about_field['certificate_content'];  ?></p>
      </div>
   </div>
   <div class="row">
      <?php if($certificate_gallery):?>
      <div class="col-lg-8 col-md-10 offset-lg-2 offset-md-1">
         <ul class="row">
            <?php foreach( $certificate_gallery as $image ): ?>
               <li class="col-4">
                  <a data-fancybox="c-img" data-src="<?php echo esc_url($image); ?>">
                     <img class="c-img" src="<?php echo esc_url($image); ?>" alt="<?php echo $about_field['certificate_title'];  ?>" />
                  </a>
               </li>
            <?php endforeach; ?>  
         </ul>
      </div>
      <?php endif; ?>
   </div>
   </div>
</section>
<?php get_footer(); ?>