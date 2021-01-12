/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

/* global $, $H */

define([
    'mage/adminhtml/grid'
], function () {
    'use strict';
    return function (config) {
        var selectedProducts = config.selectedProducts,
            categoryProducts = $H(selectedProducts),
            gridJsObject = window[config.gridJsObjectName],
            tabIndex = 1000;
        var i = 0;
        var selectedSlide = [],custom = [], editSlide = [];

        /**
         * Set selected Associate product in hidden field: MD[START][26-03-2019]
         */
        var data = Object.toJSON(categoryProducts);
        data = JSON.parse(data);
        // $('slides').value = data;
        for(var j in data){
            custom.push(data[j]);
        }
        $('slides').value = custom;

        selectedSlide = ($('slides').value).split(',');
        /**
         * Set selected Associate product in hidden field: MD[END][26-03-2019]
         */
        
        /**
         * Register Category Product
         *
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerCategoryProduct(grid, element, checked) {
            if (checked) {
                if (element.positionElement) {
                    element.positionElement.disabled = false;
                    categoryProducts.set(element.value, element.positionElement.value);
                }
            } else {
                if (element.positionElement) {
                    element.positionElement.disabled = true;
                }
                categoryProducts.unset(element.value);
            }
            $('slides').value = Object.toJSON(categoryProducts);

            grid.reloadParams = {
                'selected_products[]': categoryProducts.keys()
            };
        }

        /**
         * Click on product row
         *
         * @param {Object} grid
         * @param {String} event
         */
        function categoryProductRowClick(grid, event) {
            var trElement = Event.findElement(event, 'tr'),
                isInput = Event.element(event).tagName === 'INPUT',
                checked = false,
                checkbox = null;
                i++;

            if (trElement) {
                checkbox = Element.getElementsBySelector(trElement, 'input');

                if (checkbox[0]) {
                    checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                    gridJsObject.setCheckboxChecked(checkbox[0], checked);
                    
                    /* Added By MJ [bind data in hidden field] [03.04.2019]*/
                    if(checked === true){
                        if(!selectedSlide){
                          selectedSlide = custom;  
                        } 
                        selectedSlide.push(checkbox[0].value);  
                        $('slides').value = selectedSlide;
                    }
                    if(checked === false){
                      if(selectedSlide){
                        selectedSlide.splice(selectedSlide.indexOf(checkbox[0].value),1);
                        $('slides').value = selectedSlide;
                      }
                    }
                    /* End By MJ [bind data in hidden field] [03.04.2019]*/
                }
            }
        }

        /**
         * Change product position
         *
         * @param {String} event
         */
        function positionChange(event) {
            var element = Event.element(event);

            if (element && element.checkboxElement && element.checkboxElement.checked) {
                categoryProducts.set(element.checkboxElement.value, element.value);
                $('slides').value = Object.toJSON(categoryProducts);
            }
        }

        /**
         * Initialize category product row
         *
         * @param {Object} grid
         * @param {String} row
         */
        function categoryProductRowInit(grid, row) {
            var checkbox = $(row).getElementsByClassName('checkbox')[0],
                position = $(row).getElementsByClassName('input-text')[0];

            if (checkbox && position) {
                checkbox.positionElement = position;
                position.checkboxElement = checkbox;
                position.disabled = !checkbox.checked;
                position.tabIndex = tabIndex++;
                Event.observe(position, 'keyup', positionChange);
            }
        }

        gridJsObject.rowClickCallback = categoryProductRowClick;
        gridJsObject.initRowCallback = categoryProductRowInit;
        gridJsObject.checkboxCheckCallback = registerCategoryProduct;

        if (gridJsObject.rows) {
            gridJsObject.rows.each(function (row) {
                categoryProductRowInit(gridJsObject, row);
            });
        }
    };
});
