<?php 
get_header(); 
get_template_part('partials/inner-banner');
$single_feature_img = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
$locations = get_the_terms($post->ID,'location');
$fields = get_fields($post->ID); 
$map = $fields['industrial_map'];
?>
<main class="single-industrial section-wrap">
   <div class="container">
      <div class="row">

         <div class="col-lg-8">
            <div class="single-content-wrap">
               <?php if ($single_feature_img[0]) { ?>
                  <img src="<?php echo $single_feature_img[0]; ?>" class="w-100 single-feature-image" alt="<?php echo $post->post_title; ?>">
               <?php } ?>
              
               <!-- <span class="location"><?php var_dump($location); ?></span> -->
               <div class="single-body">
               <?php foreach ($locations as $location) { ?><span class="location"><?php echo $location->name; ?></span><?php } ?>
                  <h1 class="single-main-title page-title"><?php echo $post->post_title; ?></h1>
                  <?php if ($post->post_content) { ?>
                     <article class="main-content-wrap"><?php echo apply_filters('the_content', $post->post_content); ?></article>
                  <?php } ?>
               </div>
            </div>
            <div class="map-container">
               <h3 class="loction-title">Location of <?php echo $post->post_title;?></h3>
               <div class="wrap">
                  <div class="acf-map" style="height: 400px;">
                     <div class="marker" data-lat="<?php echo  $map['lat']; ?>" data-lng="<?php echo  $map['lng']; ?>">
                        <h4><a href="<?php the_permalink(); ?>" rel="bookmark"> <?php the_title(); ?></a></h4>
                        <p class="address"><?php echo $map['address']; ?></p>
                        <a href="https://www.google.com/maps?saddr=My+Location&daddr=<?php echo  $map['address']; ?>" target="_blank"><?php _e('Get Directions'); ?></a>
                     </div>
                  </div>
               </div>
             </div>
         </div>

         <aside class="col-lg-4 sidebar-wrap"><?php get_sidebar(); ?></aside> 
      </div>
   </div>
</main>
<script>
    (function($) {

/*
*  new_map
*
*  This function will render a Google Map onto the selected jQuery element
*
*  @type	function
*  @date	8/11/2013
*  @since	4.3.0
*
*  @param	$el (jQuery element)
*  @return	n/a
*/

function new_map( $el ) {

	// var
	var $markers = $el.find('.marker');


	// vars
	var args = {
		zoom		: 35,
		center		: new google.maps.LatLng(0, 0),
		mapTypeId	: google.maps.MapTypeId.ROADMAP
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
*
*  This function will add a marker to the selected Google Map
*
*  @type	function
*  @date	8/11/2013
*  @since	4.3.0
*
*  @param	$marker (jQuery element)
*  @param	map (Google Map object)
*  @return	n/a
*/

function add_marker( $marker, map ) {

	// var
	var latlng = new google.maps.LatLng( $marker.attr('data-lat'), $marker.attr('data-lng') );

	// create marker
	var marker = new google.maps.Marker({
		position	: latlng,
		map			: map
	});

	// add to array
	map.markers.push( marker );

	// if marker contains HTML, add it to an infoWindow
	if( $marker.html() )
	{
		// create info window
		var infowindow = new google.maps.InfoWindow({
			content		: $marker.html()
		});

		// show info window when marker is clicked
		google.maps.event.addListener(marker, 'click', function() {

			infowindow.open( map, marker );

		});

        google.maps.event.addListener(marker, 'click', function() {

            map.setZoom(18);

            map.setCenter(marker.getPosition());

        });
	}

}

/*
*  center_map
*
*  This function will center the map, showing all markers attached to this map
*
*  @type	function
*  @date	8/11/2013
*  @since	4.3.0
*
*  @param	map (Google Map object)
*  @return	n/a
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
	if( map.markers.length == 1 )
	{
		// set center of map
	    map.setCenter( bounds.getCenter() );
	    map.setZoom( 15);
	}
	else
	{
		// fit to bounds
		  map.setCenter( bounds.getCenter() );
	   	map.setZoom( 15 ); // Change the zoom value as required
		//map.fitBounds( bounds ); // This is the default setting which I have uncommented to stop the World Map being repeated

	}

}

/*
*  document ready
*
*  This function will render each map when the document is ready (page has loaded)
*
*  @type	function
*  @date	8/11/2013
*  @since	5.0.0
*
*  @param	n/a
*  @return	n/a
*/
// global var
var map = null;

jQuery(document).ready(function($){

	$('.acf-map').each(function(){

		// create map
		map = new_map( $(this) );

	});


});

})(jQuery);
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC44n4EJxputPRoWzorOaszqW-dFoVN8UE&callback=initMap"></script>
<?php get_footer(); ?>