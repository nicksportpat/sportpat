(function($){
    $(document).ready(function () {


        $(".page-header").prependTo('body');


        document.addEventListener("touchstart", function () {
        }, false);


        $('body').wrapInner('<div class="wsmenucontainer" />');
        $('<div class="overlapblackbg"></div>').prependTo('.wsmenu');

        $('#wsnavtoggle').click(function () {
            $('body').toggleClass('wsactive');
        });

        $('.overlapblackbg').click(function () {
            $("body").removeClass('wsactive');
        });

        $('.wsmenu > .wsmenu-list > li').has('.sub-menu').prepend('<span class="wsmenu-click"><i class="wsmenu-arrow"></i></span>');
        $('.wsmenu > .wsmenu-list > li').has('.wsmegamenu').prepend('<span class="wsmenu-click"><i class="wsmenu-arrow"></i></span>');

        $('.wsmenu-click').click(function () {
            $(this).toggleClass('ws-activearrow')
                .parent().siblings().children().removeClass('ws-activearrow');
            $(".wsmenu > .wsmenu-list > li > .sub-menu, .wsmegamenu").not($(this).siblings('.wsmenu > .wsmenu-list > li > .sub-menu, .wsmegamenu')).slideUp('slow');
            $(this).siblings('.sub-menu').slideToggle('slow');
            $(this).siblings('.wsmegamenu').slideToggle('slow');
        });

        $('.wsmenu > .wsmenu-list > li > ul > li').has('.sub-menu').prepend('<span class="wsmenu-click02"><i class="wsmenu-arrow"></i></span>');
        $('.wsmenu > .wsmenu-list > li > ul > li > ul > li').has('.sub-menu').prepend('<span class="wsmenu-click02"><i class="wsmenu-arrow"></i></span>');

        $('.wsmenu-click02').click(function () {
            $(this).children('.wsmenu-arrow').toggleClass('wsmenu-rotate');
            $(this).siblings('li > .sub-menu').slideToggle('slow');
        });

        $(window).on('resize', function () {

            if ($(window).outerWidth() < 992) {
                $('.smllogo').css('z-index', '2000');
                $('.smllogo').css('position', 'absolute');
                var authHtml = $('li.authorization-link');


                var langHtml = "<select id='switcher-language-nav1' class='mobile-lang-switcher' >";

                if (window.location.href.indexOf("/en") > -1) {


                    langHtml += "<option id='en' selected value='en' >EN</option><option id='fr' value='fr' >FR</option>";

                } else if (window.location.href.indexOf("/fr") > -1) {

                    langHtml += "<option id='en' value='en' >EN</option><option id='fr' value='fr' selected >FR</option>";


                } else {
                    langHtml += "<option id='en' selected value='en' >EN</option><option id='fr' value='fr' onclick='window.location.href = \"/fr\";'>FR</option>";

                }
                langHtml += "</select>";

                $('.wsmobileheader').append($(langHtml));
                $('.wsmobileheader').append(authHtml);


                $("select#switcher-language-nav1").on("change", function (e) {

                    if (window.location.href.indexOf("/en") > -1) {

                        window.location.href = window.location.href.replace("/en", "/fr");

                    } else if (window.location.href.indexOf("/fr") > -1) {

                        window.location.href = window.location.href.replace("/fr", "/en");


                    } else {

                        window.location.href = "/fr";
                    }

                });


                $('.smllogo img').on('click', function (e) {

                    if (window.location.href.indexOf("/en") > -1) {
                        window.location.href = "/en";
                    }


                    if (window.location.href.indexOf("/fr") > -1) {
                        window.location.href = "/fr";
                    }
                });


                $('.wsmenu').css('height', $(this).height() + "px");
                $('.wsmenucontainer').css('min-width', $(this).width() + "px");
            } else {
                $('.wsmenu').removeAttr("style");
                $('.wsmenucontainer').removeAttr("style");
                $('body').removeClass("wsactive");
                $('.wsmenu > .wsmenu-list > li > .wsmegamenu, .wsmenu > .wsmenu-list > li > ul.sub-menu, .wsmenu > .wsmenu-list > li > ul.sub-menu > li > ul.sub-menu, .wsmenu > .wsmenu-list > li > ul.sub-menu > li > ul.sub-menu > li > ul.sub-menu').removeAttr("style");
                $('.wsmenu-click').removeClass("ws-activearrow");
                $('.wsmenu-click02 > i').removeClass("wsmenu-rotate");

            }

        });


        $(window).trigger('resize');
        /* Top Fixed */
        $(window).scroll(function () {
            var sticky = $('.wsmainwp'),
                scroll = $(window).scrollTop();
            if (scroll >= 135) sticky.addClass('wsfixed');
            else sticky.removeClass('wsfixed');
        });
        /* End Top Fixed */

    })


})(jQuery);

