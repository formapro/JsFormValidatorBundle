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
            if (length) {
                if (this.max === this.min && length !== this.min) {
                    errors.push(this.exactMessage);
                    return errors;
                }
                if (!isNaN(this.max) && length > this.max) {
                    errors.push(this.maxMessage);
                }
                if (!isNaN(this.min) && length < this.min) {
                    errors.push(this.minMessage);
                }
            }
        }

        return errors;
    };

    this.onCreate = function () {
        this.min = parseInt(this.min);
        this.max = parseInt(this.max);

        this.minMessage = FpJsBaseConstraint.prepareMessage(
            this.minMessage,
            {'{{ limit }}': this.min},
            this.min
        );
        this.maxMessage = FpJsBaseConstraint.prepareMessage(
            this.maxMessage,
            {'{{ limit }}': this.max},
            this.max
        );
        this.exactMessage = FpJsBaseConstraint.prepareMessage(
            this.exactMessage,
            {'{{ limit }}': this.min},
            this.min
        );
    }
}
