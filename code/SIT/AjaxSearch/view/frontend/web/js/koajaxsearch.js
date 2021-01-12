/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
define(['jquery','uiComponent','ko','mage/storage'], function($,Component,ko,storage) {
    return Component.extend({
        defaults: {
            productListArray: ko.observableArray([]),
            isProduct: ko.observable(null),
            productColl : ko.observableArray([]),
            proArray : ko.observableArray([]),
        },
        initialize: function () {
            self = this;
            this._super();
            if (this.fullActionName == 'catalogsearch_result_index') {
                self.getCatalogSearchResult();
            }
        },
        productListFunction : function(){
            self.productListArray(this.productList);
            if(this.productList.length > 0){
                $.ajax({
                    url: this.baseUrl + 'sit_ajaxsearch/index/catprodata',
                    data: JSON.stringify({
                        'productID' : null,
                        'cuk':this.catUrlkey,
                        'cid':this.catId
                    }),
                    showLoader : true,
                    type: "POST",
                    success: function(response){
                        if(response.product_data != ''){
                            self.isProduct(1);
                            self.proArray(response.product_data);
                            self.productColl(response.product_data);
                            setTimeout(function(){
                                var windowHeight = $('.column.main').height();
                                $('.background-image').css('height',windowHeight);
                            }, 100);
                         } else {
                            self.isProduct(0);
                         }
                    }
                });
            }
        },
        arrayLength : function(){
            if(self.productListArray().length)
                return true;
            else
                return false;
        },
        getProductData : function(data,e) {
            var productId = e.target.options[e.target.selectedIndex].value;
            var productText = e.target.options[e.target.selectedIndex].text;
            self.productColl([]);
            if(productId == '-1') {
                self.productColl(self.proArray());
            } else {
                $.each(self.proArray(), function( key, val ) {
                    if(productId == val.id){
                        self.productColl.push(self.proArray()[key]);
                    }
                });
            }
        },
        getCatalogSearchResult: function() {
            if (this.searchProductIdArray.length == 0) {
                self.isProduct(0);
            } else {
                $.ajax({
                    url: this.baseUrl + 'sit_ajaxsearch/index/catprodata',
                    showLoader : true,
                    data: JSON.stringify({
                        'productID': this.searchProductIdArray
                    }),
                    type: "POST",
                    success: function (response) {
                        if (response.product_data != '') {
                            self.isProduct(1);
                            self.productColl(response.product_data);
                        } else {
                            self.isProduct(0);
                        }
                    }
                });
            }
        },
        checkCatalogSearch: function () {
            return this.fullActionName;
        }
    });
});