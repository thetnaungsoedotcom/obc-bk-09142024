<?php get_header(); ?>
<?php get_template_part('partials/inner-banner'); ?>
<section class="services-detail section-wrap">
    <div class="container">
    <div class="row">
         <?php
            while ( have_posts() ) { the_post();
                $id = $post->ID;
                $services_title = $post->post_title;
                $services_content = $post->post_content;
                $thumb = get_post_thumbnail_id($id);
            //     $services_images = wp_get_attachment_image_src($thumb, 'full');
            //    $services_image = aq_resize($services_images[0], 500, 350, true, true, true, true);
                $services_date = get_the_date('j F, Y');
            ?>
           <div class="col-sm-12 col-md-12 col-lg-4 ">
            <div class="sidebar-box">
               <div class="sidebar">
                  <h3 class="sidebar-title"><?php echo _e("Latest Services"); ?></h3>
                  <?php 
                     $args = array(
                        'post_type'   => 'services',
                        'posts_per_page' => -1,
                        'orderby' => 'ASC'
                     );
                     
                     $services = get_posts( $args );
                     if ($services) {
                     ?>
                  <ul>
                     <?php foreach ($services as $service) { ?>
                     <li>
                        <i class="fas fa-angle-right">
                        <a href="<?php echo get_permalink($service->ID); ?>" title="<?php echo $service->post_title; ?>"><?php echo $service->post_title; ?></a></i>
                     </li>
                     <?php } ?>
                  </ul>
                  <?php } ?>
               </div>
            </div>
         </div>
         <div class="col-sm-12 col-lg-8">
            <div class="services-box">
               <div class="thumb">
                   <!-- <img src="<?php //echo $services_image; ?>" class="services-img" alt="<?php //echo $services_title ; ?>"> -->
                </div>
                
                <article class="main-content-wrap"><?php echo apply_filters('the_content', $services_content); ?></article>
                <!-- <h1><?php echo $services_title ; ?></h1> -->
            </div>
         </div>
         <?php } ?>
      
      </div>
   </div>

   </div>
</section>
<?php get_footer(); ?>