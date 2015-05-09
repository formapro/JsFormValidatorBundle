//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is a time string
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsTime() {
    this.message = '';

    this.validate = function (value) {
        var regexp = /^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/;
        var errors = [];
        var f = FpJsFormValidator;

        if (!f.isValueEmty(value) && !regexp.test(value)) {
            errors.push(this.message.replace('{{ value }}', FpJsBaseConstraint.formatValue(value)));
        }

        return errors;
    }
}
