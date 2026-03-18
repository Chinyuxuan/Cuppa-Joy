
    $(document).ready(function(){
        $("#barista-carousel").owlCarousel({
            items: 3, // Number of items to show at a time
            loop: true, // Infinite loop
            autoplay: true, // Autoplay enabled
            margin: 1, // Margin between items
            responsive:{
                0:{
                    items:1, // Number of items to show on smaller screens
                    nav:false
                },
                600:{
                    items:2, // Number of items to show on medium-sized screens
                    nav:false
                },
                1000:{
                    items:3, // Number of items to show on larger screens
                    nav:false,
                    loop:true
                }
            }
        });
    });
