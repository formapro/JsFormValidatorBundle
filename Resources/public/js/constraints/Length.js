/**
 * Checks minimum and maximum length
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsLength() {
    this.maxMessage = '';
    this.minMessage = '';
    this.exactMessage = '';
    this.max = null;
    this.min = null;

    this.validate = function(value) {
        if (typeof value == 'number' || typeof value == 'string' || value instanceof Array) {
            var length = value.length;
            if (this.max === this.min && length !== this.min) {
                return this.exactMessage
                    .replace('{{ value }}', String(value))
                    .replace('{{ limit }}', this.min);
            }
            if (!isNaN(this.max) && length > this.max) {
                return this.maxMessage
                    .replace('{{ value }}', String(value))
                    .replace('{{ limit }}', this.max);
            }
            if (!isNaN(this.min) && length < this.min) {
                return this.minMessage
                    .replace('{{ value }}', String(value))
                    .replace('{{ limit }}', this.min);
            }
        }

        return null;
    };

    this.onCreate = function() {
        this.min = parseInt(this.min);
        this.max = parseInt(this.max);
    }
}
