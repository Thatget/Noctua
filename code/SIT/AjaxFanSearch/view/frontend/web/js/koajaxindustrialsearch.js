define(['jquery','uiComponent','ko','mage/storage'], function($,Component,ko,storage) {
    return Component.extend({
    	defaults: {
    		industrialSizeOptionsArray: ko.observableArray([]),
            industrialConnectorOptionsArray: ko.observableArray([]),
            industrialVoltageOptionsArray : ko.observableArray([]),
            industrialCollectionArray : ko.observableArray([]),
            industrialSearchSizeParamArray : ko.observableArray([]),
            industrialSearchConnectorParamArray : ko.observableArray([]),
            industrialSearchVoltageParamArray : ko.observableArray([]),
            template: {
                afterRender: function () {
                    self.addCustomScroller();
                }
            }
    	},
        initialize: function () {
        	self = this;
            this._super();
            self.industrialCollectionArray(this.industrialCollection);
            self.industrialSizeOptionsData();
            self.industrialConnectorOptionsData();
        },
        industrialSizeOptionsData : function(){
            var self = this;
            if(this.sizeOptions != null){
            	self.industrialSizeOptionsArray(this.sizeOptions);
                return self.industrialSizeOptionsArray();
            }
        },
        industrialConnectorOptionsData : function(){
            var self = this;
            if(this.connectorOptions != null){
                self.industrialConnectorOptionsArray(this.connectorOptions);
                return self.industrialConnectorOptionsArray();
            }
        },
        voltageOptionsData : function(){
            var self = this;
            if(this.voltageOptions != null){
                self.industrialVoltageOptionsArray();
                return self.industrialVoltageOptionsArray();
            }
        },
        getIndustrialSizeData : function(data,e)
        {
            var self = this;
            var attr_value = e.target.options[e.target.selectedIndex].value;
            var attr_text = e.target.options[e.target.selectedIndex].text;
            var data_attr_text = e.target.options[e.target.selectedIndex].getAttribute('data_text');
            if(attr_value == '-1'){
                self.industrialSearchSizeParamArray([]);
            } else {
                self.industrialSearchSizeParamArray({'attr_value':attr_value,'attr_text':attr_text,'data_attr_text':data_attr_text});
            }
            self.passRequest();
        },
        getIndustrialConnectorData : function(data,e)
        {
            var self = this;
            var attr_value = e.target.options[e.target.selectedIndex].value;
            var attr_text = e.target.options[e.target.selectedIndex].text;
            var data_attr_text = e.target.options[e.target.selectedIndex].getAttribute('data_text');
            if(attr_value == '-1'){
                self.industrialSearchConnectorParamArray([]);
            } else {
                self.industrialSearchConnectorParamArray({'attr_value':attr_value,'attr_text':attr_text,'data_attr_text':data_attr_text});
            }
            self.passRequest();
        },
        passRequest : function()
        {
            var self = this;
            storage.post(
                'sit_ajaxfansearch/ajax/index',
                JSON.stringify({
                    'tab_name':'line-industrial',
                    'fan_size' : self.industrialSearchSizeParamArray(),
                    'fan_connector' : self.industrialSearchConnectorParamArray()
                }),true
            ).done(function (response) {
                self.industrialCollectionArray(response);
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

            if ($('#sit-ajaxindustrialsearch-category-div .custom-scroller-industrial').length) {
                $('#sit-ajaxindustrialsearch-category-div .custom-scroller-industrial').append('<span class="panner panLeft"></span><span class="panner panRight"></span>');
                $('.panLeft').hide();
                $('.panRight').hide();
            }
            $('.panLeft').click(function() {
                var scroller = $(this).parents('.custom-scroller-industrial');
                $(scroller).animate({
                    scrollLeft: "-=700px"
                }, 1500);
                $(this).siblings('.panRight').show();
                right = true;
                left = false;
                $(this).hide();
            });

            $('.panRight').click(function() {
                var scroller = $(this).parents('.custom-scroller-industrial');
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