define([
    'ko',
    'uiComponent',
    'mage/url',
    'mage/storage',
    'jquery'
], function(ko, Component, urlBuilder,storage,$) {
    'use strict';
    var body = '', preventMultipleClick = 0,socketId = 0,manuFId = 0,mainScope,clickFlag = 0;
    return Component.extend({
        defaults: {
            template: 'SIT_ProductCompatibility/product/view/compmainboard',
            mainboardList: ko.observableArray([]),
            mainboardGrid: ko.observableArray([]),
        },
        initialize: function(config) {
            this._super();
            var self = this;
            mainScope = self;
            if($("[data-id='tab-label-comp-title-content']").hasClass("active")){
                self.callAjax();
            }
            // Call ajax on click of "COMPATIBILITY BY MAINBOARD" tab
            $("[data-id='tab-label-comp-title-content']").click(function() {
                self.callAjax();
            });

            var currentUrl = history.state.Url;
            var str_pos = currentUrl.indexOf("comp#socket_0");
            if (str_pos > -1) {
              mainScope.getAllSocket();
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
                    // Set mainboard collection
                    self.mainboardList(response);
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
             * Get data of particular socket on load if hash parameter exists
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
         * Call on click of Socket column
         * @param  {[type]} socket_id [description]
         * @return {[type]}           [description]
         */
        getSocket: function(socket_id) {
            $(".product-service-mainboard-content .socket-title").removeClass('selected');
            $('span[data-id=socket_'+socket_id.comp_socket+']').addClass('selected');
            if (socket_id.comp_socket != socketId) {
                socketId = socket_id.comp_socket;
                manuFId = 0;
                body = $('body').loader();
                body.loader('show');
                mainScope.setGrid();
            } else {
                var element = "#socket_"+socketId;
                history.pushState(null,null,element);
                var top = $(element).offset().top - 60;
                $('body,html').animate({scrollTop:top}, 'slow');
            }
        },


        getAllSocket: function() {
            $(".product-service-mainboard-content .socket-title").removeClass('selected');
            $('.main-comp-all-data').addClass('selected');
            manuFId = 0;
            socketId = 0;
            body = $('body').loader();
            body.loader('show');
            mainScope.setAllGridData();
        },

        /**
         * Call on click of Manufacture column
         * @param  {[type]} socket_id [description]
         * @param  {[type]} manuF     [description]
         * @return {[type]}           [description]
         */
        getManufacture: function(socket_id,manuF) {
            $(".product-service-mainboard-content .socket-title").removeClass('selected');
            $('span[data-id=socket_'+socket_id+']').addClass('selected');
            if (socket_id != socketId) {
                socketId = socket_id
                manuFId = manuF.comp_manufacture;
                body = $('body').loader();
                body.loader('show');
                mainScope.setGrid();
            } else {
                var element = "#socket_"+socketId+'_manuf_'+manuF.comp_manufacture;
                history.pushState(null,null,element);
                var top = $(element).offset().top - 60;
                $('body,html').animate({scrollTop:top}, 'slow');
            }
        },

        setGrid:function(){
            var self = this;
            var data = {productID: this.currentProductId, set_model: true,comp_socket_id:socketId};
            storage.post(
                this.controllerUrl,
                JSON.stringify(data)
            ).done(
                function(response) {
                    // Set mainboard grid collection
                    mainScope.mainboardGrid(response);
                    body.loader('hide');

                    if (manuFId!=0) {
                        var element = "#socket_"+socketId+'_manuf_'+manuFId;
                    } else {
                        var element = "#socket_"+socketId;
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
            var data = {productID: this.currentProductId, set_model: true,comp_socket_id:'all'};
            storage.post(
                this.controllerUrl,
                JSON.stringify(data)
            ).done(
                function(response) {
                    // Set mainboard grid collection
                    mainScope.mainboardGrid(response);
                    body.loader('hide');
                    $('.main-comp-all-data').addClass('selected');

                    if (manuFId!=0) {
                        var element = "#socket_"+socketId+'_manuf_'+manuFId;
                    } else {
                        var element = "#socket_"+socketId;
                    }

                    history.pushState(null,null,element);
                    setTimeout(function(){
                        var top = $('.socket-table-head').offset().top - 60;
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