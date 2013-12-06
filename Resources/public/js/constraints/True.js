//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is (bool) true
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsTrue() {
    this.message = '';

    this.validate = function(value) {
        return (true !== value)
            ? [this.message.replace('{{ value }}', value)]
            : null;
    }
}

