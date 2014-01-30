//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is (bool) false
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsFalse() {
    this.message = '';

    this.validate = function (value) {
        if ('' === value) {
            return [];
        }

        var errors = [];
        if (false !== value) {
            errors.push(this.message.replace('{{ value }}', value));
        }

        return errors;
    }
}
