//noinspection JSUnusedGlobalSymbols
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

    this.validate = function (value) {
        var errors = [];
        if (typeof value == 'number' || typeof value == 'string' || value instanceof Array) {
            var length = value.length;
            if (this.max === this.min && length !== this.min) {
                errors.push(
                    this.exactMessage
                        .replace('{{ value }}', String(value))
                        .replace('{{ limit }}', this.min)
                );

                return errors;
            }
            if (!isNaN(this.max) && length > this.max) {
                errors.push(
                    this.maxMessage
                        .replace('{{ value }}', String(value))
                        .replace('{{ limit }}', this.max)
                );
            }
            if (!isNaN(this.min) && length < this.min) {
                errors.push(
                    this.minMessage
                        .replace('{{ value }}', String(value))
                        .replace('{{ limit }}', this.min)
                );
            }
        }

        return errors;
    };

    this.onCreate = function () {
        this.min = parseInt(this.min);
        this.max = parseInt(this.max);
    }
}
