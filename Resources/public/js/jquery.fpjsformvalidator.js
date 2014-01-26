if(window.jQuery) {
    (function($) {
        var methods = {
            'init': function(options) {
                $(this).each(function(){
                    if (undefined == this.jsFormValidator) {
                        this.jsFormValidator = {};
                    }
                    for (var optName in options) {
                        switch (optName) {
                            case 'customEvents':
                                options[optName].apply(this);
                                break;
                            default:
                                this.jsFormValidator[optName] = options[optName];
                                break;
                        }
                    }
                });
            },

            'validate': function() {
                var isValid = true;
                $(this).each(function(){
                    if (this.jsFormValidator.validate()) {
                        isValid = false;
                    }
                });

                return isValid;
            },

            'get': function() {
                var elements = [];
                this.each(function(){
                    elements.push(this[0].jsFormValidator);
                });

                return elements;
            }
        };

        $.fn.jsFormValidator = function(method) {
            if (!method) {
                return methods.get.apply(this, arguments);
            } else if (typeof method === 'object') {
                return methods.init.apply(this, arguments);
            } else if (methods[method]) {
                return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
            } else {
                $.error('Method ' + method + ' does not exist');
                return this;
            }
        };
    })(jQuery);
}
