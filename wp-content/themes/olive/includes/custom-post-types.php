<?php
add_action( 'init', 'cp_change_post_object' );
add_action( 'init', 'create_post_type' );
add_action( 'init', 'create_tax' );
/*
|-----------------------------------------------------------------------------------
| Add new post type
|-----------------------------------------------------------------------------------
|
*/
function create_post_type(){

	register_post_type('news',
		array(
			'labels' => array(
				'name' => __('News'),
				'singular_name' => __('News')
			),
			'public' => true,
			'has_archive' => true,
			'show_in_rest'=> true,
			// 'menu_icon' =>'',
			'rewrite' => array('slug' => 'news'),
			'supports' => array('title', 'editor', 'custom-fields', 'excerpt', 'thumbnail')
		)
	);
	register_post_type('csr',
		array(
			'labels' => array(
				'name' => __('CSR'),
				'singular_name' => __('CSR')
			),
			'public' => true,
			'has_archive' => true,
			'show_in_rest'=> true,
			// 'menu_icon' =>'',
			'rewrite' => array('slug' => 'csr'),
			'supports' => array('title', 'editor', 'custom-fields', 'excerpt', 'thumbnail')
		)
	);
	// register_post_type('investment',
	// 	array(
	// 		'labels' => array(
	// 			'name' => __('Investment Incentives'),
	// 			'singular_name' => __('Investment Incentive')
	// 		),
	// 		'public' => true,
	// 		'has_archive' => true,
	// 		'show_in_rest'=> true,
	// 		// 'menu_icon' =>'',
	// 		'rewrite' => array('slug' => 'investment'),
	// 		'supports' => array('title', 'editor', 'custom-fields', 'excerpt', 'thumbnail')
	// 	)
	// );
	register_post_type('gallery',
		array(
			'labels' => array(
				'name' => __('Gallery'),
				'singular_name' => __('Gallery')
			),
			'public' => true,
			'has_archive' => true,
			'show_in_rest'=> true,
			// 'menu_icon' =>'',
			'rewrite' => array('slug' => 'gallery'),
			'supports' => array('title', 'editor', 'custom-fields', 'excerpt', 'thumbnail')
		)
	);
	register_post_type('services',
		array(
			'labels' => array(
				'name' => __('Services'),
				'singular_name' => __('Servcies')
			),
			'public' => true,
			'has_archive' => true,
			'show_in_rest'=> true,
			// 'menu_icon' =>'',
			'rewrite' => array('slug' => 'services'),
			'supports' => array('title', 'editor', 'custom-fields', 'excerpt', 'thumbnail')
		)
	);
	register_post_type('testimonials',
		array(
			'labels' => array(
				'name' => __('Testimonials'),
				'singular_name' => __('Testimonials')
			),
			'public' => true,
			'has_archive' => true,
			'show_in_rest'=> true,
			// 'menu_icon' =>'',
			'rewrite' => array('slug' => 'testimonials'),
			'supports' => array('title', 'editor', 'custom-fields', 'excerpt', 'thumbnail')
		)
	);
	register_post_type('industrial-park',
		array(
			'labels' => array(
				'name' => __('Industrial Parks'),
				'singular_name' => __('Industrial Park')
			),
			'public' => true,
			'has_archive' => true,
			'show_in_rest'=> true,
			// 'menu_icon' =>'',
			'rewrite' => array('slug' => 'industrial-park'),
			'supports' => array('title', 'editor', 'custom-fields', 'excerpt', 'thumbnail')
		)
	);
	register_post_type('advantage',
		array(
			'labels' => array(
				'name' => __('Advantages'),
				'singular_name' => __('Advantage')
			),
			'public' => true,
			'has_archive' => true,
			'show_in_rest'=> true,
			// 'menu_icon' =>'',
			'rewrite' => array('slug' => 'advantage'),
			'supports' => array('title', 'editor', 'custom-fields', 'excerpt', 'thumbnail')
		)
	);
}

/*
|-----------------------------------------------------------------------------------
| Add new category
|-----------------------------------------------------------------------------------
|
*/
function create_tax(){

	register_taxonomy('location', 'industrial-park', array(
		'label' =>__('location'),
        'rewrite'      => array('slug' => 'location'),
		'hierarchical' => true,
		'show_in_rest'=> true,
	));
	
	// register_taxonomy('menu-type', 'menu', array(
	// 	'label' =>__('menu-type'),
    //     'rewrite'      => array('slug' => 'menu-type'),
	// 	'hierarchical' => true,
	// 	'show_in_rest'=> true,
	// ));

	// register_taxonomy('events-type', 'events', array(
	// 	'label' =>__('events-type'),
    //     'rewrite'      => array('slug' => 'events-type'),
	// 	'hierarchical' => true,
	// 	'show_in_rest'=> true,
	// ));
}

/*
|-----------------------------------------------------------------------------------
| Change dashboard Posts to News
|-----------------------------------------------------------------------------------
|
*/
function cp_change_post_object() {
    $get_post_type = get_post_type_object('post');
    $labels = $get_post_type->labels;
        $labels->name = 'Blogs';
        $labels->singular_name = 'Blog';
        $labels->add_new = 'Add Blog';
        $labels->add_new_item = 'Add Blog';
        $labels->edit_item = 'Edit Blog';
        $labels->new_item = 'Blog';
        $labels->view_item = 'View Blog';
        $labels->search_items = 'Search Blogs';
        $labels->not_found = 'No Blogs found';
        $labels->not_found_in_trash = 'No Blogs found in Trash';
        $labels->all_items = 'All Blogs';
        $labels->menu_name = 'Blogs';
        $labels->name_admin_bar = 'Blogs';
}
