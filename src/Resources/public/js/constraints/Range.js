//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is a number and is between min and max values
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
export default function SymfonyComponentValidatorConstraintsRange() {
    this.maxMessage = '';
    this.minMessage = '';
    this.invalidMessage = '';
    this.max = null;
    this.min = null;

    this.validate = function (value) {
        var errors = [];
        var f = FpJsFormValidator;

        if (f.isValueEmty(value)) {
            return errors;
        }
        if (isNaN(value)) {
            errors.push(
                this.invalidMessage
                    .replace('{{ value }}', FpJsBaseConstraint.formatValue(value))
            );
        }
        if (!isNaN(this.max) && value > this.max) {
            errors.push(
                this.maxMessage
                    .replace('{{ value }}', FpJsBaseConstraint.formatValue(value))
                    .replace('{{ limit }}', FpJsBaseConstraint.formatValue(this.max))
            );
        }
        if (!isNaN(this.min) && value < this.min) {
            errors.push(
                this.minMessage
                    .replace('{{ value }}', FpJsBaseConstraint.formatValue(value))
                    .replace('{{ limit }}', FpJsBaseConstraint.formatValue(this.min))
            );
        }

        return errors;
    };

    this.onCreate = function () {
        this.min = parseInt(this.min);
        this.max = parseInt(this.max);
    }
}

window.SymfonyComponentValidatorConstraintsRange = SymfonyComponentValidatorConstraintsRange;