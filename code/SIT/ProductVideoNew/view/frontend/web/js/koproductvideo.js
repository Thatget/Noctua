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
            productVideoCollArray: ko.observableArray([]),
        },
        initialize: function (config) {
            self = this;
            this._super();

            var videoCount = 0;
            var path = '';
            $('#tab-label-video-title').on('click',function(){
                videoCount++;
                if(videoCount == 1){
                    path = 'productvideonew/index/productvideo';
                    self.callAjax(path,config.currentProductId);
                }

            });

            if($('#tab-label-video').hasClass('active')){
                videoCount++;
                if(videoCount == 1){
                    path = 'productvideonew/index/productvideo';
                    self.callAjax(path,config.currentProductId);
                }
            }
        },

        productVideoFunction : function(response){
            self.productVideoCollArray(response);
        },

        callAjax: function(path, id){
            $.ajax({  
                url: BASE_URL + path,
                data : {'id' : id},
                showLoader:true,
            }).done(function(response) {
                self.productVideoFunction(response);
            });  
        }
    });
});