//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is a date
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsDate() {
    this.message = '';

    this.validate = function (value) {
        var errors = [];
        if (value) {
            var regexp = /^(\d{4})-(\d{2})-(\d{2})$/;
            if (!regexp.test(value)) {
                errors.push(this.message.replace('{{ value }}', String(value)));
            }
        }

        return errors;
    }
}
