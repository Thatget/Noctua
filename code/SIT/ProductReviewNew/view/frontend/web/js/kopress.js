define([
    'ko',
    'uiComponent',
    'mage/url',
    'mage/storage',
    'jquery'
], function(ko, Component, urlBuilder,storage,$) {
    'use strict';
    var controllerUrl = "", categoryName = {}, productName = {}, pressOriginCollection = {}, category_products="", categoryUrlParam="", productUrlParam="", body = '';
    return Component.extend({
        defaults: {
            template: 'SIT_ProductReviewNew/kopress',
            categoryFilter: ko.observableArray([]),
            productFilter: ko.observableArray([]),
            pressCollection: ko.observableArray([]),
            selectedProduct: ko.observable(),
            selectedCategory: ko.observable(),
        },
        initialize: function(config) {
            this._super();
            var self = this;
            /**
             * set url params
             */
            controllerUrl = config.controllerUrl;
            categoryUrlParam = config.categoryUrlParam;
            productUrlParam = config.productUrlParam;
            body = $('body').loader();
            body.loader('show');
            /**
             * set press image collection
             */
            self.setCollection();
        },
        /**
         * set press image collection & filter drop-downs
         */
        setCollection: function() {
            var self = this;
            var data={press_collection: true,category:'products'};
            storage.post(
                controllerUrl,
                JSON.stringify(data)
            ).done(
                function(response) {
                    pressOriginCollection = response.collection;
                    category_products = response.category_products;

                    /**
                     * Set main press image collection
                     */
                    $.each(response.collection, function(i, item){
                        self.pressCollection.push({category:i,product:item});
                    });

                    /**
                     * set category filter drop-down
                     */
                    $.each(response.category, function(i, item){
                        self.categoryFilter.push({value:i,label:item});
                        categoryName[i] = item;
                        if (categoryUrlParam != "" && categoryUrlParam == item) {
                            categoryUrlParam = i;
                        }
                    });

                    /**
                     * set product filter drop-down
                     */
                    $.each(response.product, function(i, item){
                        self.productFilter.push({value:item.id,label:item.name });
                        productName = response.product;
                        if (productUrlParam != "" && productUrlParam == item.name) {
                            productUrlParam = item.id;
                        }
                    });

                    /**
                     * Apply filter according to url params
                     */
                    if (categoryUrlParam != "" && productUrlParam != "") {
                        self.selectedCategory(categoryUrlParam);
                        self.productFilter([]);

                        $.each(category_products[self.selectedCategory()], function(i, item){
                            self.productFilter.push({value:item.id,label:item.name});
                        });
                        self.selectedProduct(productUrlParam);
                        self.applyProductFilter();
                    } else if (categoryUrlParam != "") {
                        self.selectedCategory(categoryUrlParam);
                        self.applyCategoryFilter();
                    } else if (productUrlParam != "") {
                        self.selectedProduct(productUrlParam);
                        self.applyProductFilter();
                    }

                    /**
                     * Remove loader
                     */
                    body.loader('hide');
                }
            ).fail(
                function(response) {
                }
            );
        },

        /**
         * Apply product filter on collection by change event
         */
        applyProductFilter: function() {
            var self = this;
            var filterData="";
            var productId = self.selectedProduct();

            /**
             * Set default filter
             */
            if (productId=="") {
                if (self.selectedCategory()!="") {
                    /**
                     * Hold category filter if it's already applied
                     */
                    filterData = {category:categoryName[self.selectedCategory()],product:pressOriginCollection[categoryName[self.selectedCategory()]]};
                    self.pressCollection([]);
                    self.pressCollection.push(filterData);
                } else {
                    self.pressCollection([]);
                    $.each(pressOriginCollection, function(i, item){
                        self.pressCollection.push({category:i,product:item});
                    });
                }
            } else {
                /**
                 * Set filter according to specific products
                 */
                var label = $("#product_filter option:selected").attr('label');
                if (self.selectedCategory()!="") {
                    /**
                     * Hold category filter if it's already applied
                     */
                    filterData = {category:categoryName[self.selectedCategory()],product:pressOriginCollection[categoryName[self.selectedCategory()]]};
                }
                if (filterData!="") {
                    $.each(filterData.product, function(index,value) {
                        if (value.hasOwnProperty(productId)) {
                            /**
                             *  Apply product filter with category filter
                             */
                            var filterdProduct = filterData.product[index][productId];
                            filterData = {category:categoryName[self.selectedCategory()],product:{0:{[productId]:filterdProduct}}};
                            self.pressCollection([]);
                            self.pressCollection.push(filterData);
                            return false;
                        }
                    });
                } else {
                    /**
                     * Apply only product filter
                     */
                    self.pressCollection([]);
                    $.each(pressOriginCollection, function(i, item){
                         $.each(item, function(index, value){
                            if (value.hasOwnProperty(productId)) {
                                self.pressCollection.push({category:i,product:{0:value}});
                                return false;
                            }
                        });
                    });
                }
            }

            /**
             * Bind url params
             */
            self.setUrlParams();
        },

        /**
         * Apply category filter
         */
        applyCategoryFilter: function() {
            var self = this;
            /**
             * Set default filter
             */
            if (self.selectedCategory()=="") {
                /**
                 * Set all collection of image
                 */
                self.pressCollection([]);
                $.each(pressOriginCollection, function(i, item){
                    self.pressCollection.push({category:i,product:item});
                });
                /**
                 * Set all product in drop-down filter
                 */
                self.productFilter([]);
                $.each(productName, function(i, item) {
                    self.productFilter.push({value:item.id,label:item.name});
                });
            } else {
                /**
                 * Apply category filter
                 */
                var filterData = {category:categoryName[self.selectedCategory()],product:pressOriginCollection[categoryName[self.selectedCategory()]]};
                self.pressCollection([]);
                self.pressCollection.push(filterData);
                self.productFilter([]);

                /**
                 * Set product according to category in product filter drop-down
                 */
                $.each(category_products[self.selectedCategory()], function(i, item){
                    self.productFilter.push({value:item.id,label:item.name});
                });
            }

            /**
             * Remove product filter
             */
            self.selectedProduct("");

            /**
             * Set url params
             */
            self.setUrlParams();
        },

        /**
         * Set url params according to product & category filter
         */
        setUrlParams: function() {
            var self = this;
            if (history.pushState) {
                var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                window.history.pushState({path:newurl},'',newurl);
            }
            var urlString = "?";
            if (self.selectedCategory() != "") {
                var label = $("#category_filter option:selected").attr('label');
                label = label.replace(/ /g,"-").replace(/&/g,"_");
                urlString += 'cat='+label+'&';
            }
            if (self.selectedProduct() != "") {
                var label = $("#product_filter option:selected").attr('label');
                label = label.replace(/ /g,"_");
                urlString += 'pro='+label+'&';
            }

            if (urlString != "?") {
                urlString = urlString.substring(0, urlString.length - 1);
                if (history.pushState) {
                    var allParams = urlString;
                    var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + allParams;
                    window.history.pushState({path:newurl},'',newurl);
                }
            }
        }
    });
});