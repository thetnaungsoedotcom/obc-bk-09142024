<?php
/*
|-------------------------------------------------------------------------------------------------------------------------------
| Add custom Theme Options
|-------------------------------------------------------------------------------------------------------------------------------
*/
if (function_exists('acf_add_options_page')) {

	acf_add_options_page(array(
		'page_title' => 'Site Information',
		'menu_title' => 'Site Info',
		'menu_slug' => 'site-information',
		'capability' => 'edit_posts',
		'redirect' => false,
		'position' => 2
	));
}

?>
