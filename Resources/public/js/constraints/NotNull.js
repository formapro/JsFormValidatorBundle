/**
 * Checks if value is not null
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsNotNull() {
    this.message = '';

    this.validate = function(value) {
        return (null === value)
            ? this.message.replace('{{ value }}', String(value))
            : null;
    }
}
