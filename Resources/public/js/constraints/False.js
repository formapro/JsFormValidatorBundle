//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is (bool) false
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsFalse() {
    this.message = '';

    this.validate = function(value) {
        return (false !== value)
            ? [this.message.replace('{{ value }}', value)]
            : null;
    }
}
