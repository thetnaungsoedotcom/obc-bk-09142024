<?php 
// Template Name: Contact Us Page 
get_header(); 
$current_lang = apply_filters( 'wpml_current_language', NULL ); // checking for current language
get_template_part('partials/inner-banner'); 
$contact_field = get_fields();
$site_info = get_field('general_setting','option');
$social = get_field('social_media_links','option');
$map = get_field('map','option');
?>

<section class="contact-us section-wrap">
   <div class="container">
      <?php if ($contact_field['contact_us_main_title']) { ?>
         <h1 class="page-title"><?php echo $contact_field['contact_us_main_title']; ?></h1>
      <?php } ?>

      <?php if ($contact_field['contact_us_main_content']) { ?>
         <p class="contact-main-content"><?php echo $contact_field['contact_us_main_content']; ?></p>
      <?php } ?>

      <div class="row g-0">
         <div class="col-lg-5 bg">
            <div class="contact-info">
               <?php if ($contact_field['contact_us_main_title']) { ?>
                  <h3 class="section-title contact-info-title"><?php echo $contact_field['contact_info_title']; ?></h3>
               <?php } ?>
               <?php if ($contact_field['contact_info_content']) { ?>
                  <p class="contact-info-content"><?php echo $contact_field['contact_info_content']; ?></p>
               <?php } ?>

               <div class="contact-link">
                  <?php if ($site_info['contact_address']) { ?>
                     <div class="d-flex mb">
                        <div class="flex-shrink-0"><i class="fa-regular fa-location-dot"></i></div>
                        <div class="flex-grow-1 ms-3"><p><?php echo nl2br($site_info['contact_address']); ?></p></div>
                     </div>
                  <?php } ?>

                  <?php if ($site_info['contact_email']) { ?>
                     <div class="d-flex mb">
                        <div class="flex-shrink-0"><i class="far fa-envelope"></i></div>
                        <div class="flex-grow-1 ms-3"><p><?php contact_link($site_info['contact_email'], 'mailto:'); ?></p></div>
                     </div>
                  <?php } ?>

                  <?php if ($site_info['contact_number']) { ?>
                     <div class="d-flex mb">
                        <div class="flex-shrink-0"><i class="far fa-phone"></i></div>
                        <div class="flex-grow-1 ms-3"><p><?php contact_link($site_info['contact_number'], 'tel:'); ?></p></div>
                     </div>
                  <?php } ?>
               </div>

               <div class="social-links">
                  <?php if ($social['linkedin']) { ?>
                     <a href="<?php echo $social['linkedin']; ?>" target="_blank"><i class="fab fa-linkedin"></i></a>
                  <?php } ?>
                  <?php if ($social['twitter']) { ?>
                     <a href="<?php echo $social['twitter']; ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                  <?php } ?>
                  <?php if ($social['facebook']) { ?>
                     <a href="<?php echo $social['facebook']; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                  <?php } ?>
                  <?php if ($social['youtube']) { ?>
                     <a href="<?php echo $social['youtube']; ?>" target="_blank"><i class="fab fa-youtube"></i></a>
                  <?php } ?>
                  <?php if ($social['instagram']) { ?>
                     <a href="<?php echo $social['instagram']; ?>" target="_blank"><i class="fa-brands fa-instagram-square"></i></a>
                  <?php } ?>
                  <?php if ($social['telegram']) { ?>
                     <a href="<?php echo $social['telegram']; ?>" target="_blank"><i class="fa-brands fa-telegram"></i></a>
                  <?php } ?>
               </div>
            </div>
         </div>
         <div class="col-lg-7 form-wrap">
             <div class="form">
               <?php 
               if ($current_lang == "en") {
                  echo do_shortcode('[contact-form-7 id="244" title="Contact form (EN)"]');
               } else {
                  echo do_shortcode('[contact-form-7 id="1698" title="Contact form (CN)"]');
               } 
               ?>
            </div>
         </div>
      </div>
   </div>
</section>
<section class="map-section">
   <div class="map-container">
         <div class="wrap">
            <div class="acf-map">
               <div class="marker" data-lat="<?php echo  $map['lat']; ?>" data-lng="<?php echo  $map['lng']; ?>">
                  <h4><a href="<?php the_permalink(); ?>" rel="bookmark"> <?php the_title(); ?></a></h4>
                  <p class="address"><?php echo $map['address']; ?></p>
                  <a href="https://www.google.com/maps?saddr=My+Location&daddr=<?php echo  $map['address']; ?>" target="_blank"><?php _e('Get Directions'); ?></a>
               </div>
            </div>
         </div>
   </div>  
</section>
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
		zoom		: 15,
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
		position: latlng,
		map: map,
      icon: '<?php echo ASSET_URL; ?>images/map.png',
      animation: google.maps.Animation.BOUNCE,
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