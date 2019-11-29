//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is equal to the predefined value
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
export default function SymfonyComponentValidatorConstraintsEqualTo() {
    this.message = '';
    this.value = null;

    this.validate = function (value) {
        var errors = [];
        var f = FpJsFormValidator;

        if (!f.isValueEmty(value) && this.value != value) {
            errors.push(
                this.message
                    .replace('{{ value }}', FpJsBaseConstraint.formatValue(value))
                    .replace('{{ compared_value }}', FpJsBaseConstraint.formatValue(this.value))
                    .replace('{{ compared_value_type }}', FpJsBaseConstraint.formatValue(this.value))
            );
        }

        return errors;
    }
}

window.SymfonyComponentValidatorConstraintsEqualTo = SymfonyComponentValidatorConstraintsEqualTo;