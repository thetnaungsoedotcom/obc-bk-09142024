<?php 
get_header(); 
$current_lang = apply_filters( 'wpml_current_language', NULL ); // checking for current language
get_template_part('partials/inner-banner'); 
$services_field = get_fields(); 

if ($current_lang == "en") {
    if (is_post_type_archive(OLIVE_SERVICE_PT)) {
        $pg_info = get_post(OLIVE_SERVICE_PG);
        $pg_fields = get_fields($pg_info->ID);
    }
} else {
    if (is_post_type_archive(OLIVE_SERVICE_PT)) {
        $pg_info = get_post(OLIVE_SERVICE_CN_PG);
        $pg_fields = get_fields($pg_info->ID);
    }
}
?>
<section class="services section-wrap">
    <div class="container">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="main">
                    <h1 class="page-title"><?php echo $pg_fields['page_title']; ?></h1>
                    <article class="main-content"><?php echo apply_filters('the_content', $pg_fields['page_description']); ?></article>
                </div>
            </div>
        </div>
       
        <?php 
         if ( have_posts() ) { ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php
                $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
                    while ( have_posts() ) { the_post();
                        $id = $post->ID;
                        $services_title = $post->post_title;
                        $services_content  = $post->post_content;
                        $thumb = get_post_thumbnail_id($id);
                        $services_images = wp_get_attachment_image_src($thumb, 'full');
                        $services_image = aq_resize($services_images[0], 65, 65, true, true, true ,true);
                        $services_link = get_permalink($id);
                        $excerpt=get_the_excerpt($id);
                        $short_excerpt = wp_trim_words($excerpt, 15);
                ?>
                    <div class="col">
                        <div class="card h-100">
                            <a href="<?php echo  $services_link; ?>"><img src="<?php echo $services_image ; ?>" class="services-img" alt="<?php echo $services_title  ;?>"></a>
                            <div class="card-body">
                               <a href="<?php echo  $services_link; ?>"><h3 class="card-title"><?php echo $services_title  ;?></h3></a>
                                <p class="card-text"><?php echo $short_excerpt  ;?></p>
                                <span><a href=" <?php echo  $services_link; ?>" class="btn btn-primary">Read More<i class="fas fa-arrow-right"></a></i></span>
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