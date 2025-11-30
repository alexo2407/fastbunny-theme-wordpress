$ = jQuery.noConflict();

/*------------------------
INICIAMOS WOW
-------------------------*/
new WOW().init();

/*----------------------------------
Iniciamos smoothScroll (Scroll Suave)
--------------------------------*/
var scroll = new SmoothScroll('a[href*="#"]', {
  speed: 1000,
  speedAsDuration: true
});

/*---------------------------------
    OCULTAR Y MOSTRAR BOTON IR ARRIBA
 ----------------------------------*/

$(document).scroll(function() {

  var scrolltop = $(this).scrollTop();
  if (scrolltop >= 50) {
    $(".ir-arriba").fadeIn();
  } else {
    $(".ir-arriba").fadeOut();
  }

});


/*------------------------
INICIAMOS NAVBAR
-------------------------*/
$('#navbar').bootnavbar();


$(window).scroll(function() {
  if ($("#menu").offset().top > 50) {
    $("#menu").addClass("bg-inverse");
  } else {
    $("#menu").removeClass("bg-inverse");
  }
});


/*------------------------
GALERIA
-------------------------*/
// $('.portfolio-item').isotope({
//  	itemSelector: '.item',
//  	layoutMode: 'fitRows'
//  });
$('.portfolio-menu ul li').click(function() {
  $('.portfolio-menu ul li').removeClass('active');
  $(this).addClass('active');

  var selector = $(this).attr('data-filter');
  $('.portfolio-item').isotope({
    filter: selector
  });
  return false;
});
$(document).ready(function() {
  var popup_btn = $('.popup-btn');
  popup_btn.magnificPopup({
    type: 'image',
    gallery: {
      enabled: true
    }
  });
});




$(document).ready(function() {
  jQuery('.gallery a').each(function() {

    jQuery(this).attr({
      'data-fluidbox': ''
    });

  });

  if (jQuery('[data-fluidbox]').length > 0) {
    jQuery('[data-fluidbox]').fluidbox();
  }

});
