define([
    'ko',
    'uiComponent',
    'mage/url',
    'mage/storage',
    'jquery'
], function(ko, Component, urlBuilder,storage,$) {
    'use strict';
    var body = '', preventMultipleClick = 0,manufacturerId = 0,mainScope,socketId = 0,seriesId = 0, clickFlag = 0;
    return Component.extend({
        defaults: {
            template: 'SIT_ProductCompatibility/category/view/supportcompcpu',
            cpuHeader: ko.observableArray([]),
            cpuList: ko.observableArray([]),
        },
        initialize: function(config) {
            this._super();
            var self = this;
            mainScope = self;
            /**
             * Call ajax on click of "COMPATIBILITY BY CPU" tab
             */
            if($("[data-id='tab-label-cpu-title-content']").hasClass("active")){
                self.callAjax();
            }
            $("[data-id='tab-label-cpu-title-content']").click(function(){
                self.callAjax();
            });
            var currentUrl = history.state.Url;
            var str_pos = currentUrl.indexOf("compatibility-lists/cpu#manuf_0");
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
        /**
         * call on click of  Manufacturer
         * @param  {[type]} manufacturer_id [description]
         * @return {[type]}                 [description]
         */
        getManufacturer: function(manufacturer_id) {
            $(".complist-cpu .complist-cpu-name").removeClass('selected');
            $('div[data-id=manuf_'+manufacturer_id.comp_manufacture+']').addClass('selected');
            if (manufacturer_id.comp_manufacture != manufacturerId) {
                manufacturerId = manufacturer_id.comp_manufacture;
                socketId = 0;
                seriesId = 0;
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


         getAllManufacturer: function() {
            $(".complist-cpu .complist-cpu-name").removeClass('selected');
            $('.cpucomp-all-data').addClass('selected');
            body = $('body').loader();
            body.loader('show');
            manufacturerId = 0;
            mainScope.setAllGridData();
        },

        /**
         * call on click of Socket
         * @param  {[type]} manufacturer_id [description]
         * @param  {[type]} socket_id       [description]
         * @return {[type]}                 [description]
         */
        getSocket: function(manufacturer_id,socket_id) {
            $(".complist-cpu .complist-cpu-name").removeClass('selected');
            $('div[data-id=manuf_'+manufacturer_id+']').addClass('selected');
            if (manufacturer_id != manufacturerId) {
                manufacturerId = manufacturer_id
                socketId = socket_id.comp_socket;
                seriesId = 0;
                body = $('body').loader();
                body.loader('show');
                mainScope.setGrid();
            } else {
                var element = "#manuf_"+manufacturerId+'_socket_'+socket_id.comp_socket;
                history.pushState(null,null,element);
                var top = $(element).offset().top - 60;
                $('body,html').animate({scrollTop:top}, 'slow');
            }
        },

        /**
         * call on click of Socket
         * @param  {[type]} manufacturer_id [description]
         * @param  {[type]} series_id       [description]
         * @return {[type]}                 [description]
         */
        getSeries: function(manufacturer_id,series_id) {
            $(".complist-cpu .complist-cpu-name").removeClass('selected');
            $('div[data-id=manuf_'+manufacturer_id+']').addClass('selected');
            if (manufacturer_id != manufacturerId) {
                manufacturerId = manufacturer_id
                seriesId = series_id.comp_series;
                socketId = series_id.comp_socket;
                body = $('body').loader();
                body.loader('show');
                mainScope.setGrid();
            } else {
                var element = "#manuf_"+manufacturerId+'_socket_'+series_id.comp_socket+'_cpu_'+series_id.comp_series;
                history.pushState(null,null,element);
                var top = $(element).offset().top - 60;
                $('body,html').animate({scrollTop:top}, 'slow');
            }
        },
        setData:function(){
            var self = this;
            var data={compcpu_collection: true};
            storage.post(
                this.controllerUrl,
                JSON.stringify(data)
            ).done(
                function(response) {
                    self.cpuHeader(response);
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

        setGrid:function(){
            var data={compcpu_collection: true,set_model:true,comp_manufacturer_id:manufacturerId};
            storage.post(
                mainScope.controllerUrl,
                JSON.stringify(data)
            ).done(
                function(response) {
                    /**
                     * Set cpu grid collection
                     */
                    mainScope.cpuList(response);
                    body.loader('hide');

                    /**
                     * For move to specific position
                     */
                    if (seriesId!=0) {
                        var element = "#manuf_"+manufacturerId+'_socket_'+socketId+'_cpu_'+seriesId;
                    } else if (socketId!=0) {
                        var element = "#manuf_"+manufacturerId+'_socket_'+socketId;
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
            var data={compcpu_collection: true,set_model:true,comp_manufacturer_id:manufacturerId};
            storage.post(
                mainScope.controllerUrl,
                JSON.stringify(data)
            ).done(
                function(response) {
                    /**
                     * Set cpu grid collection
                     */
                    mainScope.cpuList(response);
                    body.loader('hide');
                    $('.cpucomp-all-data').addClass('selected');

                    /**
                     * For move to specific position
                     */
                    if (seriesId!=0) {
                        var element = "#manuf_"+manufacturerId+'_socket_'+socketId+'_cpu_'+seriesId;
                    } else if (socketId!=0) {
                        var element = "#manuf_"+manufacturerId+'_socket_'+socketId;
                    } else {
                        var element = "#manuf_"+manufacturerId;
                    }
                    history.pushState(null,null,element);
                    setTimeout(function(){
                        var top = $('.complist-cpu-head').offset().top - 60;
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