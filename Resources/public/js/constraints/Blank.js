//noinspection JSUnusedGlobalSymbols,JSUnusedGlobalSymbols
/**
 * Checks if value is blank
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsBlank() {
    this.message = '';

    this.validate = function(value) {
        return ([undefined, null, false, '', [], {}].indexOf(value) === -1)
            ? this.message.replace('{{ value }}', String(value))
            : null;
    }
}

