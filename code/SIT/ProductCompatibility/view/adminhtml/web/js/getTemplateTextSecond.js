define([
    'jquery',
    'underscore',
    'mage/url',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'Magento_Ui/js/modal/modal',
    'domReady'
], function ($,_, urlBuilder, uiRegistry, select, modal) {
    'use strict';
    
    return select.extend({
        initialize: function () {
            this._super();
            this.dropdownChange('initialize',this,this.value());
            return this;
        },
        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {
            this.dropdownChange('onupdate',this,value);
            return this._super();
        },

        dropdownChange: function(fromWhere,element,value)
        {
            var provider_name = requirejs('uiRegistry').get(this.provider);
            var template_2 = '';
            var comp_type = '';
            if(provider_name == 'sit_productcompatibility_mainboard_form'){
                comp_type = 'mainboard';
            }
            if(provider_name == 'sit_productcompatibility_cpu_form'){
                comp_type = 'cpu';
            }
            if(provider_name == 'sit_productcompatibility_ram_form'){
                comp_type = 'ram';
            }
            var selected_value = element.value();
            if(element.index == 'template_text_2') {
                this.template_2 = element.value();
            }
            var url = urlBuilder.build('sit_productcompatibility/index/add');
             $.ajax({
                url: url,
                showLoader: true,
                type: "POST",
                data: {
                    template_2: this.template_2
                },
                error: function(){
                    console.log('something is going wrong');
                },
                success: function(result){
                    setTimeout(function(){
                        var prev_text = $('textarea[name="comment"]').val();
                        var new_array = prev_text.split("\n\n");
                        new_array = jQuery.grep(new_array, function(n, i){
                          return (n !== "" && n != null);
                        });
                        if(fromWhere == 'onupdate') {
                            new_array[result.template_num - 1] = result.template_content;
                            $('textarea[name="comment"]').val(new_array.join("\n\n"));
                        } else {
                            new_array.push(result.template_content);
                            $('textarea[name="comment"]').val(new_array.join("\n\n"));
                        }
                    }, 1000);
                }
            });
        }
    });
});