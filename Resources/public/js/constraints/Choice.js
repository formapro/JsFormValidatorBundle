/**
 * Checks if value is in array of choices
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsChoice() {
    this.choices         = [];
    this.callback        = null;
    this.max             = null;
    this.min             = null;
    this.message         = '';
    this.maxMessage      = '';
    this.minMessage      = '';
    this.multiple        = false;
    this.multipleMessage = '';
    this.strict          = false;

    this.validate = function(value) {
        value = this.getValue(value);
        if (null === value) {
            return null;
        }

        var compared = this.compareChoices(value, this.getChoicesList());
        var isValid = value.length === compared.length;

        if (this.multiple) {
            if (!isValid) {
                return this.multipleMessage.replace('{{ value }}', String(value))
            }
            if (!isNaN(this.min) && value.length < this.min) {
                return this.minMessage
                    .replace('{{ value }}', String(value))
                    .replace('{{ limit }}', this.min);
            }
            if (!isNaN(this.max) && value.length > this.max) {
                return this.maxMessage
                    .replace('{{ value }}', String(value))
                    .replace('{{ limit }}', this.max);
            }
        } else if (!isValid) {
            return this.message.replace('{{ value }}', String(value));
        }

        return null;
    };

    this.onCreate = function() {
        this.min = parseInt(this.min);
        this.max = parseInt(this.max);
    };

    this.getValue = function(value) {
        if (-1 !== [undefined, null, ''].indexOf(value)) {
            return null;
        } else if (!(value instanceof Array)) {
            return [value];
        } else {
            return value;
        }
    };

    this.getChoicesList = function() {
        var choices = null;
        if (this.callback instanceof Array) {
            var model = null;
            if (undefined !== window[this.callback[0]]) {
                model = new window[this.callback[0]]();
            }

            if (model && typeof model[this.callback[1]] === 'function') {
                choices = model[this.callback[1]]();
            } else {
                throw new Error('Can not find the method "'+this.callback[1]+'" for the model "'+this.callback[0]+'" to get the choices list.');
            }

        }

        if (null == choices) {
            choices = (null !== this.choices)
                ? this.choices
                : []
        }

        return choices;
    };

    this.compareChoices = function(value, validChoices) {
        // Compare arrays by value
        var callbackFilter = function(n) {
            return validChoices.indexOf(n) != -1
        };
        // More precise comparison by type
        if (this.strict) {
            callbackFilter = function(n) {
                var result = false;
                for (var i in validChoices) {
                    if (n === validChoices[i]) {
                        result = true;
                    }
                }
                return result;
            };
        }

        return value.filter(callbackFilter);
    }
}
