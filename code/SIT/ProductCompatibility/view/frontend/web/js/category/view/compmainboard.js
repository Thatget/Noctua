define([
    'ko',
    'uiComponent',
    'mage/url',
    'mage/storage',
    'jquery'
], function(ko, Component, urlBuilder,storage,$) {
    'use strict';
    var body = '', preventMultipleClick = 0,socketId = 0,manuFId = 0,mainScope, clickFlag = 0;
    return Component.extend({
        defaults: {
            template:'SIT_ProductCompatibility/category/view/compmainboard',
            mainboardHeader: ko.observableArray([]),
            mainboardList: ko.observableArray([]),
        },
        initialize: function(config) {
            this._super();
            var self = this;
            mainScope = this;
            /**
             * Call ajax on click of "COMPATIBILITY BY MAINBOARD" tab
             */
            if($("[data-id='tab-label-mainboard-title-content']").hasClass("active")){
                self.callAjax();
            }
            $("[data-id='tab-label-mainboard-title-content']").click(function(){
                self.callAjax();
            });


            var currentUrl = history.state.Url;
            var str_pos = currentUrl.indexOf("compatibility-lists/mainboard#socket_0");
            if (str_pos > -1) {
              mainScope.getAllSocket();
            } 
        },
        callAjax: function() {
            var self = this;
            if (preventMultipleClick == 0) {
                preventMultipleClick = 1;
                mainScope.setData();
                body = $('body').loader();
                body.loader('show');
            }
        },

        /**
         * Set data on load for only list of socket & manufacture
         */
        setData:function(){
            var self = this;
            var data={compmainboard_collection: true};
            storage.post(
                this.controllerUrl,
                JSON.stringify(data)
            ).done(
                function(response) {
                    /**
                     * Set mainboard collection
                     */
                    self.mainboardHeader(response);
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
         * Call on click of Manufacture column
         * @param  {[type]} socket_id [description]
         * @param  {[type]} manuF     [description]
         * @return {[type]}           [description]
         */
        getManufacture: function(socket_id,manuF) {
            var ele = 'socket_'+socket_id;

            $(".comp-mainboard .socket-name").removeClass('selected');
            $('div[data-id='+ele+']').addClass('selected');
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

        /**
         * Call on click of Socket column
         * @param  {[type]} socket_id [description]
         * @return {[type]}           [description]
         */
        getSocket: function(socket_id) {
            var ele = 'socket_'+socket_id.comp_socket;
            $(".comp-mainboard .socket-name").removeClass('selected');
            $('div[data-id='+ele+']').addClass('selected');

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

        getAllSocket: function(socket_id) {
            $(".comp-mainboard .socket-name").removeClass('selected');
            $('.maincomp-all-data').addClass('selected');
            body = $('body').loader();
            body.loader('show');
            socketId = 0;
            mainScope.setAllGridData();
        },

        setGrid:function(){
            var data={compmainboard_collection: true,set_model:true,comp_socket_id:socketId};
            storage.post(
                mainScope.controllerUrl,
                JSON.stringify(data)
            ).done(
                function(response) {
                    /**
                     * Set mainboard grid collection
                     */
                    mainScope.mainboardList(response);
                    body.loader('hide');

                    /**
                     * For move to specific position
                     */
                    $('.mainboard-bottom-content').show(); // enable bottom content after page load
                    if (manuFId!=0) {
                        var element = "#socket_"+socketId+'_manuf_'+manuFId;
                    } else {
                        var element = "#socket_"+socketId;
                    }
                    history.pushState(null, null, element);
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
            var data={compmainboard_collection: true,set_model:true,comp_socket_id:socketId};
            storage.post(
                mainScope.controllerUrl,
                JSON.stringify(data)
            ).done(
                function(response) {
                    /**
                     * Set mainboard grid collection
                     */
                    mainScope.mainboardList(response);
                    body.loader('hide');
                    $('.maincomp-all-data').addClass('selected');

                    /**
                     * For move to specific position
                     */
                    $('.mainboard-bottom-content').show(); // enable bottom content after page load
                    if (manuFId!=0) {
                        var element = "#socket_"+socketId+'_manuf_'+manuFId;
                    } else {
                        var element = "#socket_"+socketId;
                    }
                    history.pushState(null, null, element);
                    setTimeout(function(){
                        var top = $('.socket-head').offset().top - 60;
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