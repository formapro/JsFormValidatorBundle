//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is not blank
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
export default function SymfonyComponentValidatorConstraintsNotBlank() {
    this.message = '';

    this.validate = function (value) {
        var errors = [];
        var f = FpJsFormValidator;

        if (f.isValueEmty(value)) {
            errors.push(this.message.replace('{{ value }}', FpJsBaseConstraint.formatValue(value)));
        }

        return errors;
    }
}

window.SymfonyComponentValidatorConstraintsNotBlank = SymfonyComponentValidatorConstraintsNotBlank;