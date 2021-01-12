define([
    'ko',
    'uiComponent',
    'mage/url',
    'mage/storage',
    'jquery'
], function(ko, Component, urlBuilder,storage,$) {
    'use strict';
    var currentPage="",limit="",body = '';
    return Component.extend({
        defaults: {
            template: 'SIT_GeneralNews/kogeneralnews',
            newsCollection: ko.observableArray([]),
            InputArray: ko.observableArray([]),
            PerPage: ko.observable(),
            totalPage: ko.observable(),
            Page: ko.observable(1),
            sizePerPage: ko.observable([10,20,50]),
        },

        initialize: function(config) {
            var self = this;
            this._super();
            currentPage = config.pageNumber
            limit = config.limitUrlParam;
            var serviceUrl = config.controller_url;
            var data={generalnewsList: true};
            /**
             * call for set all paginations data
             */
            body = $('body').loader();
            body.loader('show');
            self.setPagination();
            storage.post(
                serviceUrl,
                JSON.stringify(data)
            ).done(
                function(response) {
                    self.newsCollection(response);
                    if (limit != "") {
                        $('#per_page option[value="'+limit+'"]').prop("selected",true);
                        $('#per_page').trigger("change");
                    }
                    body.loader('hide');
                }
            ).fail(
                function(response) {
                }
            );
        },
        
        setPagination: function () {
            var self = this;

            /**
             * [InputArray create copy of collection]
             * @type {[type]}
             */
            self.InputArray = self.newsCollection;

            /**
             * [used for move last page]
             * @param  {[type]} ) {                           var totalNumberOfPages [description]
             * @return {[type]}   [description]
             */
            self.PageTotal = ko.computed(function() {
                var totalNumberOfPages = Math.ceil(self.InputArray().length / parseInt(self.PerPage()));
                if (self.Page() > totalNumberOfPages && totalNumberOfPages > 0) {
                    self.Page(totalNumberOfPages);
                }
                return totalNumberOfPages;
            });

            /**
             * [whenever any action occur in pagination at that time call this(PaginatedArray) computed method]
             */
            self.PaginatedArray = ko.computed(function() {
                self.newsCollection();
                /**
                 * [move specific page]
                 * @type {[type]}
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

                self.InputArray = self.newsCollection;
                self.totalPage(Math.ceil(self.InputArray().length/perPage));
                self.PageTotal();
                self.setUrlParams(currentPage,perPage);
                return self.InputArray().slice(start, start + perPage);
            });

            /**
             * [Pagination make current scope observable]
             * @type {[type]}
             */
            self.Pagination = ko.observable(this);
            if (currentPage!="") {
                self.Page(currentPage);
            }

        },
        /**
         * Set url parameter
         * @param {[type]} page    [description]
         * @param {[type]} perPage [description]
         */
        setUrlParams: function(page, perPage) {
            var per_page = $("#per_page option:selected").val();
            if (history.pushState) {
                var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                window.history.pushState({path:newurl},'',newurl);
            }
            var self = this;
            var urlString = "?";
            if (self.Page() > 1) {
                urlString += 'p='+self.Page()+'&';
            }
            if (perPage > 10) {
                urlString += 'limit='+ perPage +'&';
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
         * Move to next & previous
         * @param  {[type]} offset [description]
         * @return {[type]}        [description]
         */
        changePageBy : function(offset) {
            var self = this;
            var per_page = $("#per_page option:selected").val();
            return function() {
                self.Page(parseInt(self.Page()) + parseInt(offset));
                self.setUrlParams(self.Page(),per_page);
            };
        },
        /**
         * Move to first & last page
         * @param  {[type]} newPage [description]
         * @return {[type]}         [description]
         */
        changePageTo : function(newPage) {
            var self = this;
            var per_page = $("#per_page option:selected").val();
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
                self.setUrlParams(self.Page(),per_page);
            };
        }
    });
});