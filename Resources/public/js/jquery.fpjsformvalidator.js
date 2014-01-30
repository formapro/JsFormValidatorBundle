if(window.jQuery) {
    (function($) {
        var methods = {
            'init': function(options) {
                $(this).each(function(){
                    var item = this;

                    if (undefined == item.jsFormValidator) {
                        item.jsFormValidator = {};
                    }
                    for (var optName in options) {
                        switch (optName) {
                            case 'customEvents':
                                options[optName].apply(this);
                                break;
                            default:
                                item.jsFormValidator[optName] = options[optName];
                                break;
                        }
                    }
                });
            },

            'validate': function(opts) {
                var isValid = true;

                $(this).each(function(){
                    var item = this;
                    var method = (true === opts['recursive'])
                        ? 'validateRecursively'
                        : 'validate';

                    if (item.jsFormValidator[method]()) {
                        isValid = false;
                    }
                });

                return isValid;
            },

            'showErrors': function(opts) {
                $(this).each(function(){
                    var item = this;
                    item.jsFormValidator.showErrors.apply(item.jsFormValidator.domNode, [opts['errors'], opts['type']]);
                });
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
