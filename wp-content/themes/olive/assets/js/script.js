/*!
 *	Author: B360 Digital Marketing
	name: script.js	
	requires: jquery	
 */

$(document).ready(function() {
	
	//submenu
	// Mobile responsive nav
	$('.stellarnav').stellarNav({
		theme: 'plain',
		menuLabel: '',
		breakpoint: 992,
		position: 'right',
		openingSpeed: 250,
		closingSpeed: 250,
	});
	
	//gallery lsit 
	//masonry 
	$('.my-masonry-grid').masonryGrid({ 'columns': 3, });
	
	//logo slider
	
	var logoswiper = new Swiper(".logoSwiper", {
	  	slidesPerView: 4,
	  	autoplay: true,
	  	spaceBetween: 30,
	  	pagination: {
			el: ".logo-pag",
			clickable: true,
		
	  	},
		breakpoints: {
			992: {
				slidesPerView: 4,
			},
			768: {
				slidesPerView: 4,
			},
			576: {
				slidesPerView: 3,
			},
			320: {
				slidesPerView: 1,
			}
		}
	});
	//home banner slider
	// var bannerSlider = new Swiper(".banner-slider", {
	// 	slidesPerView: 1,
	// 	autoplay: true,
	// 	speed: 1000,
	// 	spaceBetween: 30,
	// 	pagination: {
	// 	  el: ".banner-pag",
	// 	  clickable: true,
		  
	// 	},
	// 	breakpoints: {
	// 	  992: {
	// 		  slidesPerView: 1,
	// 	  },
	// 	  578: {
	// 		  slidesPerView: 1,
	// 	  },
	// 	  200: {
	// 		  slidesPerView: 1,
	// 	  }
	//   }
	//   });

   //testimonalis slider

   
	var testswiper = new Swiper(".TestSwiper", {
		slidesPerView: 3,
		autoplay: true,
		spaceBetween: 30,
		pagination: {
		  el: ".test-pag",
		  clickable: true,
		  
		},
		breakpoints: {
		  992: {
			  slidesPerView: 3,
		  },
		  578: {
			  slidesPerView: 2,
		  },
		  200: {
			  slidesPerView: 1,
		  }
	  }
	  });
	// Mobile responsive nav
	// $('.stellarnav').stellarNav({
	// 	theme: 'plain',
	// 	menuLabel: '',
	// 	breakpoint: 992,
	// 	position: 'right',
	// 	openingSpeed: 250,
	// 	closingSpeed: 250,
	// });

	// Move to ID attr (id="id-name" attr with "a href='#join-form'")
	$('a[href^="#"]').on('click',function (e) {
		e.preventDefault();
		var target = this.hash;
		var $target = $(target);
		$('html, body').stop().animate({
			'scrollTop': $target.offset().top
		}, 900, 'swing', function () {
			// window.location.hash = target;
		});
	});

	// AOS Animate
	// AOS.init();

    //Team Member slider
	var team_swiper = new Swiper(".team-slider", {
        slidesPerView: 3,
        spaceBetween: 30,
        slidesPerGroup: 3,
        loop: true,
        loopFillGroupWithBlank: true,
        pagination: {
          el: ".team-pagination",
          clickable: true,
        },
        navigation: {
          nextEl: ".team-next",
          prevEl: ".team-prev",
        },
		breakpoints: {
			992: {
				slidesPerView: 4,
			},
			767: {
				slidesPerView: 3,
			},
			576: {
				slidesPerView: 2,
			},
			200: {
				slidesPerView: 1,
			}
		}
      });
	  jQuery( 'li.menu-item.active' ).parents().addClass( 'current-menu-ancestor' ); 
});
      
//Protfolio Slider
$(document).ready(function() {
	// Assign some jquery elements we'll need
	var $swiper = $(".portfolio");
	var $bottomSlide = null; // Slide whose content gets 'extracted' and placed
	// into a fixed position for animation purposes
	var $bottomSlideContent = null; // Slide content that gets passed between the
	// panning slide stack and the position 'behind'
	// the stack, needed for correct animation style

	var portfolioSwiper = new Swiper(".portfolio", {
		// spaceBetween: 15,
		// slidesPerView: 3,
		// centeredSlides: true,
		// //roundLengths: true,
		// loop: true,
		// //paginationClickable: true,
		// slideToClickedSlide: true,
		// //loopAdditionalSlides: 30,
		autoplay: true,
		loop: true,
		speed: 1000,
		slidesPerView: 1,
		spaceBetween: 10,
		pagination: {
			el: ".swiper-pagination",
			clickable: true,
		},
	});
});
	  
//endtest
$(window).on("load", function () {
// add class .active to center slide
$(".insights__list__item:first-child").addClass("active");

// puts last insights before first at start
$("#insight8, #insight7").prependTo(".insights__list");

const slideCount = $(".insights__list__item").length;
var slideWidth;
var sliderUlWidth;

/* ––––––––––––––––––––––––––––––– */
/* ––––––––slider functions––––––– */
/* ––––––––––––––––––––––––––––––– */

function startSlider() {
	$("#insightsSlider").css({ width: "432vw" });

	var windowWidth = $(".insights").width();
	var sliderOffset = (windowWidth / 100) * 88;

	slideWidth = $(".insights__list__item").width();
	slideWidth += (windowWidth / 100) * 12;
	sliderUlWidth = (windowWidth / 100) * 432;

	/*
	console.log("Insight Width " + slideWidth + "px");
	console.log("Slider Width " + sliderUlWidth + "px");
	console.log("Window Width " + windowWidth + "px");
	*/

	// sets width of container
	$("#insightsSlider").css({
	width: sliderUlWidth,
	marginLeft: -sliderOffset
	});
}

function moveRight() {
	$(".active").animate(
	{
		width: "40vw",
		height: "60%"
	},
	{ duration: 600, queue: false }
	);
	$(".active").next().animate(
	{
		width: "56vw",
		height: "100%"
	},
	{ duration: 600, queue: false }
	);
	$(".insights__list").animate(
	{ left: -slideWidth },
	{ duration: 600, queue: false }
	);

	$(".insights__list")
	.promise()
	.then(function () {
		// all finished
		$(".insights__list__item:first-child").appendTo(".insights__list");
		$(".insights__list").css("left", "");

		$(".active").next().addClass("active").removeClass("hovered");
		$(".insights__list__item:nth-child(2)").removeClass("active");
		$(".active").next().addClass("hovered");
	});
}
	  
function moveLeft() {
	$(".active").animate({
		width: "40vw",
		height: "60%"
	}, { 
		duration: 600, queue: false }
	);
	$(".active").prev().animate({
		width: "56vw",
		height: "100%"
	}, { 
		duration: 600, queue: false }
	);
	$(".insights__list").animate(
	{ right: - slideWidth },
	{ duration: 600, queue: false }
	);

	$(".insights__list")
	.promise()
	.then(function () {
		// all finished
		$(".insights__list__item:last-child").prependTo(".insights__list");
		$(".insights__list").css("right", "");

		$(".active").prev().addClass("active").removeClass("hovered");
		$(".insights__list__item:nth-child(4)").removeClass("active");
		$(".active").prev().addClass("hovered");
	});
}
	  
		/* ––––––––––––––––––––––––––––––– */
		/* ––––––––––––––––––––––––––––––– */
	  
		startSlider();
	  
		// Buttons & hovers for slider
		$("#arrowRight")
		  .mouseover(function () {
			$(".active").next().addClass("hovered");
		 })
		  .mouseout(function () {
			$(".active").next().removeClass("hovered");
		 })
		  .click(function () {
			moveRight();
		 });
		
		$("#arrowLeft")
		  .mouseover(function () {
			$(".active").prev().addClass("hovered");
		 })
		  .mouseout(function () {
			$(".active").prev().removeClass("hovered");
		 })
		  .click(function () {
			moveLeft();
		 });
	  
	  
		// update slider when resizing window
		$(window).resize(function () {
		  waitForFinalEvent(
			function () {
			  startSlider();
			},
			200,
			"resizeInsightSliderId"
		  );
		});
	  });
	  
	  // funtion that waits until thing is over
	  // used to not trigger window resize too many times
	  var waitForFinalEvent = (function () {
		var timers = {};
		return function (callback, ms, uniqueId) {
		  if (!uniqueId) {
			uniqueId = "Don't call this twice without a uniqueId";
		  }
		  if (timers[uniqueId]) {
			clearTimeout(timers[uniqueId]);
		  }
		  timers[uniqueId] = setTimeout(callback, ms);
		};
	  })();
	  
	  
      //News Slider
	  var news_swiper = new Swiper(".news-slider", {
        slidesPerView: 4,
        spaceBetween: 30,
        slidesPerGroup: 1,
		autoplay: true,
        loop: true,
        loopFillGroupWithBlank: false,
		speed: 1000,
        pagination: {
          el: ".news-pagination",
          clickable: true,
        },
        navigation: {
          nextEl: ".news-next",
          prevEl: ".news-prev",
        },
		breakpoints: {
			992: {
				slidesPerView: 4,
			},
			767: {
				slidesPerView: 2,
			},
			578: {
				slidesPerView: 2,
			},
			200: {
				slidesPerView: 1,
			}
		}
      });


/* menu */
// (function($) {
// 	'use strict';

	// call our plugin
	// var Nav = new hcOffcanvasNav('#main-nav', {
	//   disableAt: false,
	//   customToggle: '.toggle',
	//   levelSpacing: 40,
	//   navTitle: 'All Menu',
	//   levelTitles: true,
	//   levelTitleAsBack: true,
	//   pushContent: '#container',
	//   labelClose: false
	// });

	// add new items to original nav
	// $('#main-nav').find('li.add').children('a').on('click', function() {
	//   var $this = $(this);
	//   var $li = $this.parent();
	//   var items = eval('(' + $this.attr('data-add') + ')');

	//   $li.before('<li class="new"><a href="#">'+items[0]+'</a></li>');

	//   items.shift();

	//   if (!items.length) {
	// 	$li.remove();
	//   }
	//   else {
	// 	$this.attr('data-add', JSON.stringify(items));
	//   }

	//   Nav.update(true); // update DOM
	// });

	// demo settings update

// 	const update = function(settings) {
// 	  if (Nav.isOpen()) {
// 		Nav.on('close.once', function() {
// 		  Nav.update(settings);
// 		  Nav.open();
// 		});

// 		Nav.close();
// 	  }
// 	  else {
// 		Nav.update(settings);
// 	  }
// 	};

// 	$('.actions').find('a').on('click', function(e) {
// 	  e.preventDefault();

// 	  var $this = $(this).addClass('active');
// 	  var $siblings = $this.parent().siblings().children('a').removeClass('active');
// 	  var settings = eval('(' + $this.data('demo') + ')');

// 	  if ('theme' in settings) {
// 		$('body').removeClass().addClass('theme-' + settings['theme']);
// 	  }
// 	  else {
// 		update(settings);
// 	  }
// 	});

// 	$('.actions').find('input').on('change', function() {
// 	  var $this = $(this);
// 	  var settings = eval('(' + $this.data('demo') + ')');

// 	  if ($this.is(':checked')) {
// 		update(settings);
// 	  }
// 	  else {
// 		var removeData = {};
// 		$.each(settings, function(index, value) {
// 		  removeData[index] = false;
// 		});

// 		update(removeData);
// 	  }
// 	});
//   })(jQuery);
  