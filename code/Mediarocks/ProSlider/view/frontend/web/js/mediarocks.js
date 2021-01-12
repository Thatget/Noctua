requirejs(
  [
    'jquery',
    'skitter',
    'easing',
    'app'
  ], function($) {

  $(document).ready(function(){
    if ($(window).width() < 1000) {
      $(".skitter-large").skitter({
        responsive: { 
          medium: { 
            animation: 'fade', 
            max_width: 1000
          }
        },
        onLoad: function(){
          $('.slider-loader').remove();
        },
        navigation:true,
        numbers_align:'right',
        label_animation:'fixed'
      });
    }
    else {
      $(".skitter-large").skitter({
        navigation:false,
        onLoad: function(){
          $('.slider-loader').remove();
        },
        label_animation:'fixed'
      });
    }
  });    
});

