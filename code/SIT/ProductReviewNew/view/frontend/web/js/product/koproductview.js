define([
    'ko',
    'uiComponent',
    'mage/url',
    'mage/storage',
    'jquery'
], function(ko, Component, urlBuilder,storage,$) {
    'use strict';
    var body = '', preventMultipleClick = 0;
    return Component.extend({
        defaults: {
            template: 'SIT_ProductReviewNew/product/koproductview',
            productReviewList: ko.observableArray([]),
        },
        initialize: function(config) {
            this._super();
            var self = this;
            if($("#tab-label-review").hasClass("active")){
                self.callAjax();
            }
            $("#tab-label-review").click(function(){
                self.callAjax();
            });
        },
        callAjax: function() {
            var self = this;
            if (preventMultipleClick == 0) {
                preventMultipleClick = 1;
                self.setData();
                body = $('body').loader();
                body.loader('show');
            }
        },
        setData:function(){
            var self = this;
            var data={product_id: this.productId};
            storage.post(
                this.controllerUrl,
                JSON.stringify(data),
            ).done(
                function(response) {
                    self.productReviewList(response);
                    body.loader('hide');
                    setTimeout(function(){
                        if (window.location.hash) {
                            var loadele = window.location.hash;
                            var offTop = $(loadele).offset().top - 100;
                            $('body,html').animate({scrollTop:offTop}, 'slow');
                        }
                    }, 800);
                    var windowHeight = $('.column.main').height();
                    $('.background-image').css('min-height', windowHeight);
                }
            ).fail(
                function(response) {
                }
            );
        },
        getReviewPageUrl: function(){
            return this.reviewPageUrl;
        }
    });
});