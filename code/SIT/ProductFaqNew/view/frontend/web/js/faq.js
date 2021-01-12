require(['jquery'], function($) {
    $(document).ready(function() {

        var pathname = window.location.pathname.split("/");
        var filtered_path = pathname.filter(function(v) {
            return v !== ''
        });
        var filename = filtered_path[filtered_path.length - 1];
        $("div.categories-list li").each(function(i, e) {
            $(this).find('div a').attr('data-level', $(this).parents("ul.sub").length);
            var padding_count = $(this).find('div a').data('level') * 5;
            $(this).find('div').css('padding-left', padding_count + "%");
        });
        if (window.location.pathname.indexOf('quename') != -1) {
            var url = window.location.pathname;
            var arr = url.split('/');
            var que_name = arr[arr.indexOf('quename') + 1];
            $('#faq-search').val(que_name);
        }
        if (window.location.pathname.indexOf('category_id') != -1) {
            if (filename != "category_id") {
                var url = window.location.pathname;
                var arr = url.split('/');
                var category_value = arr[arr.indexOf('category_id') + 1];
                $("div.categories-list li[data-id = '" + category_value + "']").parents('ul.sub').css('display', 'block');
                $("div.categories-list li[data-id = '" + category_value + "']").parents('ul.sub').parent('li').find('span').toggleClass('sit-minus-icon').toggleClass('sit-plus-icon');
                $("div.categories-list li[data-id = '" + category_value + "']").find("div[data-id = '" + category_value + "']").addClass('faq-active');
                $("div.categories-list li[data-id = '" + category_value + "']").find('div[data-id = ' + category_value + '] span').addClass('sit-plus-minus-white-content');
            }
        } else {
            $("div.categories-list li[data-id = '0']").find("div[data-id = '0']").addClass('faq-active');
        }
        if (window.location.pathname.indexOf('faq') != -1) {
            if (filename != "faq") {
                $("#ans_" + filename).slideToggle(500);
                $("div.faq_ans").not("#ans_" + filename).slideUp(500);
                $('div.faq_que a#' + filename).prev('div').toggleClass('faq_que_toggle_plus').toggleClass('faq_que_toggle_minus');
            }
        }

        $('div.categories-list li div > span').click(function() {
            $.fn.openCategory(this);
        });
        $.fn.openCategory = function(thisdata) {
            var main_category = $(thisdata).parent().data('id');
            var url = window.location.pathname;
            var arr = url.split('/');
            var category_value = arr[arr.indexOf('category_id') + 1];
            if ($(thisdata).parent().next("ul.sub").length > 0) {
                $('ul.sub', $(thisdata).parent().parent()).eq(0).toggle();
                $(thisdata).toggleClass('sit-minus-icon').toggleClass('sit-plus-icon');
                $('div.categories-list li > a').each(function(i, e) {
                    $(thisdata).parent('div').removeClass('faq-active');
                });
                $(thisdata).parent("div[data-id = '" + category_value + "']").addClass('faq-active');
                return false;
            } else {
                if (main_category != 0) {
                    $.ajax({
                      url: $("#faq-search-url").val(),
                      dataType: "json",
                      data: {queid: null,quename: null,category_id:main_category},
                      showLoader: true,
                      success: function( data ) {
                        $('div.categories-content').html('');
                        $('div.categories-content').html(data.output);
                      }
                    });
                } else {
                    window.location.href = $('#faq-url').val();
                }
            }
        }
        $('div.categories-list li div > a').click(function() {
            $("#faq-search").val('');
            var main_category = $(this).parent().data('id');
            $('div.categories-list li div').removeClass('faq-active');
            $(this).parent("div[data-id='"+main_category+"']").addClass('faq-active');
            if($(this).prev('span'))
            {
                $('div.categories-list li div span').removeClass('sit-plus-minus-white-content');
                $(this).prev('span').addClass('sit-plus-minus-white-content');

            }
            if($(this).prev('span').hasClass('sit-plus-minus-white-content')) {
                $.fn.openCategory(this);
                if($(this).prev('span').hasClass('sit-plus-icon')) {
                    $(this).prev('span').removeClass('sit-plus-icon');
                    $(this).prev('span').addClass('sit-minus-icon');
                } else {
                    $(this).prev('span').removeClass('sit-minus-icon');
                    $(this).prev('span').addClass('sit-plus-icon');
                }
            }
            if (main_category != 0) {
                $.ajax({
                  url: $("#faq-search-url").val(),
                  dataType: "json",
                  data: {queid: null,quename: null,category_id:main_category},
                  showLoader: true,
                  success: function( data ) {
                    $('div.categories-content').html('');
                    $('div.categories-content').html(data.output);
                    var new_url = $('#faq-url').val() + "category_id/" + main_category;
                    window.history.pushState(null, null, new_url);
                  }
                });
            } else {
                $.ajax({
                  url: $("#faq-search-url").val(),
                  dataType: "json",
                  data: {queid: null,quename: null,category_id:null},
                  showLoader: true,
                  success: function( data ) {
                    $('div.categories-content').html('');
                    $('div.categories-content').html(data.output);
                    var new_url = $('#faq-url').val();
                    window.history.pushState(null, null, new_url);
                  }
                });
            }
        });


        $('div.categories-list li > a').each(function(i, e) {
            $(this).attr('data-level', $(this).parents("ul.sub").length);
        });
        $("body").on("click", ".faq_que a", function(event){
            var anchor_id = $(this).attr('id');
            $("#ans_" + anchor_id).slideToggle(500);
            $("div.faq_ans").not("#ans_" + anchor_id).slideUp(500);
            $('div.faq_que a#' + anchor_id).prev('div').toggleClass('faq_que_toggle_plus').toggleClass('faq_que_toggle_minus');
            var url = window.location.pathname;
            var arr = url.split('/');
            var faq_index = arr[arr.indexOf('faq')];
            var faq_value = arr[arr.indexOf('faq') + 1];
            if (faq_value != "") {
                var new_url = window.location.href.replace("faq/" + faq_value, "faq/" + anchor_id);
                window.history.pushState(null, null, new_url);
            } else {
                if(window.location.href.substr(-1) != "/"){
                    var element = window.location.href+"/faq/" + anchor_id;
                } else {
                    var element = window.location.href+"faq/" + anchor_id;
                }
                window.history.pushState(null, null, element);
            }
        });
    });
});