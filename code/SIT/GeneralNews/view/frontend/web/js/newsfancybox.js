require(['jquery','fancybox'], function($) {
   	$(document).ready(function() {
         $('[data-fancybox="fancy_imgview"]').fancybox({
              arrows: true,
              infobar: true,
              keyboard: true,
              buttons : [
                 'fullScreen',
                 'close'
               ],
               thumbs : {
                  autoStart : false
               }
         });
    	});
});