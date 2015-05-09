//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is not null
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsNotNull() {
    this.message = '';

    this.validate = function (value) {
        var errors = [];
        if (null === value) {
            errors.push(this.message.replace('{{ value }}', FpJsBaseConstraint.formatValue(value)));
        }

        return errors;
    }
}
