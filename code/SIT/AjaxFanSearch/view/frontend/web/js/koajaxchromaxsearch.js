define(['jquery','uiComponent','ko','mage/storage'], function($,Component,ko,storage) {
    return Component.extend({
    	defaults: {
    		chromaxSizeOptionsArray: ko.observableArray([]),
            chromaxConnectorOptionsArray: ko.observableArray([]),
            chromaxVoltageOptionsArray : ko.observableArray([]),
            chromaxCollectionArray : ko.observableArray([]),
            chromaxSearchSizeParamArray : ko.observableArray([]),
            chromaxSearchConnectorParamArray : ko.observableArray([]),
            chromaxSearchVoltageParamArray : ko.observableArray([]),
            template: {
                afterRender: function () {
                    self.addCustomScroller();
                }
            }
    	},
        initialize: function () {
        	self = this;
            this._super();
            self.chromaxCollectionArray(this.chromaxCollection);
            self.chromaxSizeOptionsData();
            self.chromaxConnectorOptionsData();
        },
        chromaxSizeOptionsData : function(){
            var self = this;
            if(this.sizeOptions != null){
            	self.chromaxSizeOptionsArray(this.sizeOptions);
                return self.chromaxSizeOptionsArray();
            }
        },
        chromaxConnectorOptionsData : function(){
            var self = this;
            if(this.connectorOptions != null){
                self.chromaxConnectorOptionsArray(this.connectorOptions);
                return self.chromaxConnectorOptionsArray();
            }
        },
        voltageOptionsData : function(){
            var self = this;
            if(this.voltageOptions != null){
                self.chromaxVoltageOptionsArray();
                return self.chromaxVoltageOptionsArray();
            }
        },
        getChromaxSizeData : function(data,e)
        {
            var self = this;
            var attr_value = e.target.options[e.target.selectedIndex].value;
            var attr_text = e.target.options[e.target.selectedIndex].text;
            var data_attr_text = e.target.options[e.target.selectedIndex].getAttribute('data_text');
            if(attr_value == '-1'){
                self.chromaxSearchSizeParamArray([]);
            } else {
                self.chromaxSearchSizeParamArray({'attr_value':attr_value,'attr_text':attr_text,'data_attr_text':data_attr_text});
            }
            self.passRequest();
        },
        getChromaxConnectorData : function(data,e)
        {
            var self = this;
            var attr_value = e.target.options[e.target.selectedIndex].value;
            var attr_text = e.target.options[e.target.selectedIndex].text;
            var data_attr_text = e.target.options[e.target.selectedIndex].getAttribute('data_text');
            if(attr_value == '-1'){
                self.chromaxSearchConnectorParamArray([]);
            } else {
                self.chromaxSearchConnectorParamArray({'attr_value':attr_value,'attr_text':attr_text,'data_attr_text':data_attr_text});
            }
            self.passRequest();
        },
        passRequest : function()
        {
            var self = this;
            storage.post(
                'sit_ajaxfansearch/ajax/index',
                JSON.stringify({
                    'tab_name':'line-chromax',
                    'fan_size' : self.chromaxSearchSizeParamArray(),
                    'fan_connector' : self.chromaxSearchConnectorParamArray()
                }),true
            ).done(function (response) {
                self.chromaxCollectionArray(response);
            });
        },

        // For add horizontal scroll in mobile view : MJ 
        addCustomScroller: function(){
            var scroll = 0;
            var right = true;
            var left = false;
            $(window).scroll(function (event) {
                scroll = $(window).scrollTop();
                if(scroll < 240){
                    $('.panLeft').hide();
                    $('.panRight').hide();
                }
                if(scroll > 240){
                    if(right == true){
                        $('.panRight').show();
                    }
                    if(left == true){
                        $('.panLeft').show();
                    }   
                }
            });
            if ($('#sit-ajaxchromaxsearch-category-div .custom-scroller-chromax').length) {
                $('#sit-ajaxchromaxsearch-category-div .custom-scroller-chromax').append('<span class="panner panLeft"></span><span class="panner panRight"></span>');
                $('.panLeft').hide();
                $('.panRight').hide();
            }
            $('.panLeft').click(function() {
                var scroller = $(this).parents('.custom-scroller-chromax');
                $(scroller).animate({
                    scrollLeft: "-=700px"
                }, 1500);
                $(this).siblings('.panRight').show();
                right = true;
                left = false;
                $(this).hide();
            });
            $('.panRight').click(function() {
                var scroller = $(this).parents('.custom-scroller-chromax');
                $(scroller).animate({
                    scrollLeft: "+=700px"
                }, 1500);
                $(this).siblings('.panLeft').show();
                left = true;
                right = false;
                $(this).hide();
            });
        }
        // For add horizontal scroll in mobile view : MJ 
    });
});