require(['jquery', 'jquery/ui'], function($) {
    "use strict";
    $(document).ready(function() {
        var counter = 0;
        if ($('#tab-label-awards').hasClass('active') == true) {
            counter++;
            if (counter == 1) {
                callAjax();
            }
        }
        $('#tab-label-awards').on('click', function() {
            counter++;
            if (counter == 1) {
                callAjax();
            }
        });


        function callAjax() {
            $.ajax({
                type: "POST",
                url: BASE_URL + "productreviewnew/productreview/awards",
                showLoader: true,
                success: function(response) {
                    $.each(response.award_coll, function(index, value) {
                        setAwardsData(value,response.media);
                    });
                }
            });
        }

        function setAwardsData(value,media) {
            var loader_url = require.toUrl('images/loading.gif');
            var data = '<div class="award_cell">' + '<div class="award-cell-top">' + '<a target="_blank" href="' + value.review_website_link + '">' + '<img class="lazy-image" src="' + loader_url + '" data-src="' + media+value.review_image + '">' + '</a>' + '</div>' + '<div class="award-cell-bottom">' + '<div class="award-name">' + '<span>' + value.product_name + '</span>' + '</div>' + '<div class="award-title">' + '<strong>' + '<a target="_blank" href="' + value.review_website_link + '">' + '<span>' + value.review_website + '</span>' + '</a>' + '</strong>' + '</div>' + '</div>' + '</div>';
            $(data).appendTo('div.award_container_main_div');
        }
    });
});