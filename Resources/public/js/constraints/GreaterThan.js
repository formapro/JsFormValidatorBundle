//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is greater than the predefined value
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsGreaterThan() {
    this.message = '';
    this.value = null;

    this.validate = function (value) {
        if ('' === value) {
            return [];
        }

        if (value > this.value) {
            return [];
        } else {
            return [
                this.message
                    .replace('{{ value }}', String(this.value))
                    .replace('{{ compared_value }}', String(this.value))
            ];
        }
    }
}
