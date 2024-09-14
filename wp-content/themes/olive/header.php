<?php global $THEME_OPTIONS; ?>
<!doctype html>
<!--[if lt IE 7 ]>	<html lang="en" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>		<html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>		<html lang="en" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>		<html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html dir="ltr" lang="en"  class="no-js">
<!--<![endif]-->
<head>
<meta charset="UTF-8">
<title><?php wp_title(''); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
<?php if ( file_exists(TEMPLATEPATH .'/favicon.png') ) : ?>
<link rel="shortcut icon" href="<?php bloginfo('template_url'); ?>/favicon.png">
<?php endif; ?>
<!--[if lt IE 9]>
<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<!-- Pixel Code for https://klearmap.com/ -->
<script defer src="https://klearmap.com/pixel/TZUadhgH1KkoCIn4"></script>
<!-- END Pixel Code -->

<!-- Clarity -->
<script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "jm5g7fxd44");
</script>
<!-- Clarity -->

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TBJ8WXT6');</script>
<!-- End Google Tag Manager -->

<?php wp_head(); ?>
</head>
<?php $body_classes = join( ' ', get_body_class() ); ?>
<body class="<?php if( !is_search() )echo $body_classes; ?>">
    
    <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TBJ8WXT6"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<?php 
$current_lang = apply_filters( 'wpml_current_language', NULL ); // checking for current language
$site_info = get_field('general_setting','option');
$default_logo = $site_info['default_logo'];
if ($site_info['logo']) {
    $logo = $site_info['logo'];
} else {
    $logo = ASSET_URL.'images/logo.png';
}
?>

<?php if (! is_front_page()) { ?>
    <style>
        .hc-nav-trigger span, .hc-nav-trigger span::before, .hc-nav-trigger span::after {
            background: #cfcfcf;
        }
    </style>
<?php } ?>

<header class="site-header">
    <div class="container">
        <div class="row d-flex align-items-center">
            <div class="col-lg-2 col-sm-7">
                <?php 
                if ($current_lang == 'zh-hans') {
                    $home_link = WP_HOME.'/zh-hans';
                } else {
                    $home_link = WP_HOME;
                }
                if ($logo) { 
                ?>
                    <a href="<?php echo $home_link; ?>" title="<?php bloginfo('name'); ?>" class="logo-name">
                        <img src="<?php echo WP_HOME.$logo; ?>" class="img-fluid main-logo" alt="<?php bloginfo('name'); ?>">
                    </a>
                <?php } ?>
            </div>
            <div class="col-lg-10 col-sm-5">
                <nav class="stellarnav clearfix">
                    <?php  
                    wp_nav_menu(array(
                        'theme_location' => 'main',
                        'menu' => 'main-menu',
                        'depth' => 0,
                        'menu_class' => 'menu',
                        'container' => '',
                        'container_class' => ''
                    ));
                    ?>
                </nav>
            </div>
        </div>
    </div>
</header>

