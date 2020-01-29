//noinspection JSUnusedGlobalSymbols
/**
 * Checks count of an array or object
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
export default function SymfonyComponentValidatorConstraintsCount() {
    this.maxMessage = '';
    this.minMessage = '';
    this.exactMessage = '';
    this.max = null;
    this.min = null;

    this.validate = function (value) {
        var errors = [];
        var f = FpJsFormValidator;

        if (!f.isValueArray(value) && !f.isValueObject(value)) {
            return errors;
        }

        var count = f.getValueLength(value);
        if (null !== count) {
            if (this.max === this.min && count !== this.min) {
                errors.push(this.exactMessage);
                return errors;
            }
            if (!isNaN(this.max) && count > this.max) {
                errors.push(this.maxMessage);
            }
            if (!isNaN(this.min) && count < this.min) {
                errors.push(this.minMessage);
            }
        }

        return errors;
    };

    this.onCreate = function () {
        this.min = parseInt(this.min);
        this.max = parseInt(this.max);

        this.minMessage = FpJsBaseConstraint.prepareMessage(
            this.minMessage,
            {'{{ limit }}': FpJsBaseConstraint.formatValue(this.min)},
            this.min
        );
        this.maxMessage = FpJsBaseConstraint.prepareMessage(
            this.maxMessage,
            {'{{ limit }}': FpJsBaseConstraint.formatValue(this.max)},
            this.max
        );
        this.exactMessage = FpJsBaseConstraint.prepareMessage(
            this.exactMessage,
            {'{{ limit }}': FpJsBaseConstraint.formatValue(this.min)},
            this.min
        );
    }
}

window.SymfonyComponentValidatorConstraintsCount = SymfonyComponentValidatorConstraintsCount;