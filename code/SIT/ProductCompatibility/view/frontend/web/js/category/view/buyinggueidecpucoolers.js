define([
    'ko',
    'uiComponent',
    'mage/url',
    'mage/storage',
    'jquery'
], function(ko, Component, urlBuilder,storage,$) {
	var preventMultipleClick = 0;
    return Component.extend({
        initialize: function () {
            this._super();
            var self = this;
          	if($("#tab-label-buying-guide-cpu-coolers").hasClass("active")){
				if (preventMultipleClick == 0) {
		    		preventMultipleClick = 1;
		    		self.setBuyingGuideCpuCoolerData();
		    	}
			}
			$("#tab-label-buying-guide-cpu-coolers").click(function(){
		    	if (preventMultipleClick == 0) {
		    		preventMultipleClick = 1;
		    		self.setBuyingGuideCpuCoolerData();
		    	}
		    });
        },
        setBuyingGuideCpuCoolerData: function (){
	    	$.ajax({
	            url: this.controllerUrl,
	            type: 'POST',
	            dataType: 'json',
	            showLoader: true,
	            data: {
	                buying_guide_cc: true,
	            },
	        	complete: function(response) {
	        		$(".sit-buying-guide-cpu-coolers-data").html(JSON.parse(response.responseText));
	            },
	            error: function (xhr, status, errorThrown) {
	            }
	        });
	    }
    });
});