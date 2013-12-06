//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is a date
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsDate() {
    this.message = '';

    this.validate = function(value) {
        if (value) {
            var regexp = /^(\d{4})-(\d{2})-(\d{2})$/;
            return (!regexp.test(value))
                ? this.message.replace('{{ value }}', String(value))
                : null;
        }

        return null;
    }
}
