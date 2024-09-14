<?php 
get_header(); 
$current_lang = apply_filters( 'wpml_current_language', NULL ); // checking for current language
get_template_part('partials/inner-banner'); 
if ($current_lang == "en") {
    if (is_post_type_archive(OLIVE_ADVANTAGE_PT)) {
        $pg_info = get_post(OLIVE_ADVANTAGE_PG);
        $contact_field = get_fields($pg_info->ID);
    }
} else {
    if (is_post_type_archive(OLIVE_ADVANTAGE_PT)) {
        $pg_info = get_post(OLIVE_ADVANTAGE_CN_PG);
        $contact_field = get_fields($pg_info->ID);
    }
}
?>
<section class="advantage-list section-wrap">
    <div class="container">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="main">
                    <h1 class="page-title"><?php echo $contact_field['page_title']; ?></h1>
                    <article class="main-content"><?php echo apply_filters('the_content', $contact_field['page_description']); ?></article>
                </div>
            </div>
        </div>
        <?php 
         if ( have_posts() ) { ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-5">
                <?php
                    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
                    while ( have_posts() ) { the_post();
                        $post_title = $post->post_title;
                        $post_content  = $post->post_content;
                        $post_image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
                        $post_image = aq_resize($post_image[0], 350, 250, true, true, true ,true);
                        $post_link = get_permalink($post->ID);
                        $short_excerpt = $post->post_excerpt;
                ?>
                     <div class="col">
                        <div class="card h-100">
                            <a href="<?php echo  $post_link; ?>"><img src="<?php echo $post_image ; ?>" class="post-img" alt="<?php echo $post_title  ;?>"></a>
                            <div class="card-body">
                               <a href="<?php echo  $post_link; ?>"><h3 class="card-title"><?php echo $post_title  ;?></h3></a>
                                <p class="card-text"><?php echo $short_excerpt  ;?></p>
                                <span><a href=" <?php echo  $post_link; ?>" class="btn btn-primary">Read More<i class="fas fa-arrow-right"></a></i></span>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <div class="row">
         <div class="col-12">
            <nav aria-label="Page navigation example">
               <ul class="pagination justify-content-center">        
                  <?php pagination_widget(); ?>
               </ul>
            </nav>
         </div>
      </div>
            </div>
       <?php } ?>
  </div>
</section>
<?php get_footer(); ?>
