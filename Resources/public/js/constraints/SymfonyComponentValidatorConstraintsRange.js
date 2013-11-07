/**
 * Checks if value is a number and is between min and max values
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsRange() {
    this.maxMessage = '';
    this.minMessage = '';
    this.invalidMessage = '';
    this.max = null;
    this.min = null;

    this.validate = function(value) {
        if (this.isEmtyValue(value)) {
            return;
        }

        if (isNaN(value)) {
            this.addError(value, this.invalidMessage);
            return;
        }
        if (!isNaN(this.max) && value > this.max) {
            this.addError(value, this.maxMessage);
            return;
        }
        if (!isNaN(this.min) && value < this.min) {
            this.addError(value, this.minMessage);
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
SymfonyComponentValidatorConstraintsRange.prototype = new FpJsConstraintModel();