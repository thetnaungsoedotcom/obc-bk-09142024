<?php 
// Template Name: Gallery Page 
get_header(); 
get_template_part('partials/inner-banner');
$galleries = get_field('gallery');
?>
<section class="gallery-page section-wrap">
   <div class="container">
      <div class="gal">
         <?php 
         foreach ($galleries as $ikey => $g_image) { 
            if ($g_image['url']) {
         ?>
            <div class="gallery-box">
               <a data-fancybox="gallery" data-src="<?php echo $g_image['url']; ?>" title="<?php echo $g_image['caption']; ?>">
                  <img src="<?php echo $g_image['url']; ?>" alt="<?php echo $g_image['caption']; ?>" />
               </a>
               <div class="overlay"><?php echo $g_image['caption']; ?></div>
            </div>
          <?php } } ?>
      </div>
   </div>
</section>
<?php get_footer(); ?>