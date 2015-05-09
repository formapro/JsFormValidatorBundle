//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is like an IP address
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsIp() {
    this.message = '';

    this.validate = function (value) {
        var regexp = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
        var errors = [];
        var f = FpJsFormValidator;

        if (!f.isValueEmty(value) && !regexp.test(value)) {
            errors.push(this.message.replace('{{ value }}', FpJsBaseConstraint.formatValue(value)));
        }

        return errors;
    }
}
