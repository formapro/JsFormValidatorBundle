//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is identical to the predefined value
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsIdenticalTo() {
    this.message = '';
    this.value = null;

    this.validate = function (value) {
        if ('' === value) {
            return [];
        }

        var errors = [];
        if (this.value !== value) {
            errors.push(
                this.message
                    .replace('{{ value }}', String(value))
                    .replace('{{ compared_value }}', String(this.value))
                    .replace('{{ compared_value_type }}', String(this.value))
            );
        }

        return errors;
    }
}
