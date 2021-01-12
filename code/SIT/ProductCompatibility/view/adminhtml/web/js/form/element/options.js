define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'Magento_Ui/js/form/element/multiselect',
    'Magento_Ui/js/modal/modal'
], function (_, uiRegistry, select, modal) {
    'use strict';
    return select.extend({

        initialize: function (){
            var compstatus = uiRegistry.get('index = status');
            var socket = uiRegistry.get('index = comp_socket');
            var manu = uiRegistry.get('index = comp_manufacture');
            var model = uiRegistry.get('index = comp_model');
            var series = uiRegistry.get('index = comp_series');
            var comp = uiRegistry.get('index = comp_value');
            var type = uiRegistry.get('index = comp_type');
            var products = uiRegistry.get('index = assign_products');
            var changetemp = uiRegistry.get('index = change_temp');
            var template_1 = uiRegistry.get('index = template_text_1');
            var template_2 = uiRegistry.get('index = template_text_2');
            var template_3 = uiRegistry.get('index = template_text_3');
            var template_4 = uiRegistry.get('index = template_text_4');
            var duplicate = uiRegistry.get('index = duplicate');
            var howMany = uiRegistry.get('index = how_many');
            var statusData = this._super().initialValue;

            if (statusData == 'change_socket') {
                if(socket !=  undefined) {
                    socket.show();
                }
                changetemp.hide();
                if(compstatus !=  undefined) {
                    compstatus.hide();
                }
                manu.hide();
                model.hide();
                if(series !=  undefined) {
                    series.hide();
                }
                comp.hide();
                type.hide();
                products.hide();
                template_1.hide();
                template_2.hide();
                template_3.hide();
                template_4.hide();
                duplicate.hide();
                howMany.hide();
            } else if(statusData == 'change_manu'){
                manu.show();
                compstatus.hide();
                changetemp.hide();
                if(socket !=  undefined) {
                    socket.hide();
                }
                model.hide();
                if(series !=  undefined) {
                    series.hide();
                }
                comp.hide();
                type.hide();
                products.hide();
                template_1.hide();
                template_2.hide();
                template_3.hide();
                template_4.hide();
                duplicate.hide();
                howMany.hide();
            } else if(statusData == 'change_model'){
                model.show();
                compstatus.hide();
                changetemp.hide();
                if(socket !=  undefined) {
                    socket.hide();
                }
                manu.hide();
                if(series !=  undefined) {
                    series.hide();
                }
                comp.hide();
                type.hide();
                products.hide();
                template_1.hide();
                template_2.hide();
                template_3.hide();
                template_4.hide();
                duplicate.hide();
                howMany.hide();
            } else if(statusData == 'change_series'){
               series.show();
               compstatus.hide();
               changetemp.hide();
               if(socket !=  undefined) {
                    socket.hide();
               }
               manu.hide();
               model.hide();
               comp.hide();
               type.hide();
               products.hide();
               template_1.hide();
               template_2.hide();
               template_3.hide();
               template_4.hide();
               duplicate.hide();
               howMany.hide();
            } else if(statusData == 'change_comp'){
                comp.show();
                compstatus.hide();
                changetemp.hide();
                if(socket !=  undefined) {
                    socket.hide();
                }
                manu.hide();
                model.hide();
                if(series !=  undefined) {
                    series.hide();
                }
                type.hide();
                products.hide();
                template_1.hide();
                template_2.hide();
                template_3.hide();
                template_4.hide();
                duplicate.hide();
                howMany.hide();
            } else if(statusData == 'change_type'){
                type.show();
                compstatus.hide();
                changetemp.hide();
                if(socket !=  undefined) {
                    socket.hide();
                }
                manu.hide();
                model.hide();
                if(series !=  undefined) {
                    series.hide();
                }
                comp.hide();
                products.hide();
                template_1.hide();
                template_2.hide();
                template_3.hide();
                template_4.hide();
                duplicate.hide();
                howMany.hide();
            } else if(statusData == 'assign_products'){
                products.show();
                compstatus.hide();
                changetemp.hide();
                if(socket !=  undefined) {
                    socket.hide();
                }
                manu.hide();
                model.hide();
                if(series !=  undefined) {
                    series.hide();
                }
                comp.hide();
                type.hide();
                template_1.hide();
                template_2.hide();
                template_3.hide();
                template_4.hide();
                duplicate.hide();
                howMany.hide();
            } else if(statusData == 'change_temp_text'){
                template_1.show();
                template_2.show();
                template_3.show();
                template_4.show();
                compstatus.hide();
                changetemp.hide();
                if(socket !=  undefined) {
                    socket.hide();
                }
                manu.hide();
                model.hide();
                if(series !=  undefined) {
                    series.hide();
                }
                comp.hide();
                type.hide();
                products.hide();
                duplicate.hide();
                howMany.hide();
            } else if(statusData == 'duplicate'){
               duplicate.show();
               howMany.show();
               compstatus.hide();
               changetemp.hide();
               if(socket !=  undefined) {
                    socket.hide();
               }
               manu.hide();
               model.hide();
               if(series !=  undefined) {
                    series.hide();
                }
               comp.hide();
               type.hide();
               products.hide();
               template_1.hide();
               template_2.hide();
               template_3.hide();
               template_4.hide();
            } else if(statusData == 'multi_action'){
                compstatus.show();
                if(socket !=  undefined) {
                    socket.hide();
                }
                manu.show();
                model.show();
                if(series !=  undefined) {
                    series.show();
                }
                comp.show();
                type.hide();
                products.show();
                changetemp.show();
                template_1.show();
                template_2.show();
                template_3.show();
                template_4.show();
                duplicate.hide();
                howMany.hide();
            } else {
               compstatus.hide();
               changetemp.hide();
               if(socket !=  undefined) {
                    socket.hide();
               }
               manu.hide();
               model.hide();
               if(series !=  undefined) {
                    series.hide();
               }
               comp.hide();
               type.hide();
               products.hide();
               template_1.hide();
               template_2.hide();
               template_3.hide();
               template_4.hide();
               duplicate.hide();
               howMany.hide();
            }
            return this;
        },

        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {

            var compstatus = uiRegistry.get('index = status');
            var socket = uiRegistry.get('index = comp_socket');
            var manu = uiRegistry.get('index = comp_manufacture');
            var model = uiRegistry.get('index = comp_model');
            var series = uiRegistry.get('index = comp_series');
            var comp = uiRegistry.get('index = comp_value');
            var type = uiRegistry.get('index = comp_type');
            var products = uiRegistry.get('index = assign_products');
            var changetemp = uiRegistry.get('index = change_temp');
            var template_1 = uiRegistry.get('index = template_text_1');
            var template_2 = uiRegistry.get('index = template_text_2');
            var template_3 = uiRegistry.get('index = template_text_3');
            var template_4 = uiRegistry.get('index = template_text_4');
            var duplicate = uiRegistry.get('index = duplicate');
            var howMany = uiRegistry.get('index = how_many');

            if (value == 'change_socket') {
                if(socket !=  undefined) {
                    socket.show();
                }
                changetemp.hide();
                if(compstatus !=  undefined) {
                    compstatus.hide();
                }
                manu.hide();
                model.hide();
                if(series !=  undefined) {
                    series.hide();
                }
                comp.hide();
                type.hide();
                products.hide();
                template_1.hide();
                template_2.hide();
                template_3.hide();
                template_4.hide();
                duplicate.hide();
                howMany.hide();
            } else if(value == 'change_manu'){
                manu.show();
                compstatus.hide();
                changetemp.hide();
                if(socket !=  undefined) {
                    socket.hide();
                }
                model.hide();
                if(series !=  undefined) {
                    series.hide();
                }
                comp.hide();
                type.hide();
                products.hide();
                template_1.hide();
                template_2.hide();
                template_3.hide();
                template_4.hide();
                duplicate.hide();
                howMany.hide();
            } else if(value == 'change_model'){
                model.show();
                compstatus.hide();
                changetemp.hide();
                if(socket !=  undefined) {
                    socket.hide();
                }
                manu.hide();
                if(series !=  undefined) {
                    series.hide();
                }
                comp.hide();
                type.hide();
                products.hide();
                template_1.hide();
                template_2.hide();
                template_3.hide();
                template_4.hide();
                duplicate.hide();
                howMany.hide();
            } else if(value == 'change_series'){
               series.show();
               compstatus.hide();
               changetemp.hide();
               if(socket !=  undefined) {
                    socket.hide();
               }
               manu.hide();
               model.hide();
               comp.hide();
               type.hide();
               products.hide();
               template_1.hide();
               template_2.hide();
               template_3.hide();
               template_4.hide();
               duplicate.hide();
               howMany.hide();
            } else if(value == 'change_comp'){
                comp.show();
                compstatus.hide();
                changetemp.hide();
                if(socket !=  undefined) {
                    socket.hide();
                }
                manu.hide();
                model.hide();
                if(series !=  undefined) {
                    series.hide();
                }
                type.hide();
                products.hide();
                template_1.hide();
                template_2.hide();
                template_3.hide();
                template_4.hide();
                duplicate.hide();
                howMany.hide();
            } else if(value == 'change_type'){
                type.show();
                compstatus.hide();
                changetemp.hide();
                if(socket !=  undefined) {
                    socket.hide();
                }
                manu.hide();
                model.hide();
                if(series !=  undefined) {
                    series.hide();
                }
                comp.hide();
                products.hide();
                template_1.hide();
                template_2.hide();
                template_3.hide();
                template_4.hide();
                duplicate.hide();
                howMany.hide();
            } else if(value == 'assign_products'){
                products.show();
                compstatus.hide();
                changetemp.hide();
                if(socket !=  undefined) {
                    socket.hide();
                }
                manu.hide();
                model.hide();
                if(series !=  undefined) {
                    series.hide();
                }
                comp.hide();
                type.hide();
                template_1.hide();
                template_2.hide();
                template_3.hide();
                template_4.hide();
                duplicate.hide();
                howMany.hide();
            } else if(value == 'change_temp_text'){
                template_1.show();
                template_2.show();
                template_3.show();
                template_4.show();
                compstatus.hide();
                changetemp.hide();
                if(socket !=  undefined) {
                    socket.hide();
                }
                manu.hide();
                model.hide();
                if(series !=  undefined) {
                    series.hide();
                }
                comp.hide();
                type.hide();
                products.hide();
                duplicate.hide();
                howMany.hide();
            } else if(value == 'duplicate'){
               duplicate.show();
               howMany.show();
               compstatus.hide();
               changetemp.hide();
               if(socket !=  undefined) {
                    socket.hide();
               }
               manu.hide();
               model.hide();
               if(series !=  undefined) {
                    series.hide();
                }
               comp.hide();
               type.hide();
               products.hide();
               template_1.hide();
               template_2.hide();
               template_3.hide();
               template_4.hide();
            } else if(value == 'multi_action'){
                compstatus.show();
                if(socket !=  undefined) {
                    socket.hide();
                }
                manu.show();
                model.show();
                if(series !=  undefined) {
                    series.show();
                }
                comp.show();
                type.hide();
                products.show();
                changetemp.show();
                template_1.show();
                template_2.show();
                template_3.show();
                template_4.show();
                duplicate.hide();
                howMany.hide();
            } else {
               compstatus.hide();
               changetemp.hide();
               if(socket !=  undefined) {
                    socket.hide();
               }
               manu.hide();
               model.hide();
               if(series !=  undefined) {
                    series.hide();
               }
               comp.hide();
               type.hide();
               products.hide();
               template_1.hide();
               template_2.hide();
               template_3.hide();
               template_4.hide();
               duplicate.hide();
               howMany.hide();
            }
            return this._super();
        },
    });
});