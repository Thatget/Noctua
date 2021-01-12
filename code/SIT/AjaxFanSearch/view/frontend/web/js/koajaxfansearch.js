define(['jquery','uiComponent','ko','mage/storage'], function($,Component,ko,storage) {
    return Component.extend({
        defaults: {
            fanSizeOptionsArray: ko.observableArray([]),
            fanConnectorOptionsArray: ko.observableArray([]),
            fanVoltageOptionsArray : ko.observableArray([]),
            fanCollectionArray : ko.observableArray([]),
            fanSearchSizeParamArray : ko.observableArray([]),
            fanSearchConnectorParamArray : ko.observableArray([]),
            fanSearchVoltageParamArray : ko.observableArray([]),
            template: {
                afterRender: function () {
                    self.addCustomScroller();
                }
            }
        },
        initialize: function () {
            self = this;
            this._super();
            self.fanCollectionArray(this.fanCollection);
            self.fanSizeOptionsData();
            self.fanConnectorOptionsData();
            self.voltageOptionsData();

            fanIdUrl = window.location.href;
            fanId = fanIdUrl.split("=");
            
            if(fanId[1]) {
                setTimeout(function(){ 
                    $('.voltage_select option[value="'+fanId[1]+'"]').prop("selected",true);
                    $('.voltage_select').trigger("change");
                }, 1800);
            }
        },
        fanSizeOptionsData : function(){
            var self = this;
            self.fanSizeOptionsArray(this.sizeOptions);
            return self.fanSizeOptionsArray();
        },
        fanConnectorOptionsData : function(){
            var self = this;
            self.fanConnectorOptionsArray(this.connectorOptions);
            return self.fanConnectorOptionsArray();
        },
        voltageOptionsData : function(){
            var self = this;
            if(this.voltageOptions != null){
                self.fanVoltageOptionsArray(this.voltageOptions);
                return self.fanVoltageOptionsArray();
            }
        },
        getFanSizeData : function(data,e)
        {
            var self = this;
            var attr_value = e.target.options[e.target.selectedIndex].value;
            var attr_text = e.target.options[e.target.selectedIndex].text;
            var data_attr_text = e.target.options[e.target.selectedIndex].getAttribute('data_text');
            if(attr_value == '-1'){
                self.fanSearchSizeParamArray([]);
            } else {
                self.fanSearchSizeParamArray({'attr_value':attr_value,'attr_text':attr_text,'data_attr_text':data_attr_text});
            }
          
            connectorVal = $('select.filter-select.connector_select').val();
            voltageVal = $('select.filter-select.voltage_select').val();
            self.setUrlParams(attr_value,connectorVal,voltageVal);
            self.passRequest();
        },
        getFanConnectorData : function(data,e)
        {
            var self = this;
            var attr_value = e.target.options[e.target.selectedIndex].value;
            var attr_text = e.target.options[e.target.selectedIndex].text;
            var data_attr_text = e.target.options[e.target.selectedIndex].getAttribute('data_text');
            if(attr_value == '-1'){
                self.fanSearchConnectorParamArray([]);
            } else {
                self.fanSearchConnectorParamArray({'attr_value':attr_value,'attr_text':attr_text,'data_attr_text':data_attr_text});
            }

            sizeVal = $('#fan-size-select').val();
            voltageVal = $('select.filter-select.voltage_select').val();
            self.setUrlParams(sizeVal,attr_value,voltageVal);
            self.passRequest();
        },
        getFanVoltageData : function(data,e)
        {
            var self = this;
            var attr_value = e.target.options[e.target.selectedIndex].value;
            var attr_text = e.target.options[e.target.selectedIndex].text;
            var data_attr_text = e.target.options[e.target.selectedIndex].getAttribute('data_text');
            if(attr_value == '-1'){
                self.fanSearchVoltageParamArray([]);
            } else {
                self.fanSearchVoltageParamArray({'attr_value':attr_value,'attr_text':attr_text,'data_attr_text':data_attr_text});
            }

            sizeVal = $('#fan-size-select').val();
            connectorVal = jQuery('select.filter-select.connector_select').val();
            self.setUrlParams(sizeVal,connectorVal,attr_value);
            self.passRequest();
        },
        setUrlParams: function(size, connector, voltage) {
            if (history.pushState) {
                var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                window.history.pushState({path:newurl},'',newurl);
            }
            var self = this;
            
            var urlString = "?";
            if (size != -1 ) {
                urlString += 'size='+size+'&';
            }

            if (connector != -1) {
                urlString += 'connector='+connector+'&';
            }

            if (voltage != -1) {
                urlString += 'voltage='+voltage+'&';
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

        passRequest : function() {
            var self = this;
            storage.post(
                'sit_ajaxfansearch/ajax/index',
                JSON.stringify({
                    'tab_name':'fan',
                    'fan_size' : self.fanSearchSizeParamArray(),
                    'fan_connector' : self.fanSearchConnectorParamArray(),
                    'fan_voltage' : self.fanSearchVoltageParamArray()
                }),true
            ).done(function (response) {
                self.fanCollectionArray(response);
            });
        },

        // For add horizontal scroll in mobile view : MJ 
        addCustomScroller: function(){
            var scroll = 0;
            var right = true;
            var left = false;
            var scrollDivWidth = $('.section-inner-presse-page-full.fan-page-main').width();
            scrollDivWidth = scrollDivWidth+'px';
            if ($('#sit-ajaxfansearch-category-div .custom-scroller-fan').length) {
                $('#sit-ajaxfansearch-category-div .custom-scroller-fan').append('<span class="panner panLeft"></span><span class="panner panRight"></span>');
                $('.panLeft').hide();
                $('.panRight').hide();
            }
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
            $('.panLeft').click(function() {
                var scroller = $(this).parents('.custom-scroller-fan');
                $(scroller).animate({
                    scrollLeft: "-=700px"
                }, 1500);
                $(this).siblings('.panRight').show();
                right = true;
                left = false;
                $(this).hide();
            });

            $('.panRight').click(function() {
                var scroller = $(this).parents('.custom-scroller-fan');
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