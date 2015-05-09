//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is a date
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsDate() {
    this.message = '';

    this.validate = function (value) {
        var regexp = /^(\d{4})-(\d{2})-(\d{2})$/;
        var errors = [];
        var f = FpJsFormValidator;

        if (!f.isValueEmty(value) && !regexp.test(value)) {
            errors.push(this.message.replace('{{ value }}', FpJsBaseConstraint.formatValue(value)));
        }

        return errors;
    }
}
