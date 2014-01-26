//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is (bool) true
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsTrue() {
    this.message = '';

    this.validate = function(value) {
        var errors = [];
        if (true !== value) {
            errors.push(this.message.replace('{{ value }}', value));
        }

        return errors;
    }
}

