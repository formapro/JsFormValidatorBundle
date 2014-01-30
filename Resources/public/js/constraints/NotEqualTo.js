//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is not equal to the predefined value
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsNotEqualTo() {
    this.message = '';
    this.value = null;

    this.validate = function (value) {
        var errors = [];
        if (this.value == value) {
            errors.push(
                this.message
                    .replace('{{ value }}', String(this.value))
                    .replace('{{ compared_value }}', String(this.value))
                    .replace('{{ compared_value_type }}', String(this.value))
            );
        }

        return errors;
    }
}
