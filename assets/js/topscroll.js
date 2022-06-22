        $(window).scroll(function() {
            if ($(this).scrollTop() > 50 ) {
                $('.scrolltop:hidden').stop(true, true).fadeIn();
            } else {
                $('.scrolltop').stop(true, true).fadeOut();
            }
        });
        $(function(){$(".scroll").click(function(){$("html,body").animate({scrollTop:$("#navbar-outer-up").offset().top},"1000");return false})})