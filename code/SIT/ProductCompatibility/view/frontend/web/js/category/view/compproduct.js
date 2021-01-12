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
            template: 'SIT_ProductCompatibility/category/view/compproduct',
            productList: ko.observableArray([]),
        },
        initialize: function(config) {
            this._super();
            var self = this;

            if($("a[data-id='tab-label-comp-title-content']").hasClass("active")){
                self.callAjax();
            }
            /**
             * Call ajax on click of "COMPATIBILITY BY Product" tab
             */
            $("a[data-id='tab-label-comp-title-content']").click(function(){
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
        setData: function(){
            var self = this;
            var data={compproduct_collection: true};
            storage.post(
                this.controllerUrl,
                JSON.stringify(data)
            ).done(
                function(response) {
                    /**
                     * Set product collection
                     */
                    self.productList(response);
                    body.loader('hide');
                }
            ).fail(
                function(response) {
                }
            );
        },
    });
});