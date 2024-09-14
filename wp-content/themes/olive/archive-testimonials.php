<?php get_header(); ?>
<?php get_template_part('partials/inner-banner'); ?>
<section class="testimonials-list section-wrap">
    <div class="container">
       
        <?php 
         if ( have_posts() ) { ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-5">
            <?php
                $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
                    while ( have_posts() ) { the_post();
                        $id = $post->ID;
                        $testimonials_name = $post->post_title;
                        $testimonials_content  = $post->post_content;
                        $thumb = get_post_thumbnail_id($id);
                        $testimonials_images = wp_get_attachment_image_src($thumb, 'full');
                        $testimonials_image = aq_resize($testimonials_images[0], 100, 100, true, true, true ,true);
                        $testimonials_link = get_permalink($id);
                        $excerpt=get_the_excerpt($id);
                        $short_excerpt = wp_trim_words($excerpt, 15);
                ?>
                    <div class="col">
                        <div class="card h-100 test-box">
                            <div class="d-flex align-items-center pd">
                                <div class="flex-shrink-0">
                                    <div class="test-img">
                                        <img src="<?php echo $testimonials_image; ?>" alt=" <?php echo $testimonails_name; ?>">
                                    </div>
                                </div>
                                <div class="flex-grow-0 ms-3">
                                    <?php echo $testimonials_name; ?>
                                </div>
                            </div>
                        
                            <div class="card-body">
                                <p>
                                    <?php echo $testimonials_content; ?>
                                   
                                </p>
                            </div>
                            <div class="quote"><span class="flaticon-quote"><i class="fa-solid fa-quote-right"></i></span></div>
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
