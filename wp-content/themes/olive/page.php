<?php get_header(); 
get_template_part('partials/inner-banner');
$short_field = get_fields($post->ID);
?> 

    <main class="single-investment">
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="single-content-wrap">
                    <?php if ($short_field['short_description']) { ?>
                        <p class="short-description"><?php echo $short_field['short_description']; ?></p>
                    <?php } ?>
                        <?php if ($post->post_title) { ?>
                            <!-- <h2 class="post-main-title my-5"><?php //echo $post->post_title; ?></h2> -->
                        <?php } ?>
                        <?php if ($post->post_content) { ?>
                            <article class="main-article">
                                <?php echo apply_filters('the_content', $post->post_content); ?>
                            </article>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?php get_footer(); ?>