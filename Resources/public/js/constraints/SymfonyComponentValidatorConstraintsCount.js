/**
 * Checks count of an array or object
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsCount() {
    this.maxMessage = '';
    this.minMessage = '';
    this.exactMessage = '';
    this.max = null;
    this.min = null;

    this.validate = function(value) {
        if (this.isEmtyValue(value)) {
            return;
        }

        var count = value.length;
        if (this.max === this.min && count !== this.min) {
            this.addError(value, this.exactMessage);
            return;
        }
        if (!isNaN(this.max) && count > this.max) {
            this.addError(value, this.maxMessage);
            return;
        }
        if (!isNaN(this.min) && count < this.min) {
            this.addError(value, this.minMessage);
        }
    };

    this.onCreate = function() {
        this.min = parseInt(this.min);
        this.max = parseInt(this.max);

        this.minMessage = this.transChoice(this.minMessage, this.min, {
            '{{ limit }}': this.min
        });
        this.maxMessage = this.transChoice(this.maxMessage, this.max, {
            '{{ limit }}': this.max
        });
        this.exactMessage = this.transChoice(this.exactMessage, this.min, {
            '{{ limit }}': this.min
        });
    }
}
SymfonyComponentValidatorConstraintsCount.prototype = new FpJsConstraintModel();