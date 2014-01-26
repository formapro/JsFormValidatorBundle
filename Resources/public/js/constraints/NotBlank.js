//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is not blank
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsNotBlank() {
    this.message = '';

    this.validate = function (value) {
        var errors = [];
        if ([undefined, null, false, '', [], {}].indexOf(value) >= 0) {
            errors.push(this.message.replace('{{ value }}', String(value)));
        }

        return errors;
    }
}
