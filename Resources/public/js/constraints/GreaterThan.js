//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is greater than the predefined value
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
export default function SymfonyComponentValidatorConstraintsGreaterThan() {
    this.message = '';
    this.value = null;

    this.validate = function (value) {
        var f = FpJsFormValidator;
        if (f.isValueEmty(value) || value > this.value) {
            return [];
        } else {
            return [
                this.message
                    .replace('{{ value }}', FpJsBaseConstraint.formatValue(value))
                    .replace('{{ compared_value }}', FpJsBaseConstraint.formatValue(this.value))
            ];
        }
    }
}

window.SymfonyComponentValidatorConstraintsGreaterThan = SymfonyComponentValidatorConstraintsGreaterThan;