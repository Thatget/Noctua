require([
  'jquery',
  'jquery/ui'
],function($){
    'use strict';

    // MJ : for news letter ajax request
    var count = 0;
    $('.news-letter-name').click(function(e){
      e.stopPropagation();
      count++;
      $('.focus').show();
      $('.noctua-popup').show();
      if(count == 1){
        callNewsletterAjax();
      }
    });

    $('.popup-close-button').click(function(){
        $('.noctua-popup').hide();
        $('.focus').hide();
        $('.comp-mainboard, .comp-cpu, .comp-socket, .service-case-comp, .complist-case-cooler, .complist-cpu, .mainboard-custom-router-content, .ram-custom-router-content').css('overflow','auto');
    });

    /*Changed by MD for open newsletter popup with parameter[START][13-07-2019]*/
    $(document).ready(function(){
      let searchParams = new URLSearchParams(window.location.search)
      let param = searchParams.get('param')
      if(param == 'newsletter') {
        callNewsletterAjax();
      }
    });
    /*Changed by MD for open newsletter popup with parameter[END][13-07-2019]*/

    $(document).on("click", function(e) {
      var popup = $('.noctua-popup');
      if (!popup.is(e.target) && popup.has(e.target).length === 0) {
        $(".noctua-popup").hide();
        $('.focus').hide();
      }
    });

    $(document).on('keydown', function(event) {
       if (event.key == "Escape") {
            $('.noctua-popup').hide();
            $('.focus').hide();
            $('.comp-mainboard, .comp-cpu, .comp-socket, .service-case-comp, .complist-case-cooler, .complist-cpu, .mainboard-custom-router-content, .ram-custom-router-content').css('overflow','auto');
       }
    });


    function callNewsletterAjax(){
        $.ajax({
          url: BASE_URL + "newsletterpopup/index/newsletter",
          showLoader: true,
        }).done(function(response) {
            $(response.output).appendTo('body');
            $('.comp-mainboard, .comp-cpu, .comp-socket, .service-case-comp, .complist-case-cooler, .complist-cpu, .mainboard-custom-router-content, .ram-custom-router-content').css('overflow','hidden');
            return response;
        });
    }
});