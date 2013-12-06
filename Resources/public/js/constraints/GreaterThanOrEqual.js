//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is greater than or equal to the predefined value
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsGreaterThanOrEqual() {
    this.message = '';
    this.value = null;

    this.validate = function(value) {
        return (value >= this.value)
            ? null
            : this.message
            .replace('{{ value }}', String(value))
            .replace('{{ compared_value }}', String(this.value))
            ;
    }
}
