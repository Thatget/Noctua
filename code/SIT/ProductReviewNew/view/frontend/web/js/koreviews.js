define([
    'ko',
    'uiComponent',
    'mage/url',
    'mage/storage',
    'jquery'
], function(ko, Component, urlBuilder,storage,$) {
    'use strict';
    var baseUrl="",currentPage = "",preventMultipleClick = 0,currentRequest, productFilterParam="", languageFilterParam="", limitParam="", reviewMoveId = "", reviewMoveIdParam="",jumpToValue="";
    return Component.extend({
        defaults: {
            template: 'SIT_ProductReviewNew/koreviews'
        },

        /**
         * [ReviewCollection store all reviews]
         * @type {[type]}
         */
        ReviewCollection: ko.observableArray([]),

        /**
         * [sizePerPage set paginations per page]
         * @type {[type]}
         */
        sizePerPage: ko.observable([10,20,50,100]),

        /**
         * [totalPage store total page count]
         * @type {[type]}
         */
        totalPage: ko.observable(),

        /**
         * [Page store current page]
         * @type {[type]}
         */
        Page: ko.observable(1),

        /**
         * [language store all languages]
         * @type {[type]}
         */
        language:ko.observableArray(),

        /**
         * [categoryProducts store products of category]
         * @type {[type]}
         */
        categoryProducts : ko.observableArray([]),

        /**
         * [initialize call default]
         * @param  {[type]} config [description]
         * @return {[type]}        [description]
         */
        initialize: function(config) {
            this._super();
            baseUrl = config.base_url;
            var self = this;
            var categories_products=config.allproductsname;
            var newCategories=[];
            var newProducts=[];
            /**
             * Url parameters
             * @type {[type]}
             */
            currentPage = config.pageNumber;
            productFilterParam = config.productUrlParam;
            languageFilterParam = config.languageUrlParam;
            limitParam = config.limitUrlParam;
            reviewMoveId = window.location.href;
            reviewMoveIdParam = reviewMoveId.split("#");
            /**
             * [prepare collection for product filter array]
             * @param  {[type]} key        [description]
             * @param  {Array}  categories )             {                newProducts [description]
             * @return {[type]}            [description]
             */
            $.each(categories_products, function( key, categories ) {
                newProducts=[];
                $.each(categories, function( key, product ) {
                    newProducts.push({"id":key,"label":product});
                });
                newCategories.push({"label":key,"category":newProducts});
            });
            self.categoryProducts = ko.observableArray(newCategories);

        },

        reviewBlock:function()
        {
            return this.review_block;
        },

        /**
         * Call after filter binding div tag
         * @return {[type]} [description]
         */
        bindAfterRender:function(){
            var self = this;
            if($("#tab-label-reviews").hasClass("active")){
                if (preventMultipleClick == 0) {
                    preventMultipleClick = 1;
                    self.callAjax();
                }
            }
            /**
             * Call ajax on click of "Review" tab
             */
            $("#tab-label-reviews").click(function(){
                if (preventMultipleClick == 0) {
                    preventMultipleClick = 1;
                    self.callAjax();
                }
            });
        },
        callAjax: function(){
            var self = this;
            /**
             * set product collection to array
             */
            var serviceUrl = baseUrl+'productreviewnew/productreview/koreviews';
            var data={languageList: true};
            storage.post(
                serviceUrl,
                JSON.stringify(data)
            ).done(
                function(response) {
                   /**
                    * [set observable array for language collection]
                    * @param  {[type]} key   [description]
                    * @param  {[type]} value )             {                        self.language.push(value);                    } [description]
                    * @return {[type]}       [description]
                    */
                    self.language(response);
                    if (productFilterParam != "") {
                        $('#product_filter option[value="'+productFilterParam+'"]').prop("selected",true);
                        $('#product_filter').trigger("change");
                    }
                    if (languageFilterParam != "") {
                        $('#language_filter option[value="'+languageFilterParam+'"]').prop("selected",true);
                        $('#language_filter').trigger("change");
                    }
                    if (limitParam != "") {
                        $('#per_page option[value="'+limitParam+'"]').prop("selected",true);
                        $('#per_page').trigger("change");
                    }
                    if (currentPage != "") {
                        self.Page(currentPage);
                        var selectedProduct = $(".fieldValue option:selected").val();
                        var lang = $("#language_filter option:selected").val();
                        var per_page = $("#per_page option:selected").val();

                        if (typeof selectedProduct==="undefined") {
                            selectedProduct="All";
                        }
                        if (typeof lang==="undefined") {
                            lang="All";
                        }
                        if (typeof per_page==="undefined") {
                            per_page=10;
                        }
                        self.setData(selectedProduct, lang, self.Page(), per_page);
                        self.setUrlParams(selectedProduct,self.Page(),per_page,lang);
                    }

                    if (productFilterParam == "" && languageFilterParam == "" && limitParam == "" && currentPage == "") {
                        self.Page(1);
                        self.setData("All","All",self.Page(),10);
                    }
                }
            ).fail(
                function(response) {

                }
            );
        },
        setData: function(productId, languageId, page, pageSize){
            var self = this;
            var serviceUrl = baseUrl+'productreviewnew/productreview/koreviews';
            var data={reviewlist: true, pid:productId,lid:languageId,p:page,limit:pageSize};
            currentRequest = $.ajax({
                url: serviceUrl,
                type: 'POST',
                showLoader: true,
                data: JSON.stringify(data),
                beforeSend: function() {
                  if (currentRequest != null) {
                        currentRequest.abort();
                    }
                },
                success: function(response) {
                    self.ReviewCollection(response.collection);
                    var per_page = $("#per_page option:selected").val();
                    self.totalPage(Math.ceil(response.size/per_page));
                    if (window.location.hash) {
                        var element = window.location.hash;
                        var top = $(element).offset().top - 60;
                        $('body,html').animate({scrollTop:top}, 'slow');
                    }
                    if (response.size==0) {
                        self.Page(0);
                    }
                }
            });
        },
        pagerLast:function()
        {
            return this.pageLast;
        },
        pagerFirst: function()
        {
            return this.pageFirst;
        },

        /**
         * set url parameter
         * @param {[type]} product  [description]
         * @param {[type]} page     [description]
         * @param {[type]} perPage  [description]
         * @param {[type]} language [description]
         */
        setUrlParams: function(product, page, perPage, language) {
            if (history.pushState) {
                var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                window.history.pushState({path:newurl},'',newurl);
            }
            var self = this;
            var selectedProduct = $("#product_filter option:selected").val();
            var selectedLimit = $("#per_page option:selected").val();
            var urlString = "?";
            if (product != "All" && product!="" && product!="undefined") {
                urlString += 'product='+product+'&';
            }

            if (self.Page() > 1) {
                urlString += 'p='+self.Page()+'&';
            }

            if (selectedLimit > 10) {
                urlString += 'limit='+selectedLimit+'&';
            }

            if (language != "All" && language != "") {
                urlString += 'lng='+language+'&';
            }

            if (reviewMoveIdParam.length == 2) {
                if ($('#'+reviewMoveIdParam[1]).length) {
                    urlString += '#'+reviewMoveIdParam[1]+'&';
                    $('html, body').animate({
                        scrollTop: $('#'+reviewMoveIdParam[1]).offset().top - 70
                    }, 1);
                }
            }

            if (urlString != "?") {
                urlString = urlString.substring(0, urlString.length - 1);
                if (history.pushState) {
                    var allParams = urlString;
                    var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + allParams;
                    window.history.pushState({path:newurl},'',newurl);
                }
            }
        },

        /**
         * [changePageBy call from template file when next & previous action]
         * @param {[type]} offset [description]
         */
        changePageBy : function(offset) {
            var self = this;
            var selectedProduct = $(".fieldValue option:selected").val();
            var per_page = $("#per_page option:selected").val();
            var lang = $("#language_filter option:selected").val();
            $("#jumptopage").val("");
            $("#jumptopage_bottom").val("");
            jumpToValue = $("#jumptopage").val();
            return function() {
                self.Page(parseInt(self.Page()) + parseInt(offset));
                self.setData(selectedProduct,lang,self.Page(),per_page);
                self.setUrlParams(selectedProduct,self.Page(),per_page,lang);
            };
        },

        /**
         * [JumpToPage move to specific page]
         */
        JumpToPage: function(){
            var self = this;
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (self.Page()!="") {
                if ($.isNumeric($("#jumptopage").val())) {
                    if ($("#jumptopage").val()>0) {
                        if ($("#jumptopage").val() > self.totalPage()) {
                            $("#jumptopage").val(self.totalPage());
                            $("#jumptopage_bottom").val($("#jumptopage").val());
                        }
                        /**
                         * 13 = Enter key code
                         */
                        if(keycode == '13'){
                            self.Page($("#jumptopage").val());
                            $('#jumptopage').blur();
                            self.jumpToPageFocusOut();
                        }
                    }
                } else {
                    $("#jumptopage").val("");
                    $("#jumptopage_bottom").val($("#jumptopage").val());
                }
            }
        },

        /**
         * event of footer jump to page
         */
        JumpToPageBottom: function() {
            var self = this;
            var keycode = (event.keyCode ? event.keyCode : event.which);
            $("#jumptopage").val($("#jumptopage_bottom").val());
            self.JumpToPage();
        },

        /**
         * event of footer jump to page
         * When focus out at that time execute
         */
        jumpToPageFocusOutBottom:function() {
            var self = this;
            self.jumpToPageFocusOut();
        },

        /**
         * Call on focus out of jump to input field
         * [jumpToPageFocusOut description]
         * @return {[type]} [description]
         */
        jumpToPageFocusOut:function() {
            var self = this;
            if ($("#jumptopage").val() != jumpToValue) {
                jumpToValue = $("#jumptopage").val();
                $("#jumptopage_bottom").val(jumpToValue);
                self.Page(jumpToValue);
                if (self.Page()<=0) {
                    $("#jumptopage").val();
                    self.Page(1);
                }
                var selectedProduct = $(".fieldValue option:selected").val();
                var lang = $("#language_filter option:selected").val();
                var per_page = $("#per_page option:selected").val();
                self.setData(selectedProduct, lang, self.Page(), per_page);
                self.setUrlParams(selectedProduct,self.Page(),per_page,lang);
            }
        },

        /**
         * event for fotter limit(page size) drop down.
         */
        setLimitBottom: function(){
            var self = this;
            var limitValueBottom = $("#per_page_bottom option:selected").val();
            $('#per_page option[value="'+limitValueBottom+'"]').prop("selected",true);
            self.setLimit();
        },

        /**
         * Pagination dropdown
         */
        setLimit: function() {
            var self = this;
            $("#jumptopage").val("");
            $("#jumptopage_bottom").val("");
            jumpToValue = $("#jumptopage").val();
            var selectedProduct = $(".fieldValue option:selected").val();
            var lang = $("#language_filter option:selected").val();
            var per_page = $("#per_page option:selected").val();
            self.Page(1);
            $('#per_page_bottom option[value="'+per_page+'"]').prop("selected",true);
            self.setData(selectedProduct, lang, self.Page(), per_page);
            self.setUrlParams(selectedProduct,self.Page(),per_page,lang);
        },

        /**
         * [productFilterData filter products as per selected value from dropdown]
         * @return {[type]} [description]
         */
        productFilterData: function() {
            var self = this;
            $("#jumptopage").val("");
            $("#jumptopage_bottom").val("");
            jumpToValue = $("#jumptopage").val();
            var selectedProduct = $(".fieldValue option:selected").val();
            var lang = $("#language_filter option:selected").val();
            var per_page = $("#per_page option:selected").val();
            self.Page(1);
            self.setData(selectedProduct, lang, self.Page(), per_page);
            self.setUrlParams(selectedProduct,self.Page(),per_page,lang);
        },

        /**
         * Language filter
         * [languageSetParam description]
         * @return {[type]} [description]
         */
        languageSetParam: function() {
            var self = this;
            $("#jumptopage").val("");
            $("#jumptopage_bottom").val("");
            jumpToValue = $("#jumptopage").val();
            var selectedProduct = $(".fieldValue option:selected").val();
            var lang = $("#language_filter option:selected").val();
            var per_page = $("#per_page option:selected").val();
            self.Page(1);
            self.setData(selectedProduct,lang,self.Page(),per_page);
            self.setUrlParams(selectedProduct,self.Page(),per_page,lang);
        },

        /**
         * [changePageTo call from template file when move first or last element]
         * @param {[type]} newPage [description]
         */
        changePageTo : function(newPage) {
            var self = this;
            var selectedProduct = $(".fieldValue option:selected").val();
            var per_page = $("#per_page option:selected").val()
            var lang = $("#language_filter option:selected").val();
            $("#jumptopage").val("");
            $("#jumptopage_bottom").val("");
            jumpToValue = $("#jumptopage").val();
            return function() {
                switch (newPage) {
                    case "last":
                        self.Page(self.totalPage());
                        break;
                    case "first":
                        self.Page(1);
                        break;
                    default:
                        self.Page(newPage);
                }
                self.setData(selectedProduct,lang,self.Page(),per_page);
                self.setUrlParams(selectedProduct,self.Page(),per_page,lang);
            };
        }
    });
});