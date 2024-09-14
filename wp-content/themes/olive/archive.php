<?php 
get_header(); 
$current_lang = apply_filters( 'wpml_current_language', NULL ); // checking for current language
// $qry_obj = get_queried_object();
get_template_part('partials/inner-banner'); 
$fields = get_fields(); 

if ($current_lang == "en") {
    if (is_post_type_archive(OLIVE_SERVICE_PT)) {
        $pg_info = get_post(OLIVE_SERVICE_PG);
        $pg_fields = get_fields($pg_info->ID);
    }
    elseif (is_post_type_archive(OLIVE_CSR_PT)) {
        $pg_info = get_post(OLIVE_CSR_PG);
        $pg_fields = get_fields($pg_info->ID);
    }
    elseif (is_post_type_archive(OLIVE_NEWS_PT)) {
        $pg_info = get_post(OLIVE_NEWS_PG);
        $pg_fields = get_fields($pg_info->ID);
    }
    elseif (is_post_type_archive(OLIVE_BLOG_PT)) {
        $pg_info = get_post(OLIVE_BLOG_PG);
        $pg_fields = get_fields($pg_info->ID);
    }
    elseif (is_post_type_archive(OLIVE_GALLERY_PT)) {
        $pg_info = get_post(OLIVE_GALLERY_PG);
        $pg_fields = get_fields($pg_info->ID);
    }
    elseif (is_post_type_archive(OLIVE_TESTIMONIAL_PT)) {
        $pg_info = get_post(OLIVE_TESTIMONIAL_PG);
        $pg_fields = get_fields($pg_info->ID);
    }
    elseif (is_post_type_archive(OLIVE_INDUSTRIAL_PT)) {
        $pg_info = get_post(OLIVE_INDUSTRIAL_PG);
        $pg_fields = get_fields($pg_info->ID);
    }
} else {
    if (is_post_type_archive(OLIVE_SERVICE_PT)) {
        $pg_info = get_post(OLIVE_SERVICE_CN_PG);
        $pg_fields = get_fields($pg_info->ID);
    }
    elseif (is_post_type_archive(OLIVE_CSR_PT)) {
        $pg_info = get_post(OLIVE_CSR_CN_PG);
        $pg_fields = get_fields($pg_info->ID);
    }
    elseif (is_post_type_archive(OLIVE_NEWS_PT)) {
        $pg_info = get_post(OLIVE_NEWS_CN_PG);
        $pg_fields = get_fields($pg_info->ID);
    }
    elseif (is_post_type_archive(OLIVE_BLOG_PT)) {
        $pg_info = get_post(OLIVE_BLOG_CN_PG);
        $pg_fields = get_fields($pg_info->ID);
    }
    elseif (is_post_type_archive(OLIVE_GALLERY_PT)) {
        $pg_info = get_post(OLIVE_GALLERY_CN_PG);
        $pg_fields = get_fields($pg_info->ID);
    }
    elseif (is_post_type_archive(OLIVE_TESTIMONIAL_PT)) {
        $pg_info = get_post(OLIVE_TESTIMONIAL_CN_PG);
        $pg_fields = get_fields($pg_info->ID);
    }
    elseif (is_post_type_archive(OLIVE_INDUSTRIAL_PT)) {
        $pg_info = get_post(OLIVE_INDUSTRIAL_CN_PG);
        $pg_fields = get_fields($pg_info->ID);
    }
}
?>
<section class="investment section-wrap">
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
                        $invest_title = $post->post_title;
                        $invest_content  = $post->post_content;
                        $thumb = get_post_thumbnail_id($post->ID);
                        $invest_images = wp_get_attachment_image_src($thumb, 'full');
                        $invest_image = aq_resize($invest_images[0], 480, 343, true, true, true ,true);
                        $invest_link = get_permalink($post->ID);
                        $short_excerpt = get_the_excerpt($post->ID);
                ?>
                        <div class="col">
                            <div class="card h-100">
                                <div class="overflow-img">
                                    <a href="<?php echo  $invest_link; ?>"><img src="<?php echo $invest_image ; ?>" class="card-img-top invest-img" alt="<?php echo $invest_title  ;?>"></a>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="<?php echo $invest_link; ?>">
                                            <?php echo $invest_title  ;?>
                                        </a>
                                    </h5>
                                    <p class="card-text"><?php echo $short_excerpt  ;?></p>
                                    <span><a href=" <?php echo  $invest_link; ?>" class="btn btn-primary">Read More<i class="fas fa-arrow-right"></a></i></span>
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