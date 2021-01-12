define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'Magento_Ui/js/modal/modal',
    'domReady'
], function (_, uiRegistry, select, modal) {
    'use strict';

    return select.extend({

        initialize: function () {
            this._super();
            this.dropdownChange(this.value());
            return this;
        },

        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {
            this.dropdownChange(value);
            return this._super();
        },

        dropdownChange: function(value)
        {
            var page_name = uiRegistry.get('index = cms_page');
            if(value == 'CMS') {
                page_name.show();
            } else {
                page_name.hide();
            }

            var category_name = uiRegistry.get('index = category');
            if(value == 'Category') {
                category_name.show();
            } else {
                category_name.hide();
            }

            var product_sku = uiRegistry.get('index = sku');
            if(value == 'Product') {
                product_sku.show();
            } else {
                product_sku.hide();
            }

            var custom_page = uiRegistry.get('index = custom_page');
            if(value == 'Custom') {
                custom_page.show();
            } else {
                custom_page.hide();
            }
            return this;
        }
    });
});