define([
    'ko',
    'uiComponent',
    'mage/url',
    'mage/storage',
    'jquery'
], function(ko, Component, urlBuilder,storage,$) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'SIT_ProductFaqNew/servicefaq',
            faqCollection: ko.observableArray([]),
            currentUrl: ko.observableArray([]),
        },

        initialize: function (config) {
            this._super();
            var self = this;
            var preventMultiClick = 0;
            $('#tab-label-faq-title').on('click',function(){
                preventMultiClick++;
                if(preventMultiClick == 1){
                   self.ajaxCall(config);
                }
            });
            if($('#tab-label-faq-title').hasClass('active')){
                preventMultiClick++;
                if(preventMultiClick == 1){
                    self.ajaxCall(config);
                }
            }
        },

        ajaxCall: function(config){
            var self = this;
            $.ajax({
                type: "POST",
                url: BASE_URL + "productfaqs/productfaq/faqview",
                data: { 'id' : config.currentId },
                showLoader: true,
                success: function(response){
                    self.faqCollection(response);
                    $('.product-service-faq-content img#loader').hide();
                    setTimeout(function(){
                        if (window.location.hash) {
                            var loadele = window.location.hash;
                            var offTop = $(loadele).offset().top - 80;
                            $('body,html').animate({scrollTop:offTop}, 'slow');
                        }
                    }, 800);
                    $('.faq-question-title a').click( function() {
                        var element = $(this).attr('id');
                        history.pushState(null,null,element);
                        var top = $(element).offset().top - 100;
                        $('body,html').animate({scrollTop:top}, 'slow');
                    });
                }
            });
        }

    });
});