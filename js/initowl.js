        $('.owl-one').owlCarousel({
            loop: true,
            margin: 0,
            nav: true,
            autoWidth: true,
            animateOut: 'slideOutDown',
            animateIn: 'flipInX',
            autoplay: true,
            autoplayTimeout: 2500,
            autoplayHoverPause: true,


            navText: ['<i class="fa fa-caret-left" title="Anterior"></i>', '<i class="fa  fa-caret-right" title="Siguiente"></i>'],
            responsive: {
                0: {
                    items: 2,
                    margin: 60
                },
                544: {
                    items: 2,
                    margin: 60
                },
                800: {
                    items: 4,
                    margin: 60
                },
                1000: {
                    items: 5,
                    margin: 60
                }
            }
        });

        $('.owl-two').owlCarousel({
            loop: true,
            margin: 0,
            nav: true,
            autoWidth: true,
            autoplay: true,
            autoplayTimeout: 2500,
            autoplayHoverPause: true,


            navText: ['<i class="fa fa-caret-left" title="Anterior"></i>', '<i class="fa  fa-caret-right" title="Siguiente"></i>'],
            responsive: {
                0: {
                    items: 2,
                    margin: 30
                },
                576: {
                    items: 2,
                    margin: 75
                },
                768: {
                    items: 3,
                    margin: 45
                },
                992: {
                    items: 4,
                    margin: 60
                },
                1200: {
                    items: 5,
                    margin: 50
                }
            }
        });

        $('.owl-three').owlCarousel({
            loop: true,
            margin: 0,
            nav: false,
            autoWidth: true,
            autoplay: true,
            autoplayTimeout: 2500,
            autoplayHoverPause: true,


            navText: ['<i class="fa fa-caret-left" title="Anterior"></i>', '<i class="fa  fa-caret-right" title="Siguiente"></i>'],
            responsive: {
                0: {
                    items: 2,
                    margin: 20
                },
                576: {
                    items: 2,
                    margin: 60
                },
                992: {
                    items: 4,
                    margin: 60
                },
                1200: {
                    items: 5,
                    margin: 40
                }
            }
        });

        $('.play').on('click', function() {
            owl.trigger('play.owl.autoplay', [2000])
        });
        $('.stop').on('click', function() {
            owl.trigger('stop.owl.autoplay')
        });

