//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is null
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsNull() {
    this.message = '';

    this.validate = function(value) {
        var errors = [];
        if (null !== value) {
            errors.push(this.message.replace('{{ value }}', String(value)));
        }

        return errors;
    }
}
