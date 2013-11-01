/**
 * Check minimum and maximum length
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsLength() {
    this.maxMessage = '';
    this.minMessage = '';
    this.exactMessage = '';
    this.max = null;
    this.min = null;

    this.validate = function(value) {
        if ([undefined, null, ''].indexOf(value) >= 0) {
            return;
        }

        var length = value.length;
        if (this.max === this.min && length !== this.min) {
            this.addError(value, this.exactMessage);
            return;
        }
        if (null !== this.max && length > this.max) {
            this.addError(value, this.maxMessage);
            return;
        }
        if (null !== this.max && length < this.min) {
            this.addError(value, this.minMessage);
        }
    };

    this.onCreate = function() {
        this.min = parseInt(this.min);
        this.max = parseInt(this.max);

        var limit         = new RegExp('{{ limit }}', 'g');
        this.minMessage   = this.minMessage.replace(limit, this.min);
        this.maxMessage   = this.maxMessage.replace(limit, this.max);
        this.exactMessage = this.exactMessage.replace(limit, this.min);
    }
}
SymfonyComponentValidatorConstraintsLength.prototype = new FpJsConstraintModel();