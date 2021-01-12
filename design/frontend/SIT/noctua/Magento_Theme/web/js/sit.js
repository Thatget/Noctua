require(['jquery', 'jquery/ui', 'jquery/jquery.cookie', 'dlmenu', 'modernizr'], function($, ui, dlmenu, modernizr) {
    /* Scroll to top && Background Image : JM*/
    $(window).scroll(function() {
        var windowHeight = $('.column.main').height();
        $('.background-image').css('min-height', windowHeight);
        var height = $(window).scrollTop();
        if (height > 100) {
            $('#sit-scrolltop').fadeIn(500);
        } else {
            $('#sit-scrolltop').fadeOut(500);
        }
    });

    /* For display loader in slider : MJ */
    var slider_loader = '<div class="slider-loader"></div>';
    $(slider_loader).appendTo('.columns .slider-container');

    /* Display youtube video after page load : RH */
    $(window).load(function() {
        if ($('body').hasClass('cms-home')) {
            var vidDefer = document.getElementsByTagName('iframe');
            for (var i = 0; i < vidDefer.length; i++) {
                if (vidDefer[i].getAttribute('data-src')) {
                    vidDefer[i].setAttribute('src', vidDefer[i].getAttribute('data-src'));
                }
            }
        }
        if ($('body').hasClass('catalog-product-view')) {
            var vidDefer = document.getElementsByTagName('iframe');
            if (vidDefer) {
                for (var i = 0; i < vidDefer.length; i++) {
                    if (vidDefer[i].getAttribute('data-src')) {
                        vidDefer[i].setAttribute('src', vidDefer[i].getAttribute('data-src'));
                    }
                }
            }
        }
    });

    /* For display active menu : MJ */
    $('.ui-menu-item').on('click', function() {
        var lastClass = $(this).attr('class').split(' ').pop();
        localStorage.setItem("active", lastClass);
    });

    $(document).ready(function() {
         $(".column.main ,.background-image").css('min-height', '350px'); //
       /* For display active menu : MJ */
        var menuList = ["technologies", "where-to-buy", "reviews", "press", "support", "industrial-applications", "products"];
        var currentUrl = window.location.href;
        $(menuList).each(function(index, value) {
            setUrlBasedActiveMenu(value);
        });
        /* To set active menu when direct url is passed */
        function setUrlBasedActiveMenu(value) {
            if (currentUrl.indexOf(value) != -1) {
                localStorage.removeItem("active");
                localStorage.setItem("active", value);
            }
        }
        var activeMenu = localStorage.getItem('active');
        $('.ui-menu-item.' + activeMenu).addClass('active');
        localStorage.removeItem("active");

        /* Code for home page review show less and show more : NG */
        if ($('body').hasClass('cms-home')) {
            $('.review-label > a').each(function() {
                var el = $(this);
                var height = el.height();
                divLineHeight = parseInt(el.css('line-height'));
                lineToShow = 1;
                divPreviewHeight = divLineHeight * lineToShow + 1;
                if (height > divPreviewHeight) {
                    el.css('height', divPreviewHeight);
                    el.siblings('.more-details').show();
                    el.siblings('.more-details').toggle(
                        function(e) {
                            el.animate({
                                'height': height
                            });
                            $(this).children('.more-icon').addClass('less-icon');
                            $(this).children('.more-details-text').hide();
                        },
                        function(e) {
                            el.animate({
                                'height': divPreviewHeight
                            });
                            $(this).children('.more-icon').removeClass('less-icon');
                            $(this).children('.more-details-text').show();
                        });
                }
            });
        }
        var linkUrl = $("#ajaxsearch-autocomp-url").val();
        var currentRequest;

        var keyDown = 0 ;
        $("#search").on("keyup", function(e) {
            if(e.keyCode == 38 || e.keyCode == 40 || e.keyCode == 13){
                selectedOnArrow(e.keyCode);
            }else{
                eptAutoComplete();
            }
        });

        //For trigger enter event on click search : MJ
        $(window).on('resize',function(){
            var win = $(this);
            if(win.width() > 1000){
                $('#search_mini_form div.actions').hide();
                $('.block-search .label').css({display: 'inline-block', width: '25px', height: '23px',right: '2px', top:'-1px',border: '1px solid #eee'});
                $('.block-search .control').css({display: 'inline-block'});
                $('label.label').addClass('sit-search-desktop');
                triggerEnterEvent();
            }else{
                $('.block-search .label').removeAttr('style');
                $('.block-search .control').removeAttr('style');
                triggerEnterEvent();
            }
        });

        if($(window).width() > 1000){
            $('#search_mini_form div.actions').hide();
            $('.block-search .label').css({display: 'inline-block', width: '25px', height: '23px',right: '2px', top:'-1px',border: '1px solid #eee'});
            $('.block-search .control').css({display: 'inline-block'});
            $('label.label').addClass('sit-search-desktop');
            triggerEnterEvent();
        }else{
            $('#search_mini_form').removeClass('active');
            $('#search').removeAttr('aria-expanded');
            $('#search').removeAttr('aria-haspopup');
            $('label.label').removeClass('active');
            $('label.label').removeAttr('data-role');
            triggerEnterEvent();
        }

        function triggerEnterEvent(){
            var preventClick = 0;
            $('.field.search label.label').on('click',function(e){
                preventClick++;
                if(preventClick == 1){
                    $('#search_mini_form').addClass('active');
                    $('label.label').addClass('active');
                    $('#search').attr('aria-expanded','true','aria-haspopup','ture');
                    $('label.label').attr('data-role');
                }else{
                    $('#search').attr('aria-haspopup','true');
                    $('div.control #search').show();
                    if($('#search').val()){
                        var e = $.Event( "keydown", { which:13, selector:$('#search') } );
                        $('#search').trigger(e);
                    }
                    else{
                        alert('Please enter keyword to search');
                    }
                }
            });
        }
        //For trigger enter event on click search : MJ

        // Added By MJ [For selected list] [20.06.2019]
        function selectedOnArrow(keyCode){
            var li = $('#search_autocomplete ul li');
            var link = '';
            if(keyCode === 40){
                if(li.length >= keyDown){
                    if(keyDown == li.length){
                        keyDown = 0;
                    }
                    keyDown++;
                    li.eq(keyDown).addClass('selected');
                    var title = li.eq(keyDown).attr('title');
                    link = li.eq(keyDown).children('a').attr('href');
                    $('#search').val(title);
                    $('#search').attr('direct-link',link);
                    $("#search_autocomplete").scrollTop(0);
                }
            }else if(keyCode === 38){
                if(li.length >= keyDown){
                    if(keyDown < 1){
                        keyDown = 1;
                    }
                    $('#search_autocomplete ul li').removeClass('selected');
                    keyDown--;
                    li.eq(keyDown).addClass('selected');
                    var title = li.eq(keyDown).attr('title');
                    link = li.eq(keyDown).children('a').attr('href');
                    $('#search').val(title);
                    $('#search').attr('direct-link',link);
                    $("#search_autocomplete").scrollTop(0);
                }
            }else if(keyCode === 13){
                li.last().children('a').attr('href');
                console.log(li.last().children('a').attr('href'));
                if(keyDown > 0){
                    window.location.replace($('#search').attr('direct-link'));
                }
            }
            $("#search_autocomplete").scrollTop(0);//set to top
            $("#search_autocomplete").scrollTop($('.selected').offset().top - $("#search_autocomplete").height());
            li.eq(keyDown-1).removeClass('selected');
        }
        // Added By MJ [For selected list] [20.06.2019]

        function eptAutoComplete() {
            var searchVal = $('#search').val();
            if (searchVal.length >= 2) {
                var data = $("form").serialize();
                currentRequest = $.ajax({
                    type: "GET",
                    url: linkUrl + "?q=" + searchVal,
                    data: {
                        id: "id"
                    },
                    beforeSend: function() {
                        if (currentRequest != null) {
                            currentRequest.abort();
                        }
                    },
                    success: function(data) {
                        if (data != "") {
                            $("#search_autocomplete").html(data).show();
                        } else {
                            $("#search_autocomplete").html(data).show();
                        }
                    }
                });
            } else {
                $("#search_autocomplete").hide();
            }
        }
        /* Code for home page review show less and show more : NG */

        /* Award lazy load image : NG */
        function lazyLoadImages() {
            var e = document.querySelectorAll("img[data-src]");
            [].forEach.call(e, function(e) {
                isElementInViewport(e) && (e.setAttribute("src", e.getAttribute("data-src")), e.removeAttribute("data-src"))
            }), 0 == e.length && (window.removeEventListener("DOMContentLoaded", lazyLoadImages), window.removeEventListener("load", lazyLoadImages), window.removeEventListener("resize", lazyLoadImages), window.removeEventListener("scroll", lazyLoadImages))
        }

        function isElementInViewport(e) {
            var t = e.getBoundingClientRect();
            return t.top >= 0 && t.left >= 0 && t.bottom <= (window.innerHeight || document.documentElement.clientHeight) && t.right <= (window.innerWidth || document.documentElement.clientWidth)
        }
        window.addEventListener("DOMContentLoaded", lazyLoadImages), window.addEventListener("load", lazyLoadImages), window.addEventListener("resize", lazyLoadImages), window.addEventListener("scroll", lazyLoadImages)

        $(document).ajaxComplete(function() {
            lazyLoadImages();
        });

        $(window).on("scroll", function() {
            lazyLoadImages();

        });
        /* Award lazy load image : NG */

        /* Scroll to top && Background Image */
        var windowHeight = $('.column.main').height();
        $('.background-image').css('min-height', windowHeight);

        $("#sit-scrolltop").click(function(event) {
            event.preventDefault();
            $("html, body").animate({
                scrollTop: 0
            }, "slow");
            return false;
        });
        /* Scroll to top && Background Image */

        /* Add For Child Tab : NG */
        $('div.data.item.title').removeClass('active');

        $('.child-tab-title:first-child .switch').addClass('active');
        $('.child.switch.checkSwitch').click(function(e) {
            e.preventDefault();
            $('.child.switch').removeClass('active');
            $(this).addClass('active');
            var id = $(this).attr('data-url');
            $('.child-tab-content').hide();
            $('.child-tab-content#' + id).show();
        });
        /* Add For Child Tab : NG */

        /* Add For Horizontal Scroller : JM */
        if ($('.custom-scroller').length) {
            $('.custom-scroller').append('<span class="panner panLeft"></span><span class="panner panRight"></span>');
        }
        $('.panLeft').click(function() {
            var scroller = $(this).parents('.custom-scroller');
            $(scroller).animate({
                scrollLeft: "-=700px"
            }, 2000);
            $(this).siblings('.panRight').show();
            $(this).hide();
        });

        $('.panRight').click(function() {
            var scroller = $(this).parents('.custom-scroller');
            $(scroller).animate({
                scrollLeft: "+=700px"
            }, 2000);
            $(this).siblings('.panLeft').show();
            $(this).hide();
        });
        /* Add For Horizontal Scroller : JM */

        /* Add for China Store Popup : JM */
        $('.weibo-popup,.wechat-popup').click(function() {
            var popupId = $(this).attr('popup-id')
            $('#' + popupId).show();
        });
        $('#closed-weibo,#closed-wechat').click(function() {
            var popupId = $(this).attr('close-pop-id')
            $('#' + popupId).hide();
        });
        /* Add for China Store Popup : JM */

    });

    /** Add Menu : AR */

    $(window).on('orientationchange resize', function() {
        var height = window.innerHeight - $('.page-header').height() - $('.menu-footer').height() - 10;
        $('.dl-menuwrapper .dl-menu').css('max-height', height);
        $('.dl-menuwrapper .dl-menu').css('height', height);
    });
    var scroll = 1;
    $('.nav-toggle').on('click', function(event) {
        $(".form.minisearch").removeClass('active');
        $(this).toggleClass('mobile-menu');
        //$('.dl-menuwrapper .dl-menu').toggleClass('dl-menuopen', 700);
        $('.dl-menuwrapper .dl-menu').toggleClass('dl-menuopen');
        $('.custom-level').addClass('active');
        $('body').addClass('custom-body');
        $(this).css({"cursor": "wait","pointer-events": "none"});
        $('.custom-body .nav-sections-items').css('z-index', '1');
        setTimeout(function() {
            $('.nav-toggle').css({"cursor": "pointer","pointer-events": "auto"});
            if (!$(".dl-menuwrapper").find('.dl-menu').hasClass('dl-menuopen')) {
                $('.custom-level').removeClass('active');
                $('body').removeClass('custom-body');
                $('ul.dl-menu').removeClass('dl-subview');
                $('ul.dl-menu li').removeClass('dl-subview dl-subviewopen');
            }

        }, 710);
        var $menu;
        if (scroll == 1) {
            scroll++;
            scrollLock = 1;
            $menu = 'openMenu';
        } else {
            scrollLock = '';
            scroll--;
            $menu = 'closeMenu';
        }
        /** [Start for onload open menu] */
        $.ajax({
            url : BASE_URL + 'mainadmin/index/cookieData',
            type : 'POST',
            data: {
                "create": $menu
            },
            success : function(data) {
            }
        });
         /** [End for onload open menu] */
    });

     /** [Start for onload open menu] */
    if ($(window).width() <= 1000) {
        setTimeout(function() {
            if ($.cookie("switcher") == 'switched') {
                if ($.cookie("menu") == 'opened') {
                    $('.nav-toggle').trigger('click');
                }
            } else {
                $.ajax({
                    url : BASE_URL + 'mainadmin/index/cookieData',
                    type : 'POST',
                    data: {
                        "onload": 'destroy'
                    },
                    success : function(data) {
                    }
                });
            }
        },1000);
    }
     /** [End for onload open menu] */

    var previousScrollTop = 0, scrollLock;
    $(window).on("scroll", function(event) {
        if ($(window).scrollTop() > 35) {
            if(scrollLock) {
                $(window).scrollTop(previousScrollTop);
            }
        }
        previousScrollTop = $(window).scrollTop();
    });
    /** Add Menu : AR */

    var nav = $('.sections.nav-sections');
    var navtop = nav.offset().top;
    var mobilestick = $('.header.content').offset().top;

    if ($(window).width() <= 1000) {
        if (!$.cookie('desktop')) {
            $(function() {
                $('#dl-menu').dlmenu({
                    animationClasses: {
                        classin: 'dl-animate-in-2',
                        classout: 'dl-animate-out-2'
                    }
                });
            });
            $(".dl-back").click(function() {
                $('ul.dl-menu').addClass('dl-animate-in-2');
                var subviewopen = $(this).parents(".dl-menuwrapper").find('.dl-subviewopen');
                $submenu = subviewopen.find('ul.dl-submenu:first');
                $flyin = $submenu.clone().insertAfter($('ul.dl-menu'));
                var onAnimationEndFn = function() {
                    $flyin.remove();
                };
                $flyin.addClass('dl-animate-out-2');

                if (subviewopen.parents('ul:first').hasClass('dl-menu')) {
                    var subview = subviewopen.parents('ul:first');
                    var submenutext = $(".menu-title").attr('name');
                    $('.dl-back').css({
                        'opacity': '0',
                        'display': 'none'
                    });
                } else {
                    var subview = $(this).parents(".dl-menuwrapper").find('li.dl-subview');
                    var submenutext = subview.attr('name');
                    $('.dl-back').css({
                        'display': 'block'
                    });
                }
                $('.dl-back ,.menu-title span').css({"cursor": "wait","pointer-events": "none"});
                setTimeout(function() {
                    $('ul.dl-menu').removeClass('dl-animate-in-2');
                    onAnimationEndFn.call();
                    $('.dl-menu').addClass('dl-menuopen');
                    subview.show("slide", {
                        direction: "left"
                    }, 'slow');
                    $('.dl-back').css({"cursor": "pointer","pointer-events": "auto"});
                    if (subviewopen.parents('ul:first').hasClass('dl-menu')) {
                        $('.menu-title span').css({"cursor": "wait","pointer-events": "none"});
                    } else {
                        $('.menu-title span').css({"cursor": "pointer","pointer-events": "auto"});
                    }
                }, 1000);

                subviewopen.removeClass('dl-subviewopen');
                $(this).parents(".dl-menuwrapper").find('ul.dl-menu').addClass('dl-menuopen');
                subview.addClass('dl-subviewopen');
                subview.removeClass('dl-subview');
                $(".menu-title").fadeIn(1000);
                $(".menu-title span").text(submenutext);
            });
            $(".dl-menuwrapper li").click(function() {
                $(".menu-title").fadeIn(1000);
                $(".menu-title span").text($(this).attr('name'));
            });
            $(".menu-title span").click(function() {
                $( ".dl-back" ).trigger( "click" );
            });
            $(window).on("scroll", function() {
                if ($(window).scrollTop() > mobilestick || $('body').hasClass('fotorama__fullscreen')) {
                    $('.header.content').addClass('mobile-sticky-menu');
                    $('.custom-level').addClass('mobile-sticky-navigation');
                    $('.page-header .switcher ,.view-content').hide();
                } else {
                    $('.header.content').removeClass('mobile-sticky-menu');
                    $('.page-header .switcher ,.view-content').show();
                    $('.custom-level').removeClass('mobile-sticky-navigation');
                }
            });
        }
    } else {
        $(window).on("scroll", function() {
            if ($(window).scrollTop() >= navtop) {
                nav.addClass('sticky-menu');
            } else {
                nav.removeClass('sticky-menu');
            }
        });
    }
    /** Add Menu : AR */
});