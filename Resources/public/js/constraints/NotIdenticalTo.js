/**
 * Checks if value is not identical to the predefined value
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsNotIdenticalTo() {
    this.message = '';
    this.value = null;

    this.validate = function(value) {
        return (this.value === value)
            ? this.message
                .replace('{{ value }}', String(value))
                .replace('{{ compared_value }}', String(this.value))
                .replace('{{ compared_value_type }}', String(this.value))
            : null;
    }
}
