//noinspection JSUnusedGlobalSymbols,JSUnusedGlobalSymbols
/**
 * Checks if value is blank
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsBlank() {
    this.message = '';

    this.validate = function (value) {
        var errors = [];
        var f = FpJsFormValidator;

        if (!f.isValueEmty(value)) {
            errors.push(this.message.replace('{{ value }}', FpJsBaseConstraint.formatValue(value)));
        }

        return errors;
    }
}

