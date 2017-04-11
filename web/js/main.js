$(document).ready(function(){
    
    //sticky nav
    $(".navbar").sticky({topSpacing:0});
    
    // yamm menu
    $(document).on('click', '.yamm .dropdown-menu', function(e) {
        e.stopPropagation();
    })
    
    $('ul.dropdown-menu [data-toggle=dropdown]').on('click', function(event) {
        event.preventDefault(); 
        event.stopPropagation(); 
        $(this).parent().siblings().removeClass('open');
        $(this).parent().toggleClass('open');
    });
    
    //tootltip
    $('[data-toggle="tooltip"]').tooltip();
    
    //Popover
    $('[data-toggle="popover"]').popover();
    
    //owl carousel 5 Columns
    $(".owl-carousel.column-5").owlCarousel({
        nav : true, // Show next and prev buttons
        navText: false,
        dots: false,
        items: 5,
        margin: 15,
        responsiveClass: true,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:3
            },
            1000:{
                items:5
            }
        }
    });
    
    //owl carousel 4 Columns
    $(".owl-carousel.column-4").owlCarousel({
        nav : true, // Show next and prev buttons
        navText: false,
        dots: false,
        items: 4,
        margin: 15,
        responsiveClass: true,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:2
            },
            1000:{
                items:4
            }
        }
    });
    
    //owl carousel 3 Columns
    $(".owl-carousel.column-3").owlCarousel({
        nav : true, // Show next and prev buttons
        navText: false,
        dots: false,
        items: 3,
        margin: 15,
        responsiveClass: true,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:2
            },
            1000:{
                items:3
            }
        }
    });
    
    //owl slider
    $('.slider').owlCarousel({
        animateOut: 'zoomOut',
        nav : true, // Show next and prev buttons
        navText: false,
        dots: true,
        items: 1,
        margin: 0,
        smartSpeed: 450
    });
    
    //owl product-showcase
    $('.product-showcase').owlCarousel({
        animateOut: 'lightSpeedOut',
        nav : true, // Show next and prev buttons
        navText: false,
        dots: true,
        items: 1,
        margin: 0,
        smartSpeed: 450
    });
    
    //owl next prev icons
    $(".owl-carousel .owl-next").addClass("fa fa-angle-right");
    $(".owl-carousel .owl-prev").addClass("fa fa-angle-left");
    
    //CountDown
    $('.countdown').downCount({
        date: '10/21/2017 12:00:00',
        offset: +1
    }, function () {
        //alert('WOOT WOOT, done!');
    });
    
    //CountDown
    $('.countdown-product').downCount({
        date: '10/21/2017 12:00:00',
        offset: +1
    }, function () {
        //alert('WOOT WOOT, done!');
    });
    
    // Range Slider
    var rangeSlider  = document.querySelector('.ui-range-slider');
    if(typeof rangeSlider !== 'undefined' && rangeSlider !== null) {
        var dataStartMin = parseInt(rangeSlider.parentNode.getAttribute( 'data-start-min' ), 10),
            dataStartMax = parseInt(rangeSlider.parentNode.getAttribute( 'data-start-max' ), 10),
            dataMin = parseInt(rangeSlider.parentNode.getAttribute( 'data-min' ), 10),
            dataMax = parseInt(rangeSlider.parentNode.getAttribute( 'data-max' ), 10),
            dataStep = parseInt(rangeSlider.parentNode.getAttribute( 'data-step' ), 10);
        var valueMin = document.querySelector('.ui-range-value-min span'),
            valueMax = document.querySelector('.ui-range-value-max span'),
            valueMinInput = document.querySelector('.ui-range-value-min input'),
            valueMaxInput = document.querySelector('.ui-range-value-max input');
        noUiSlider.create(rangeSlider, {
            start: [ dataStartMin, dataStartMax ],
            connect: true,
            step: dataStep,
            range: {
                'min': dataMin,
                'max': dataMax
            }
        });
        rangeSlider.noUiSlider.on('update', function(values, handle) {
            var value = values[handle];
            if ( handle ) {
                valueMax.innerHTML  = Math.round(value);
                valueMaxInput.value = Math.round(value);
            } else {
                valueMin.innerHTML  = Math.round(value);
                valueMinInput.value = Math.round(value);
            }
        });
    }
    
    //back to top
    $('body').append('<a href="javascript:void(0);" id="back-to-top"><i class="fa fa-angle-up"></i></a>');
    
    $(window).scroll(function() {
        if ($(this).scrollTop() >= 200) {
            $('#back-to-top').fadeIn(200);
        } else {
            $('#back-to-top').fadeOut(200);
        }
    });
    $('#back-to-top').click(function() {
        $('body,html').animate({
            scrollTop : 0
        }, 500);
    });
    
    //wow for animate.css
    new WOW().init();
    
});