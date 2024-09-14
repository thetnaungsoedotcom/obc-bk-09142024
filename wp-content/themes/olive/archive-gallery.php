<?php get_header(); ?>
<?php get_template_part('partials/inner-banner'); ?>
<?php if (have_posts()) { ?>
    <div class="gallery-list section-wrap">
    <div class="container">
        <div class="my-masonry-grid">
            <?php  while ( have_posts() ) { the_post(); 
                $id = $post->ID;
                $gallery_title = $post->post_title;
                $image = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'full' );  
            ?> 
                <div class="my-masonry-grid-item">
                    <div class="card">
                        <div class="gallery-box">
                            <img src="<?php echo $image[0]; ?>" alt="<?php echo $t_title; ?>" class="w-100"> 
                            <div class="gallery-overlay">
                              <h6><?php echo $gallery_title; ?></h6>   
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    </div>
<?php } ?>
<?php get_footer(); ?>