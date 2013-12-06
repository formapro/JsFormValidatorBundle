//noinspection JSUnusedGlobalSymbols
/**
 * Checks count of an array or object
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsCount() {
    this.maxMessage = '';
    this.minMessage = '';
    this.exactMessage = '';
    this.max = null;
    this.min = null;

    this.validate = function(value) {
        var count = null;
        if (value instanceof Array) {
            count = value.length;
        } else if (typeof value == 'object') {
            count = 0;
            for (var propName in value) {
                if (value.hasOwnProperty(propName)) {
                    count++;
                }
            }
        }

        if (null !== count) {
            if (this.max === this.min && count !== this.min) {
                return this.exactMessage
                    .replace('{{ value }}', String(value))
                    .replace('{{ limit }}', this.min);
            }
            if (!isNaN(this.max) && count > this.max) {
                return this.maxMessage
                    .replace('{{ value }}', String(value))
                    .replace('{{ limit }}', this.max);
            }
            if (!isNaN(this.min) && count < this.min) {
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
