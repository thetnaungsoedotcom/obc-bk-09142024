<?php 
$current_lang = apply_filters( 'wpml_current_language', NULL ); // checking for current language
$general = get_field('general_setting', 'option'); 
$default_banner = $general['default_banner'];
$obj = get_queried_object();
$inner_banner = get_field('inner_banner', $obj->ID);
?>
<?php 
if ($current_lang == "en") {
    if (is_post_type_archive(OLIVE_CSR_PT)) { 
        $inner_banner = get_field('inner_banner', OLIVE_CSR_PG);    
    } 
    elseif (is_post_type_archive(OLIVE_NEWS_PT)) { 
        $inner_banner = get_field('inner_banner', OLIVE_NEWS_PG);    
    } 
    // elseif (is_post_type_archive(OLIVE_INVESTMENT_PT)) { 
    //     $inner_banner = get_field('inner_banner', OLIVE_INVESTMENT_PG);    
    // } 
    elseif (is_page(OLIVE_OUR_CLIENT_PG)) { 
        $inner_banner = get_field('inner_banner', OLIVE_OUR_CLIENT_PG);    
    } 
    elseif (is_page(OLIVE_GALLERY_PG)) { 
        $inner_banner = get_field('inner_banner', OLIVE_GALLERY_PG);    
    }
    elseif (is_post_type_archive(OLIVE_ADVANTAGE_PT)) { 
        $inner_banner = get_field('inner_banner', OLIVE_ADVANTAGE_PG);    
    }
    elseif (is_post_type_archive(OLIVE_INDUSTRIAL_PT)) { 
        $inner_banner = get_field('inner_banner', OLIVE_INDUSTRIAL_PG);    
    }
    elseif (is_tax(OLIVE_LOCATION_TAXO)) { 
        $inner_banner = get_field('inner_banner', OLIVE_INDUSTRIAL_PG);    
    }
    elseif (is_singular(OLIVE_INDUSTRIAL_PT)) { 
        $inner_banner = get_field('inner_banner', OLIVE_INDUSTRIAL_PG);    
    }
    elseif (is_singular(OLIVE_ADVANTAGE_PT)) { 
        $inner_banner = get_field('inner_banner', OLIVE_ADVANTAGE_PG);    
    }
} else {
    if (is_post_type_archive(OLIVE_CSR_PT)) { 
        $inner_banner = get_field('inner_banner', OLIVE_CSR_CN_PG);    
    } 
    elseif (is_post_type_archive(OLIVE_NEWS_PT)) { 
        $inner_banner = get_field('inner_banner', OLIVE_NEWS_CN_PG);    
    } 
    // elseif (is_post_type_archive(OLIVE_INVESTMENT_PT)) { 
    //     $inner_banner = get_field('inner_banner', OLIVE_INVESTMENT_CN_PG);    
    // } 
    elseif (is_page(OLIVE_OUR_CLIENT_CN_PG)) { 
        $inner_banner = get_field('inner_banner', OLIVE_OUR_CLIENT_CN_PG);    
    } 
    elseif (is_page(OLIVE_GALLERY_CN_PG)) { 
        $inner_banner = get_field('inner_banner', OLIVE_GALLERY_CN_PG);    
    }
    elseif (is_post_type_archive(OLIVE_ADVANTAGE_PT)) { 
        $inner_banner = get_field('inner_banner', OLIVE_ADVANTAGE_CN_PG);    
    }
    elseif (is_post_type_archive(OLIVE_INDUSTRIAL_PT)) { 
        $inner_banner = get_field('inner_banner', OLIVE_INDUSTRIAL_CN_PG);    
    }
    elseif (is_tax(OLIVE_LOCATION_TAXO)) { 
        $inner_banner = get_field('inner_banner', OLIVE_INDUSTRIAL_CN_PG);    
    }
    elseif (is_singular(OLIVE_INDUSTRIAL_PT)) { 
        $inner_banner = get_field('inner_banner', OLIVE_INDUSTRIAL_CN_PG);    
    }
    elseif (is_singular(OLIVE_ADVANTAGE_PT)) { 
        $inner_banner = get_field('inner_banner', OLIVE_ADVANTAGE_CN_PG);    
    }
}
?>
<div class="inner-banner" style="background-size: cover!important;
    background-repeat: no-repeat!important;
    background-position: center;<?php if($inner_banner){ ?>background: url(<?php echo $inner_banner; ?>); <?php }else{ ?>background: url(<?php echo $default_banner; ?>);<?php } ?>">
    <div class="container">
        <div class="inner-banner-pg">
            <h1 class="text-center">
                <?php if (is_archive()) {
                    if ($obj->label == true) {
                        echo $obj->label;
                    }else {
                        echo $obj->name;
                    }
                }else {
                    echo $obj->post_title; 
                }
                ?>
            </h1>
          
            <!-- <h1 class="banner-title">Founded in 2001 in New York, USA</h1> -->
           
            <div class="text-center">
                <?php
                    if ( function_exists('yoast_breadcrumb') ) {
                    yoast_breadcrumb( '<p id="breadcrumbs">','</p>' );
                    }
                ?>
                 
            </div>
           
        </div>
    </div>
    <div class="banner-overlay"></div>
</div>