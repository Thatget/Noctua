define([
    'ko',
    'uiComponent',
    'mage/url',
    'mage/storage',
    'jquery'
], function(ko, Component, urlBuilder,storage,$) {
    'use strict';
    var body = "",baseUrl="",buyerParam='',sellerParam='',currentPage='',provincePrepareArray = [],buyerProParam='',sellerProParam='',limitParam='';
    return Component.extend({
        defaults: {
            template: 'Onibi_StoreLocator/wheretobuy'
        },

        /**
         * Store all buyer & seller information
         * @type {[type]}
         */
        BuyerSellerInfo:ko.observableArray([]),

        /**
         * [InputArray generate duplicate of all seller/buyer collection for manage original state of collection]
         * @type {[type]}
         */
        InputArray: ko.observableArray([]),

        /**
         * [sizePerPage set paginations per page]
         * @type {[type]}
         */
        sizePerPage: ko.observable([10,20,50]),

        /**
         * [PerPage observable of page]
         * @type {[type]}
         */
        PerPage: ko.observable([50]),

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
         * store all regions of all country
         * @type {[type]}
         */
        allRegions:ko.observableArray([]),

        /**
         * Store Buyer country
         * @type {[type]}
         */
        buyersCountry:ko.observableArray([]),

        /**
         * Store selected buyer country for filter
         * @type {[type]}
         */
        selectedBuyersCountry:ko.observable(),

        /**
         * Store seller country
         * @type {[type]}
         */
        sellerCountry:ko.observableArray([]),

        /**
         * Store selected seller country
         * @type {[type]}
         */
        selectedSellerCountry:ko.observable(),

        /**
         * Store selected buyer province
         * @type {[type]}
         */
        selectedBuyersProvinces:ko.observable(''),

        /**
         * Store selected seller province
         * @type {[type]}
         */
        selectedSellerProvinces:ko.observable(''),

        /**
         * Store buyer province according to country
         * @type {[type]}
         */
        buyersProvinces:ko.observableArray([]),

        /**
         * Store seller province according to country
         * @type {[type]}
         */
        sellerProvinces:ko.observableArray([]),

        /**
         * Store media url for display image of seller/buyer
         * @type {[type]}
         */
        mediaUrl:ko.observable(),

        sellerFooterText:ko.observable(),

        whereToBuyPreTextVar:ko.observable(),


        initialize: function(config) {
            this._super();
            var self = this;
            baseUrl = config.base_url;
            self.mediaUrl(config.media_url);
            self.whereToBuyPreTextVar(this.whereToBuyPreText);

            /**
             * Set params according to config variable
             */
            buyerParam = config.buyercountry;
            sellerParam = config.sellercountry;
            currentPage = config.currentpage;
            sellerProParam=config.sellerprovince;
            buyerProParam=config.buyerprovince;
            limitParam=config.limit;

            /**
             * Get all regions from controller
             */
            body = $('body').loader();
            body.loader('show');
            var serviceUrl = baseUrl+'onibi_storelocator/storelocator/wheretobuy';
            self.setPagination();
            var data={regionsList: true};
            storage.post(
                serviceUrl,
                JSON.stringify(data)
            ).done(
                function(response) {
                   self.allRegions(response);
                }
            ).fail(
                function(response) {

                }
            );

            /**
             * Get country list from controller
             */
            var data={countryList: true};
            storage.post(
                serviceUrl,
                JSON.stringify(data)
            ).done(
                function(response) {
                    $.each(response, function( key, value ) {
                        if (value.value != "") {
                            self.buyersCountry.push({countryCode:value.value,countryLabel: value.label});
                            self.sellerCountry.push({countryCode:value.value,countryLabel: value.label});
                        }
                    });
                    self.buyerSellerData();
                }
            ).fail(
                function(response) {

                }
            );
        },

        /**
         * All filters logic apply in this functions
         */
        setPagination: function () {
            var self = this;
            var seller_text = this.sellerText;

            /**
             * InputArray create copy of buyer/seller collection
             */
            self.InputArray = ko.observableArray([]);

            self.PageTotal = ko.computed(function() {
                var totalNumberOfPages = Math.ceil(self.InputArray().length / parseInt(self.PerPage()));
                if (self.Page() > totalNumberOfPages && totalNumberOfPages > 0) {
                    self.Page(totalNumberOfPages);
                }
                return totalNumberOfPages;
            });
            /**
             * used for move last page
             */
            self.PageTotal = ko.computed(function() {
                var totalNumberOfPages = Math.ceil(self.InputArray().length / parseInt(self.PerPage()));
                if (self.Page() > totalNumberOfPages && totalNumberOfPages > 0) {
                    self.Page(totalNumberOfPages);
                }
                return totalNumberOfPages;
            });

            /**
             * When ever any action occur in pagination at that time call this(PaginatedArray) computed method
             */
            self.PaginatedArray = ko.computed(function() {
                var buyerFilter = self.selectedBuyersCountry();
                var sellerFilter = self.selectedSellerCountry();
                var buyersProvincesFilter = self.selectedBuyersProvinces();
                var sellerProvincesFilter = self.selectedSellerProvinces();
                self.BuyerSellerInfo();

                /**
                 * Move to specific page
                 */
                if (self.Page()!="") {
                    if ($.isNumeric(self.Page())) {
                        self.Page(self.Page());
                    }
                }

                if (self.Page()<1) {
                    self.Page(1);
                }

                var perPage = parseInt(self.PerPage());
                var start = self.Page() * perPage - perPage;
                if (perPage < 50) {
                    self.setUrlParams();
                }
                if ((!buyerFilter || buyerFilter == "" || buyerFilter == "-1") && (!sellerFilter || sellerFilter == "" || sellerFilter == "-1")) {
                    /**
                     * Return blank array when filters value is none
                     */
                    self.totalPage(0);
                    self.PageTotal();
                    self.InputArray = ko.observableArray([]);
                } else {
                    /**
                     * When seller filter apply
                     */
                    if (buyerFilter == "" && sellerFilter!="" && sellerFilter != "-1" ) {
                        sellerParam = sellerFilter;
                        buyerParam='';
                        $('#buyer_country option[value="-1"]').prop("selected",true);
                        self.buyersProvinces([]);
                        self.InputArray= ko.observableArray([]);
                        provincePrepareArray = [];
                        ko.utils.arrayFilter(self.BuyerSellerInfo(), function(i) {
                            if (sellerProvincesFilter != "" && sellerProvincesFilter != "undefined"  && self.sellerProvinces().length > 0) {
                                if (i.country_id==sellerFilter && i.type != '' && i.type!='1') {
                                    if (sellerProvincesFilter=='-1') {
                                        buyerProParam = sellerProvincesFilter;
                                        buyerProParam='';
                                        self.InputArray.push(i);
                                    }
                                    if (i.province==sellerProvincesFilter) {
                                        sellerProParam = sellerProvincesFilter;
                                        buyerProParam='';
                                        self.InputArray.push(i);
                                    }
                                    if (i.province!='') {
                                        if ($.inArray(i.province,provincePrepareArray)<0) {
                                            provincePrepareArray.push(i.province);
                                        }
                                    }
                                }
                            }
                            else if (i.country_id==sellerFilter && i.type != '' && i.type!='1') {
                                if (i.province!='') {
                                    /*Set province*/
                                    if ($.inArray(i.province,provincePrepareArray)<0) {
                                        provincePrepareArray.push(i.province);
                                    }

                                }
                                self.InputArray.push(i);
                            }
                        });
                        self.totalPage(Math.ceil(self.InputArray().length/perPage));
                        if (self.InputArray().length==0) {
                            var new_seller_text = seller_text.replace("%1",$("#seller_country option[value='"+sellerParam+"']").text());
                            self.sellerFooterText(new_seller_text);
                            self.Page(0);
                            self.setUrlParams();
                            return self.InputArray();
                        } else {
                            self.sellerFooterText('');
                            self.setUrlParams();
                            return self.InputArray().slice(start, start + self.PerPage());
                        }
                    }
                    /**
                     * when buyer filter apply
                     */
                    else if (buyerFilter != "" && sellerFilter=="" && buyerFilter != "-1") {
                        buyerParam = buyerFilter;
                        sellerParam='';
                        $('#seller_country option[value="-1"]').prop("selected",true);

                        self.InputArray= ko.observableArray([]);
                        self.sellerProvinces([]);
                        provincePrepareArray = [];
                        ko.utils.arrayFilter(self.BuyerSellerInfo(), function(i) {
                            var typeSting = i.type;
                            var flag = typeSting.search("1");//if not match return -1
                            if (buyersProvincesFilter != "" && buyersProvincesFilter != "undefined" && self.buyersProvinces().length > 0) {
                                /**
                                 *when province filter apply
                                 */
                                if (i.country_id==buyerFilter && i.type != '' && flag >= 0) {
                                    if (buyersProvincesFilter=='-1') {
                                        buyerProParam = buyersProvincesFilter;
                                        sellerProParam = '';
                                        self.InputArray.push(i);
                                    }
                                    if (i.province==buyersProvincesFilter) {
                                        buyerProParam = buyersProvincesFilter;
                                        sellerProParam = '';
                                        self.InputArray.push(i);
                                    }
                                    if (i.province!='') {
                                        /*Set province*/
                                        if ($.inArray(i.province,provincePrepareArray)<0) {
                                            provincePrepareArray.push(i.province);
                                        }
                                    }
                                }
                            } else if (i.country_id == buyerFilter && i.type != '' && flag >= 0) {
                                /**
                                 * Only for country filter
                                 */
                                if (i.province!='') {
                                    /*Set province*/
                                    if ($.inArray(i.province,provincePrepareArray) < 0) {
                                        provincePrepareArray.push(i.province);
                                    }
                                }
                                self.InputArray.push(i);
                            }
                        });

                        self.totalPage(Math.ceil(self.InputArray().length/perPage));
                        if (self.InputArray().length==0) {
                            self.Page(0);
                            self.setUrlParams();
                            return self.InputArray();
                        } else {
                            self.setUrlParams();
                            return self.InputArray().slice(start, start + self.PerPage());
                        }
                    }
                }

                self.PageTotal();
            });
            self.Pagination = ko.observable(this);
        },

        /**
         * Set & Update url parameter
         */
        setUrlParams: function() {
            var self = this;
            var selectedLimit = $("#per_page option:selected").val();
            var buyerCountry = $("#buyer_country option:selected").val();
            var sellerCountry = $("#seller_country option:selected").val();
            var sellerProVal = $('#seller_Provinces').val();
            if (history.pushState) {
                var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                window.history.pushState({path:newurl},'',newurl);
            }
            var urlString = "?";
            if (buyerParam!='' && buyerParam!='-1') {
                urlString +="buyercountry="+buyerParam+'&';
                if(buyerParam!='' && buyerParam!='-1' && buyerProParam != '-1' && buyerProParam != '') {
                    urlString += 'buyerprovince='+buyerProParam+'&';
                }
            } else if (sellerParam!='' && sellerParam!='-1') {
                urlString +="sellercountry="+sellerParam+'&';
                if (sellerParam!='' && sellerParam!='-1' && sellerProParam != '-1' && sellerProParam != '' && sellerProVal!='-1') {
                    urlString += 'sellerprovince='+sellerProParam+'&';
                }
            }
            if (self.Page() > 1) {
                urlString += 'p='+self.Page()+'&';
            }

            if (selectedLimit < 50) {
                urlString += 'limit='+selectedLimit+'&';
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
         * Set Buyer/Seller Data
         */
        buyerSellerData : function() {
            var self = this;
            var serviceUrl = baseUrl+'onibi_storelocator/storelocator/wheretobuy';
            var data={buyerSellerList: true};
            storage.post(
                serviceUrl,
                JSON.stringify(data)
            ).done(
                function(response) {
                    self.BuyerSellerInfo(response);
                    body.loader('hide');
                    if (buyerParam!="") {
                        /**
                         * When set dufault buyer filter by country
                         */
                        $('#buyer_country option[value="'+buyerParam+'"]').prop("selected",true);
                        $('#buyer_country').trigger("change");
                        if (limitParam != "") {
                            $('#per_page option[value="'+limitParam+'"]').prop("selected",true);
                            $('#per_page').trigger("change");
                            if (currentPage != "") {
                                self.Page(currentPage);
                            }
                        }
                    }else if (sellerParam!="") {
                        /**
                         * When set default seller filter by country
                         */
                        $('#seller_country option[value="'+sellerParam+'"]').prop("selected",true);
                        $('#seller_country').trigger("change");
                        if (limitParam != "") {
                            $('#per_page option[value="'+limitParam+'"]').prop("selected",true);
                            $('#per_page').trigger("change");
                            if (currentPage != "") {
                                self.Page(currentPage);
                            }
                        }
                    }
                }
            ).fail(
                function(response) {

                }
            );
        },

        /**
         * Call when buyer event(change) trigger
         */
        filterBuyerData : function(offset) {
            var self = this;

            if (provincePrepareArray.length > 0) {
                self.buyersProvinces([]);
                /**
                 * Set province according to country
                 */
                $.each(self.allRegions(), function( key, value ) {
                    if (jQuery("#buyer_country").val() == value.code) {
                        self.buyersProvinces.push({code:value.region_code,name:value.default_name});
                    }
                });
                /**
                 * Filter apply for province when set default param in url
                 */
                if (buyerProParam!="") {
                    $('#buyer_Provinces option[value="'+buyerProParam+'"]').prop("selected",true);
                    $('#buyer_Provinces').trigger("change");
                    if (currentPage != "") {
                        self.Page(currentPage);
                    }
                }
            } else {
                buyerProParam='';
                self.buyersProvinces([]);
            }
            if (buyerParam!="") {
                buyerParam="";
            }
            self.selectedSellerCountry('');
            var windowHeight = $('.column.main').height();
            $('.background-image').css('min-height', windowHeight);
        },

        /**
         * Call when change(event) trigger for seller filter
         */
        filterSellerData : function(offset) {
            var self = this;

            if (provincePrepareArray.length > 0) {
                self.sellerProvinces([]);
                /**
                 * Set province according to country
                 */
                $.each(self.allRegions(), function( key, value ) {
                    if (jQuery("#seller_country").val() == value.code) {
                        self.sellerProvinces.push({code:value.region_code,name:value.default_name});
                    }
                });
                /**
                 * apply default filter for seller province
                 */
                if (sellerProParam!="") {
                    $('#seller_Provinces option[value="'+sellerProParam+'"]').prop("selected",true);
                    $('#seller_Provinces').trigger("change");
                    if (currentPage != "") {
                        self.Page(currentPage);
                    }
                }
            } else {
                sellerProParam='';
                self.sellerProvinces([]);
            }
            if (sellerParam!="") {
                sellerParam="";
            }
            self.selectedBuyersCountry('');
            var windowHeight = $('.column.main').height();
            $('.background-image').css('min-height', windowHeight);
        },

        /**
         * Change event trigger when buyer province filter apply & change url param
         */
        filterBuyerProvincesData: function() {
            var self = this;
            self.setUrlParams();
        },

        /**
         * Change event trigger when seller province filter apply & change url param
         */
        filterSellerProvincesData: function(){
            var self = this;
            self.setUrlParams();
        },
        /**
         * Move to next/previous page
         */
        changePageBy : function(offset) {
            var self = this;
            return function() {
                self.setUrlParams();
                self.Page(parseInt(self.Page()) + parseInt(offset));
            };
        },

        /**
         * Move to first & last page
         */
        changePageTo : function(newPage) {
            var self = this;
            return function() {
                switch (newPage) {
                    case "last":
                        self.Page(self.PageTotal());
                        break;
                    case "first":
                        self.Page(1);
                        break;
                    default:
                        self.Page(newPage);
                }
                self.setUrlParams();
            };
        }
    });
});