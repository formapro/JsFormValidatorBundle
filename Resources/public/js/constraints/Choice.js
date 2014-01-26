/**
 * Checks if value is in array of choices
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsChoice() {
    this.choices = [];
    this.callback = null;
    this.max = null;
    this.min = null;
    this.message = '';
    this.maxMessage = '';
    this.minMessage = '';
    this.multiple = false;
    this.multipleMessage = '';
    this.strict = false;

    this.validate = function (value, element) {
        var errors = [];
        value = this.getValue(value);
        if (null === value) {
            return errors;
        }

        var compared = this.compareChoices(value, this.getChoicesList(element));
        var isValid = value.length === compared.length;

        if (this.multiple) {
            if (!isValid) {
                errors.push(this.multipleMessage.replace('{{ value }}', String(value)));
            }
            if (!isNaN(this.min) && value.length < this.min) {
                errors.push(
                    this.minMessage
                        .replace('{{ value }}', String(value))
                        .replace('{{ limit }}', this.min)
                );
            }
            if (!isNaN(this.max) && value.length > this.max) {
                errors.push(
                    this.maxMessage
                        .replace('{{ value }}', String(value))
                        .replace('{{ limit }}', this.max)
                );
            }
        } else if (!isValid) {
            errors.push(this.message.replace('{{ value }}', String(value)));
        }

        return errors;
    };

    this.onCreate = function () {
        this.min = parseInt(this.min);
        this.max = parseInt(this.max);
    };

    this.getValue = function (value) {
        if (-1 !== [undefined, null, ''].indexOf(value)) {
            return null;
        } else if (!(value instanceof Array)) {
            return [value];
        } else {
            return value;
        }
    };

    /**
     * @param {FpJsFormElement} element
     * @return {Array}
     */
    this.getChoicesList = function (element) {
        var choices = null;
        if (this.callback) {
            if (typeof element.callbacks[this.callback] == "function") {
                choices = element.callbacks[this.callback].apply(element);
            } else {
                throw new Error('Can not find a "' + this.callback + '" callback for the element id="' + element.id + '" to get a choices list.');
            }
        }

        if (null == choices) {
            choices = (null == this.choices) ? [] : this.choices;
        }

        return choices;
    };

    this.compareChoices = function (value, validChoices) {
        // Compare arrays by value
        var callbackFilter = function (n) {
            return validChoices.indexOf(n) != -1
        };
        // More precise comparison by type
        if (this.strict) {
            callbackFilter = function (n) {
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
