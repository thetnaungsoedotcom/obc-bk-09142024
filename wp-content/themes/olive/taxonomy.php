<?php get_header(); ?>
    <main>
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <?php if ($post->post_title) { ?>
                        <h2 class="post-main-title my-5"><?php echo $post->post_title; ?></h2>
                    <?php } ?>
                    <?php if ($post->post_content) { ?>
                        <article class="main-article">
                            <?php echo apply_filters('the_content', $post->post_content); ?>
                        </article>
                    <?php } ?>
                </div>
            </div>
        </div>
    </main>
<?php get_footer(); ?>