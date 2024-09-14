<?php 
get_header(); 
get_template_part('partials/inner-banner');
?>
<main class="single-tpl section-wrap">
   <div class="container">
      <div class="row">

         <div class="col-lg-8">
            <div class="single-content-wrap">
               <?php if ($single_feature_img[0]) { ?>
                  <img src="<?php echo $single_feature_img[0]; ?>" class="w-100 single-feature-image" alt="<?php echo $post->post_title; ?>">
               <?php } ?>
               <div class="single-body">
                  <h1 class="single-main-title page-title"><?php echo $post->post_title; ?></h1>
                  <?php if ($post->post_content) { ?>
                     <article class="main-content-wrap"><?php echo apply_filters('the_content', $post->post_content); ?></article>
                  <?php } ?>
               </div>
            </div>
         </div>

         <aside class="col-lg-4 sidebar-wrap"><?php get_sidebar(); ?></aside> 
      </div>
   </div>
</main>
<?php get_footer(); ?>