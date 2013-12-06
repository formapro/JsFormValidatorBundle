//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is a number and is between min and max values
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsRange() {
    this.maxMessage = '';
    this.minMessage = '';
    this.invalidMessage = '';
    this.max = null;
    this.min = null;

    this.validate = function(value) {
        if (isNaN(value)) {
            return this.invalidMessage
                .replace('{{ value }}', String(value));
        }
        if (!isNaN(this.max) && value > this.max) {
            return this.maxMessage
                .replace('{{ value }}', String(value))
                .replace('{{ limit }}', this.max);
        }
        if (!isNaN(this.min) && value < this.min) {
            return this.minMessage
                .replace('{{ value }}', String(value))
                .replace('{{ limit }}', this.min);
        }

        return null;
    };

    this.onCreate = function() {
        this.min = parseInt(this.min);
        this.max = parseInt(this.max);
    }
}
