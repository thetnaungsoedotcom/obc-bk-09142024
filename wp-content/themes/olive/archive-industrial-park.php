<?php 
get_header(); 
$current_lang = apply_filters( 'wpml_current_language', NULL ); // checking for current language
get_template_part('partials/inner-banner'); 
// $industry_pg = get_post(OLIVE_INDUSTRIAL_PG);
// $contact_field = get_fields($industry_pg->ID);

if ($current_lang == "en") {
    if (is_post_type_archive(OLIVE_INDUSTRIAL_PT)) {
        $pg_info = get_post(OLIVE_INDUSTRIAL_PG);
        $contact_field = get_fields($pg_info->ID);
    }
} else {
    if (is_post_type_archive(OLIVE_INDUSTRIAL_PT)) {
        $pg_info = get_post(OLIVE_INDUSTRIAL_CN_PG);
        $contact_field = get_fields($pg_info->ID);
    }
}
?>
<section class="industrial-list section-wrap">
    <div class="container">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="main">
                    <h1 class="page-title"><?php echo $contact_field['page_title']; ?></h1>
                    <article class="main-content"><?php echo apply_filters('the_content', $contact_field['page_description']); ?></article>
                </div>
            </div>
        </div>

        <?php 
         if ( have_posts() ) { ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php
                $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
                while ( have_posts() ) { the_post();
                    $post_title = $post->post_title;
                    $industrial_image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
                    $industrial_image = aq_resize($industrial_image[0], 480, 322, true, true, true ,true);
                    $industrial_link = get_permalink($post->ID);
                    $short_excerpt = $post->post_excerpt;
                    $tsps = get_the_terms($post->ID,'location');

                    $fields = get_fields($post->ID); 
                    $location = $fields['industrial_map'];
                ?>
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="<?php echo  $industrial_link; ?>" title="<?php echo $post_title ;?>">
                                        <?php echo $post_title ;?>
                                    </a>
                                </h5>
                                <?php foreach ($tsps as $tsp) { ?>
                                    <a href="<?php echo get_term_link($tsp); ?>">
                                        <span class="location"><?php echo $tsp->name; ?></span>
                                    </a>
                                <?php } ?>
                                <p class="card-text"><?php echo $short_excerpt  ;?></p>
                                <!-- <span><a href=" <?php //echo $industrial_link; ?>" class="btn btn-primary">Read More<i class="fas fa-arrow-right"></a></i></span> -->
                                
                            </div>
                            <figure>
                                <a href="<?php echo  $industrial_link; ?>" title="<?php echo $post_title; ?>">
                                    <img src="<?php echo $industrial_image ; ?>" class="card-img-top img-fluid industry-img" alt="<?php echo $post_title ;?>">
                                </a>
                            </figure>
                            <!-- Google Map Wrap -->
                            <div class="acf-map">
                                <div class="marker" data-lat="<?php echo $location['lat']; ?>" data-lng="<?php echo $location['lng']; ?>">
                                    <?php if ( $post_title ) { ?>
                                        <h4 class="map-name"><?php echo $post_title; ?></h4>
                                    <?php } ?>
                                    <?php if ( $location['address'] ) { ?>
                                        <address class="address"><?php echo $location['address']; ?></address> 
                                    <?php } ?>
                                </div> 
                            </div>
                            <!-- #Google Map Wrap -->
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
        <?php } ?>
    </div>
</section>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC44n4EJxputPRoWzorOaszqW-dFoVN8UE&callback=initMap"></script>
<script type="text/javascript">
(function($) {
/*
*  new_map
*  This function will render a Google Map onto the selected jQuery element
*/
function new_map( $el ) {
    // variables
    var $markers = $el.find('.marker');
    var args = {
        zoom: 16,
        center: new google.maps.LatLng(0, 0),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    // create map
    var map = new google.maps.Map( $el[0], args);

    // add a markers reference
    map.markers = [];

    // add markers
    $markers.each(function(){
        add_marker( $(this), map );
    });

    // center map
    center_map( map );

    // return
    return map;
}

/*
*  add_marker
*  This function will add a marker to the selected Google Map
*  @param   $marker (jQuery element)
*  @param   map (Google Map object)
*/
function add_marker( $marker, map ) {

    // var
    var latlng = new google.maps.LatLng( $marker.attr('data-lat'), $marker.attr('data-lng') );

    // create marker
    var marker = new google.maps.Marker({
        position: latlng,
        map: map
    });

    // add to array
    map.markers.push( marker );

    // if marker contains HTML, add it to an infoWindow
    if( $marker.html() )
    {
        // create info window
        var infowindow = new google.maps.InfoWindow({
            content: $marker.html()
        });

        // show info window when marker is clicked
        google.maps.event.addListener(marker, 'click', function() {
            infowindow.open( map, marker );
        });
    }
}

/*
*  center_map
*  This function will center the map, showing all markers attached to this map
*  @param   map (Google Map object)
*/
function center_map( map ) {

    // vars
    var bounds = new google.maps.LatLngBounds();

    // loop through all markers and create bounds
    $.each( map.markers, function( i, marker ){
        var latlng = new google.maps.LatLng( marker.position.lat(), marker.position.lng() );
        bounds.extend( latlng );
    });

    // only 1 marker?
    if( map.markers.length == 1 ) {
        // set center of map
        map.setCenter( bounds.getCenter() );
        map.setZoom( 16 );
    } else {
        // fit to bounds
        map.fitBounds( bounds );
    }
}

/*
*  document ready
*  This function will render each map when the document is ready (page has loaded)
*/
// global var
var map = null;
$(document).ready(function() {
    $('.acf-map').each(function() {
        // create map
        map = new_map( $(this) );
    });
});

})(jQuery);
</script>                           

<?php get_footer(); ?>
