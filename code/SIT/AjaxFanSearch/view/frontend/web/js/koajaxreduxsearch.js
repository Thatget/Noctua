define(['jquery','uiComponent','ko','mage/storage'], function($,Component,ko,storage) {
    var reduxCollectionData;
    return Component.extend({
    	defaults: {
    		reduxSizeOptionsArray: ko.observableArray([]),
            reduxConnectorOptionsArray: ko.observableArray([]),
            reduxVoltageOptionsArray : ko.observableArray([]),
            reduxCollectionArray : ko.observableArray([]),
            reduxSearchSizeParamArray : ko.observableArray([]),
            reduxSearchConnectorParamArray : ko.observableArray([]),
            reducSearchVoltageParamArray : ko.observableArray([]),
            template: {
                afterRender: function () {
                    self.addCustomScroller();
                }
            }
    	},
        initialize: function () {
        	self = this;
            this._super();
            reduxCollectionData = this.reduxCollection.collection;
            self.reduxCollectionArray(this.reduxCollection);
            self.reduxSizeOptionsData();
            self.reduxConnectorOptionsData();
        },
        reduxSizeOptionsData : function() {
            var self = this;
            if(this.sizeOptions != null){
            	self.reduxSizeOptionsArray(this.sizeOptions);
                return self.reduxSizeOptionsArray();
            }
        },
        reduxConnectorOptionsData : function(){
            var self = this;
            if(this.connectorOptions != null){
                self.reduxConnectorOptionsArray(this.connectorOptions);
                return self.reduxConnectorOptionsArray();
            }
        },
        voltageOptionsData : function(){
            var self = this;
            if(this.voltageOptions != null){
                self.reduxVoltageOptionsArray();
                return self.reduxVoltageOptionsArray();
            }
        },
        getReduxSizeData : function(data,e)
        {
            var self = this;
            var attr_value = e.target.options[e.target.selectedIndex].value;
            var attr_text = e.target.options[e.target.selectedIndex].text;
            var data_attr_text = e.target.options[e.target.selectedIndex].getAttribute('data_text');
            if(attr_value == '-1'){
                self.reduxSearchSizeParamArray([]);
            } else {
                self.reduxSearchSizeParamArray({'attr_value':attr_value,'attr_text':attr_text,'data_attr_text':data_attr_text});
            }
            self.passRequest();
        },
        getReduxConnectorData : function(data,e)
        {
            var self = this;
            var attr_value = e.target.options[e.target.selectedIndex].value;
            var attr_text = e.target.options[e.target.selectedIndex].text;
            var data_attr_text = e.target.options[e.target.selectedIndex].getAttribute('data_text');
            if(attr_value == '-1'){
                self.reduxSearchConnectorParamArray([]);
            } else {
                self.reduxSearchConnectorParamArray({'attr_value':attr_value,'attr_text':attr_text,'data_attr_text':data_attr_text});
            }
            self.passRequest();
        },

        passRequest : function()
        {
            var self = this;
            storage.post(
                'sit_ajaxfansearch/ajax/index',
                JSON.stringify({
                    'tab_name': 'line-redux',
                    'fan_size' : self.reduxSearchSizeParamArray(),
                    'fan_connector' : self.reduxSearchConnectorParamArray()
                }),true
            ).done(function (response) {
                self.reduxCollectionArray(response);
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
            if ($('#sit-ajaxreduxsearch-category-div .custom-scroller-redux').length) {
                $('#sit-ajaxreduxsearch-category-div .custom-scroller-redux').append('<span class="panner panLeft"></span><span class="panner panRight"></span>');
                $('.panLeft').hide();
                $('.panRight').hide();
            }
            $('.panLeft').click(function() {
                var scroller = $(this).parents('.custom-scroller-redux');
                $(scroller).animate({
                    scrollLeft: "-=700px"
                }, 1500);
                $(this).siblings('.panRight').show();
                right = true;
                left = false;
                $(this).hide();
            });

            $('.panRight').click(function() {
                var scroller = $(this).parents('.custom-scroller-redux');
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