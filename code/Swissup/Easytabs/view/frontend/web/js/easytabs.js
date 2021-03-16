define([
    'jquery'
], function ($) {
    'use strict';
    return function (config) {
        var flag_on_load = 0;
        /* Add for tab scroller bar : JM */
        function tabScrolled(scrollWidth)
        {
            var tabLeft = parseInt($('.easytabs-title-items .item.title.active').offset().left) +
                          parseInt($('.easytabs-title-items .item.title.active').outerWidth());
            var arrowLeft = parseInt($('#right_scroll_tabs').offset().left);
            if (tabLeft >= arrowLeft) {
                $(".easytabs-title-items").animate({
                    'marginLeft': '-=' + scrollWidth + 'px'
                }, 'slow');
                $('#right_scroll_tabs').hide();
                $('#left_scroll_tabs').css('display', 'inline-block');
            }

        }
        function tabScroller(flag)
        {
            if ($(window).width() > 1000) {
                var mainElement = $('.easytabs-title-items .data.item.title').length;
                var parentWidth = 0;
                for (var i = 1; i <= mainElement; i++) {
                    var width = parseFloat($('.easytabs-title-items .data.item.title:nth-child(' + i + ')').outerWidth());
                    parentWidth = parentWidth + width + 11;
                }
                if (parentWidth > 812) {
                    $('.custom-tab-slider').css('width', '845px');
                    $('.easytabs-title').css('width', '800px');
                    $('.easytabs-title-items').css('width', parentWidth + 10 + 'px');
                    $('#right_scroll_tabs').css('display', 'inline-block');
                    var scrollWidth = (parentWidth - 810);
                    if(flag !=1) {
                        tabScrolled(scrollWidth);
                        return true;
                    }

                    $(".scroll-arrow").mouseenter(function() {
                        if ($(this).attr('id') == 'right_scroll_tabs') {
                            $(".easytabs-title-items").animate({
                                'marginLeft': '-=' + scrollWidth + 'px'
                            }, 'slow');
                            $(this).hide();
                            $('#left_scroll_tabs').css('display', 'inline-block');
                        } else {
                            $(".easytabs-title-items").animate({
                                'marginLeft': '+=' + scrollWidth + 'px'
                            }, 'slow');
                            $(this).hide();
                            $('#right_scroll_tabs').css('display', 'inline-block');
                        }
                    });
                }
            }
        }
        /* Add for tab scroller bar : JM */

        function changeUrl(title, newurl, tabalias) {
            if (typeof (history.pushState) != "undefined") {
                var obj = {
                    Title: title,
                    Url: newurl
                };

                if (tabalias == '/reviews' || tabalias == '/awards' || tabalias == '/technologies') {
                    history.pushState(obj, obj.Title, BASE_URL + tabalias.replace("/", "").toLowerCase());
                    document.title = title;
                } else {
                    history.pushState(obj, obj.Title, obj.Url);
                    document.title = title;
                }
            } else {
                alert("Browser does not support HTML5.");
            }
        }
        $(function () {
            var tabsContainer = $(config.tabsSelector);
            var links = $(config.linksSelector);
            var CurrentTab, url, newurl, title, currurl, urlValue, current, firstTitle = "";
            var curl = config.currenturl; //get current Url
            if(curl.indexOf('review') > -1){ // when user click from home page review at that time #review value required in url
                  var curl = window.location.href;
            }
             if(window.location.href.indexOf('#') > -1) // when url has #parameter value at that time url should be same
            {
                var curl = window.location.href;
            }
            url = curl.replace("?tab=", "/");
            var lastURLSegment = url.substr(url.lastIndexOf('/') + 1);
            CurrentTab = lastURLSegment;
            tabScroller(1); //add tab arrows scroller.
            tabsContainer.children('[data-role="content"]').each(function (index) {
                /**
                 * Active child tabs on page load
                 */
                if (CurrentTab.indexOf('#') > -1) // when url has params with # sign at that time tab should be active
                        {
                            CurrentTab = CurrentTab.substring(0, CurrentTab.indexOf('#'));
                        }
                $(this).find('[data-role="child-content"]').each(function (cindex) {

                    if (CurrentTab.indexOf('#') > -1) // when url has params with # sign at that time tab should be active
                        {
                            CurrentTab = CurrentTab.substring(0, CurrentTab.indexOf('#'));
                        }
                    if (this.id == CurrentTab) {

                        tabsContainer.tabs('activate', index);
                        $('a[data-url="' + CurrentTab + '"]').trigger('click');
                    }
                });
                if (this.id == CurrentTab) {
                    tabsContainer.tabs('activate', index);
                    tabScroller(); //add tab arrows scroller.
                }

                if (CurrentTab.includes("faq_view")) {
                    tabsContainer.tabs('activate', index);
                }
            });

            /**
             * [Set Page title on page load]
             */
            firstTitle = $('#swissup-easy-tabs .data.item').find('a[href=""]').attr('title');
            title = $('#swissup-easy-tabs .data.item.active').find('a.switch').attr('title');

            if (typeof (firstTitle) != 'undefined') {
                if(title != firstTitle)
                {
                title = firstTitle + " || " + title;
                }
                if (CurrentTab == '/reviews' || CurrentTab == '/awards' || CurrentTab == '/technologies') {
                    title = $('#swissup-easy-tabs .data.item.active').find('a.switch').attr('title');
                }
            }
            else
            {
               if (CurrentTab.indexOf('reviews') > -1) {
                    title = $('#swissup-easy-tabs .data.item.active').find('a.switch').attr('title');
                }
                else{
                title = $("meta[name=title]").attr("content");
            }
        }



            changeUrl(title, url, CurrentTab);

            /* active current url tab */

              current= config.cururlnoparams; //get current url without parameter

            $('.data.switch').on('click', function (e) {

                title = $(this).attr('title');
                urlValue = $(this).attr("href");
                newurl = urlValue.replace("?tab=", "/");

				if (urlValue.indexOf("?tab=") != -1) {

                /**
                 * [Set Page title on tab change]
                 */
                firstTitle = $('#swissup-easy-tabs .data.item').find('a[href=""]').attr('title');
                if (!$(this).parent('.data.item').is(':first-child')) {
                    title = firstTitle + " || " + title;
                    if (newurl == '/reviews' || newurl == '/awards' || newurl == '/technologies') {
                        title = $(this).html();
                    }
                }
                    /* remove query string from faq view url */
                    if(current.indexOf("?faq_view") != -1){
                         current = current.substring(0, current.indexOf("?"));
                    }
                    /* remove query string from faq view url */
                    changeUrl(title, current + newurl, newurl);
				}
            });

            $('#swissup-easy-tabs [role="tabpanel"][aria-hidden="false"]').show();
            links.on('click', function (event) {
                event.preventDefault();
                var anchor = $(this).attr('href').replace(/^.*?(#|$)/, '');

                tabsContainer.children('[data-role="content"]').each(function (index) {
                    if (this.id == anchor) {
                        tabsContainer.tabs('activate', index);
                    }
                });
            });
        });
    };
});

