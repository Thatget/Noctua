define([
    'ko',
    'uiComponent',
    'mage/url',
    'mage/storage',
    'jquery'
], function(ko, Component, urlBuilder,storage,$) {
    'use strict';
    var body = '', preventMultipleClick = 0,manufacturerId = 0,mainScope,modelId = 0,clickFlag = 0;
    return Component.extend({
        defaults: {
            template: 'SIT_ProductCompatibility/category/view/supportcompcase',
            caseList: ko.observableArray([]),
            caseGrid: ko.observableArray([]),
        },
        initialize: function(config) {
            this._super();
            var self = this;
            mainScope = self;
            if($("[data-id='tab-label-case-title-content']").hasClass("active")){
                self.callAjax();
            }

            // Call ajax on click of "COMPATIBILITY BY CASE" tab
            $("[data-id='tab-label-case-title-content']").click(function(){
                self.callAjax();
            });

            var currentUrl = history.state.Url;
            var str_pos = currentUrl.indexOf("compatibility-lists/case#manuf_0");
            if (str_pos > -1) {
              mainScope.getAllManufacturer();
            } 
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
            var data = {productID: this.currentProductId};
            storage.post(
                this.controllerUrl,
                JSON.stringify(data)
            ).done(
                function(response) {
                    // Set case collection
                    self.caseList(response);
                    body.loader('hide');
                }
            ).fail(
                function(response) {
                }
            );
        },

        /**
         * Load data by url parameter
         * @return {[type]} [description]
         */
        loadData: function(){
            /**
             * Get data of particular manufacturer on load if hash parameter exists
             * @type {[type]}
             */
            var param = window.location.hash;
            if (param != ""){
            var ele=document.getElementById(param);
                if(ele && clickFlag == 0) {
                    clickFlag = 1;
                    document.getElementById(param).click();
                }
            }
        },
        /**
         * call on click of Model
         * @param  {[type]} manufacturer_id [description]
         * @param  {[type]} model_id       [description]
         * @return {[type]}                 [description]
         */
        getModel: function(manufacturer_id,model_id) {
            $(".complist-case-cooler .complist-case-name").removeClass('selected');
            $('div[data-id=manuf_'+manufacturer_id+']').addClass('selected');
            if (manufacturer_id != manufacturerId) {
                manufacturerId = manufacturer_id
                modelId = model_id.comp_model;
                body = $('body').loader();
                body.loader('show');
                mainScope.setGrid();
            } else {
                var element = "#manuf_"+manufacturerId+'_model_'+model_id.comp_model;
                history.pushState(null,null,element);
                var top = $(element).offset().top - 60;
                $('body,html').animate({scrollTop:top}, 'slow');
            }
        },

        /**
         * call on click of  Manufacturer
         * @param  {[type]} manufacturer_id [description]
         * @return {[type]}                 [description]
         */
        getManufacturer: function(manufacturer_id) {
            $(".complist-case-cooler .complist-case-name").removeClass('selected');
            $('div[data-id=manuf_'+manufacturer_id.comp_manufacture+']').addClass('selected');
            if (manufacturer_id.comp_manufacture != manufacturerId) {
                manufacturerId = manufacturer_id.comp_manufacture;
                modelId = 0;
                body = $('body').loader();
                body.loader('show');
                mainScope.setGrid();
            } else {
                var element = "#manuf_"+manufacturerId;
                history.pushState(null,null,element);
                var top = $(element).offset().top - 60;
                $('body,html').animate({scrollTop:top}, 'slow');
            }
        },


        getAllManufacturer: function(manufacturer_id) {
            $(".complist-case-cooler .complist-case-name").removeClass('selected');
            $('.casecomp-all-data').addClass('selected');
            body = $('body').loader();
            body.loader('show');
            manufacturerId = 0;
            mainScope.setAllGridData();
        },

        setGrid:function(){
            var self = this;
            var data = {productID: this.currentProductId, set_model: true,comp_manufacturer_id:manufacturerId};
            storage.post(
                this.controllerUrl,
                JSON.stringify(data)
            ).done(
                function(response) {
                    // Set case grid collection
                    mainScope.caseGrid(response);
                    body.loader('hide');

                    // For move to specific position
                    if (modelId!=0) {
                        var element = "#manuf_"+manufacturerId+'_model_'+modelId;
                    } else {
                        var element = "#manuf_"+manufacturerId;
                    }
                    history.pushState(null,null,element);
                    setTimeout(function(){
                        var top = $(element).offset().top - 60;
                        $('body,html').animate({scrollTop:top}, 'slow');
                    },500);
                }
            ).fail(
                function(response) {
                }
            );
        },

        setAllGridData:function(){
            var self = this;
            var data = {productID: this.currentProductId, set_model: true,comp_manufacturer_id:manufacturerId};
            storage.post(
                this.controllerUrl,
                JSON.stringify(data)
            ).done(
                function(response) {
                    // Set case grid collection
                    mainScope.caseGrid(response);
                    body.loader('hide');
                    $('.casecomp-all-data').addClass('selected');

                    // For move to specific position
                    if (modelId!=0) {
                        var element = "#manuf_"+manufacturerId+'_model_'+modelId;
                    } else {
                        var element = "#manuf_"+manufacturerId;
                    }
                    history.pushState(null,null,element);
                    setTimeout(function(){
                        var top = $('.case-head').offset().top - 60;
                        $('body,html').animate({scrollTop:top}, 'slow');
                    },500);
                }
            ).fail(
                function(response) {
                }
            );
        },
    });
});