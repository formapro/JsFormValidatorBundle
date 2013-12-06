//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is not blank
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsNotBlank() {
    this.message = '';

    this.validate = function(value) {
        return ([undefined, null, false, '', [], {}].indexOf(value) >= 0)
            ? this.message.replace('{{ value }}', String(value))
            : null;
    }
}
