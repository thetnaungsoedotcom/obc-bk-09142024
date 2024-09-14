<div class="sidebar-box-wrap">
    <h3 class="sidebar-title"><?php echo _e("Industrial Park"); ?></h3>
    <?php 
    $industrial_args = array(
        'post_type' => OLIVE_INDUSTRIAL_PT,
        'posts_per_page' => 5,
        'post_status' => 'publish',
        'exclude' => $post->ID,
            'suppress_filters'  => false,
        'orderby' => 'date',
        'order' => 'DESC'
    );

    $related_industrials = get_posts($industrial_args);       

    if ($related_industrials) {
    ?>
        <ul>
            <?php 
            foreach ($related_industrials as $related_industrial) { 
                $related_industrial_img = get_the_post_thumbnail_url($related_industrial->ID);
                $related_industrial_img = aq_resize($related_industrial_img, 80, 80, TRUE, TRUE, TRUE);  
            ?>
                <li>
                    
                    <div class="d-flex align-items-center sidebar-excerpt">
                        <?php if ($related_industrial_img) { ?>
                            <div class="flex-shrink-0">
                                <img src="<?php echo WP_HOME.$related_industrial_img; ?>" class="img-fluid" alt="<?php echo $related_industrial->post_title; ?>">
                            </div>
                        <?php } ?>
                        <?php if ($related_industrial->post_title) { ?>
                            <div class="flex-grow-1 ms-3">
                                <h4>
                                    <a href="<?php echo get_permalink($related_industrial->ID); ?>" title="<?php echo $related_industrial->post_title; ?>">
                                        <?php echo $related_industrial->post_title; ?>
                                    </a>
                                </h4>
                            </div>
                        <?php } ?>
                    </div>
                </li>
            <?php } ?>
        </ul>
    <?php } ?>
</div>

<div class="sidebar-box-wrap">
    <h3 class="sidebar-title"><?php echo _e("Townships"); ?></h3>
    <?php 
    $location_terms = get_terms([
        'taxonomy' => OLIVE_LOCATION_TAXO,
        'hide_empty' => false,
            'suppress_filters'  => false
    ]);     

    if ($location_terms) { ?>
        <ul>
            <?php 
            foreach ($location_terms as $location_term) { 
                $term_by_id = get_term( $location_term->term_id, OLIVE_LOCATION_TAXO );
            ?>
                <li>
                     
                    <div class="d-flex align-items-center sidebar-excerpt">
                            <div class="flex-shrink-0">
                            <a href="<?php echo get_term_link($location_term); ?>" title="<?php echo $location_term->name; ?>">
                            <i class="fa-solid fa-angle-right"></i><?php echo $location_term->name; ?></a>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <span><?php echo "( ".$term_by_id->count." )"; ?></span>
                            </div>
                        </div>
                   
                </li>
            <?php } ?>
        </ul>
    <?php } ?>
</div>

<?php if ( ! is_singular(OLIVE_INDUSTRIAL_PT) ) { ?>
    <div class="sidebar-box-wrap">
        <h3 class="sidebar-title"><?php echo _e("Latest News"); ?></h3>
        <?php 
        $news_args = array(
            'post_type' => OLIVE_NEWS_PT,
            'posts_per_page' => 4,
            'post_status' => 'publish',
            'exclude' => $post->ID,
            'orderby' => 'date',
                'suppress_filters'  => false,
            'order' => 'DESC'
        );

        $related_news = get_posts($news_args);       

        if ($related_news) {
        ?>
            <ul>
                <?php 
                foreach ($related_news as $related_new) { 
                    $related_new_img = get_the_post_thumbnail_url($related_new->ID);
                    $related_new_img = aq_resize($related_new_img, 80, 80, TRUE, TRUE, TRUE);  
                ?>
                    <li>
                        
                        <div class="d-flex align-items-center sidebar-excerpt">
                            <div class="flex-shrink-0">
                                <img src="<?php echo WP_HOME.$related_new_img; ?>" class="img-fluid" alt="<?php echo $related_new->post_title; ?>">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4>
                                    <a href="<?php echo get_permalink($related_new->ID); ?>" title="<?php echo $related_new->post_title; ?>">
                                        <?php echo $related_new->post_title; ?>
                                    </a>
                                </h4>
                            </div>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
    </div>

    <div class="sidebar-box-wrap">
        <h3 class="sidebar-title"><?php echo _e("Latest Blogs"); ?></h3>
        <?php 
        $blog_args = array(
            'post_type' => OLIVE_BLOG_PT,
            'posts_per_page' => 4,
            'post_status' => 'publish',
            'exclude' => $post->ID,
            'orderby' => 'date',
                'suppress_filters'  => false,
            'order' => 'DESC'
        );

        $related_blogs = get_posts($blog_args);       

        if ($related_blogs) {
        ?>
            <ul>
                <?php 
                foreach ($related_blogs as $related_blog) { 
                    $related_blog_img = get_the_post_thumbnail_url($related_blog->ID);
                    $related_blog_img = aq_resize($related_blog_img, 80, 80, TRUE, TRUE, TRUE);  
                ?>
                    <li>
                        
                        <div class="d-flex align-items-center sidebar-excerpt">
                            <div class="flex-shrink-0">
                                <img src="<?php echo WP_HOME.$related_blog_img; ?>" class="img-fluid" alt="<?php echo $related_blog->post_title; ?>">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4>
                                    <a href="<?php echo get_permalink($related_blog->ID); ?>" title="<?php echo $related_blog->post_title; ?>">
                                        <?php echo $related_blog->post_title; ?>
                                    </a>
                                </h4>
                            </div>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
    </div>

    <div class="sidebar-box-wrap">
        <h3 class="sidebar-title"><?php echo _e("Latest CSR"); ?></h3>
        <?php 
        $csr_args = array(
            'post_type' => OLIVE_BLOG_PT,
            'posts_per_page' => 4,
            'post_status' => 'publish',
            'exclude' => $post->ID,
                'suppress_filters'  => false,
            'orderby' => 'date',
            'order' => 'DESC'
        );

        $related_csrs = get_posts($csr_args);       

        if ($related_csrs) {
        ?>
            <ul>
                <?php 
                foreach ($related_csrs as $related_csr) { 
                    $related_csr_img = get_the_post_thumbnail_url($related_csr->ID);
                    $related_csr_img = aq_resize($related_csr_img, 80, 80, TRUE, TRUE, TRUE);  
                ?>
                    <li>
                        
                        <div class="d-flex align-items-center sidebar-excerpt">
                            <div class="flex-shrink-0">
                                <img src="<?php echo WP_HOME.$related_csr_img; ?>" class="img-fluid" alt="<?php echo $related_csr->post_title; ?>">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4>
                                    <a href="<?php echo get_permalink($related_csr->ID); ?>" title="<?php echo $related_csr->post_title; ?>">
                                        <?php echo $related_csr->post_title; ?>
                                    </a>
                                </h4>
                            </div>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
    </div>
<?php } ?>