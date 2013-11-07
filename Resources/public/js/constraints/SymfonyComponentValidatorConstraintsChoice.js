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
    this.message         = "Choose a valid gender.";
    this.maxMessage      = "You must select at most {{ limit }} choice.";
    this.minMessage      = "You must select at least {{ limit }} choice.";
    this.multiple        = false;
    this.multipleMessage = "One or more of the given values is invalid.";
    this.strict          = false;

    this.validate = function(value) {
        if (!value instanceof Array) {
            value = [value];
        }

        var validChoices;
        if (this.callback instanceof Array) {
            validChoices = FpJsFormValidator.prototype.callModelMethod.call(this, this.callback);
        }
        if (!validChoices) {
            validChoices = (null !== this.choices)
                ? this.choices
                : []
            ;
        }

        // Compare arrays by value
        var filter = function(n) {
            return validChoices.indexOf(n) != -1
        };
        // More precise comparison by type
        if (this.strict) {
            filter = function(n) {
                var result = false;
                for (var i in validChoices) {
                    if (n === validChoices[i]) {
                        result = true;
                    }
                }
                return result;
            };
        }

        // Check if all the received elemens are valid
        var isValid = value.length === value.filter(filter).length;

        if (this.multiple) {
            if (!isValid) {
                this.addError(value, this.multipleMessage);
            }
            if (!isNaN(this.min) && value.length < this.min) {
                this.addError(value, this.minMessage);
                return;
            }
            if (!isNaN(this.max) && value.length < this.max) {
                this.addError(value, this.maxMessage);
            }
        } else if (!isValid) {
            this.addError(value, this.message);
        }
    };

    this.onCreate = function() {
        this.min = parseInt(this.min);
        this.max = parseInt(this.max);


        this.minMessage   = this.transChoice(this.minMessage, this.min, {
            '{{ limit }}': this.min
        });
        this.maxMessage   = this.transChoice(this.maxMessage, this.max, {
            '{{ limit }}': this.max
        });
    }
}
SymfonyComponentValidatorConstraintsChoice.prototype = new FpJsConstraintModel();