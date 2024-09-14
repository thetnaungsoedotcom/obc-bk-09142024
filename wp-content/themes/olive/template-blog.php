<?php 
// Template Name: Blog Page 
get_header(); 
get_template_part('partials/inner-banner');
$pg_fields = get_fields(); 
?>
<section class="investment section-wrap">
    <div class="container">
         <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="main">
                    <?php if ( $pg_fields['page_title'] != null ) { ?>
                        <h1 class="page-title"><?php echo $pg_fields['page_title']; ?></h1>
                    <?php } ?>
                    <?php if ( $pg_fields['page_description'] != null ) { ?>
                        <article class="main-content"><?php echo apply_filters('the_content', $pg_fields['page_description']); ?></article>
                    <?php } ?>
                </div>
            </div>
        </div>
       
        <?php 
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $args = array(
            'post_type'   => 'post',
            'posts_per_page' => 6,
            'orderby' => 'ASC',
            'paged' =>  $paged
        );
            
        $wp_query = new WP_Query($args);
        if ( have_posts() ) { 
        ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php
                $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
                while ($wp_query->have_posts()) { $wp_query->the_post();
                    $id = $post->ID;
                    $blog_title = $post->post_title;
                    $blog_contents = $post->post_content;
                    $blog_content = wp_trim_words($blog_contents, 40);
                    $mobile_blog_content = wp_trim_words($blog_contents, 15);
                    $thumb = get_post_thumbnail_id($post->ID);
                    $blog_images = wp_get_attachment_image_src($thumb, 'full');
                    $blog_image = aq_resize($blog_images[0], 300, 200, true, true, true);
                    $blog_link = get_permalink($id);
                    $blog_excerpt = get_the_excerpt($id);
                    $blog_excerpt = $post->excerpt_content;
                    $blog_date = get_the_date('j F, Y');
                ?>
                    <div class="col">
                        <div class="card h-100">
                            <div class="overflow-img">
                                    <a href="<?php echo  $blog_link; ?>" title="<?php echo $blog_title; ?>"><img src="<?php echo $blog_image ; ?>" class="img-fluid card-img-top invest-img" alt="<?php echo $blog_title  ;?>"></a>
                            </div>
                            <div class="card-body">
                                <a href="<?php echo  $blog_link; ?>" title="<?php echo $blog_title; ?>"><h5 class="card-title"><?php echo $blog_title; ?></h5></a>
                                <article class="card-text"><?php echo apply_filters('the_content', $blog_content); ?></article>
                                <span><a href="<?php echo $blog_link; ?>" class="btn btn-primary">Read More<i class="fas fa-arrow-right"></a></i></span>
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
</section>
<?php get_footer(); ?>