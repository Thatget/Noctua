/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
define([
    'jquery',
    'uiComponent',
    'ko'
    ], function($, Component, ko) {
    return Component.extend({
        defaults: {
            mainVideoCollArray: ko.observableArray([]),
        },
        initialize: function (config) {
            self = this;
            this._super();
            self.mainVideoCollArray(config.collectionJson);
            var path = 'mainpagevideo/index/mainvideoajax';
            self.callAjax(path);
        },

        mainVideoFunction : function(response){
            self.mainVideoCollArray(response);
        },

        callAjax: function(path){
            $.ajax({  
                url: BASE_URL + path,
                showLoader:true,
            }).done(function(response) {
                self.mainVideoFunction(response);
            });  
        }
    });
});