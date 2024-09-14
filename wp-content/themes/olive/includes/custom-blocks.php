<?php
add_action('after_setup_theme','mytheme_setup');
add_action( 'acf/init', 'add_acf_custom_blocks');

/* Register for Gutenberg Wide Images */
function mytheme_setup() {
    add_theme_support('align-wide');
}

/* Register Custom Category Block for Gutenberg Editor */
function custom_block_category($categories, $post) {
    return array_merge(
        array(
            array(
                'slug' => 'custom-name-blocks',
                'title' => __('Client Website Name Blocks', 'custom-name-blocks'),
            ),
        ),
        $categories
    );
}
add_filter('block_categories', 'custom_block_category', 10, 2);

function add_acf_custom_blocks() {
    acf_register_block(array(
        'name' => 'media-text',
        'title' => 'Media & Text Block',
        'render_template' => 'blocks/media-text.php',
        'category' => 'custom-name-blocks',
        'icon' => 'admin-comments',
    ));  
}

?>
