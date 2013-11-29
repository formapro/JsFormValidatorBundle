/**
 * Checks if value is less than the predefined value
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsLessThan() {
    this.message = '';
    this.value = null;

    this.validate = function(value) {
        return (value < this.value)
            ? null
            : this.message
                .replace('{{ value }}', String(value))
                .replace('{{ compared_value }}', String(this.value))
        ;
    }
}
