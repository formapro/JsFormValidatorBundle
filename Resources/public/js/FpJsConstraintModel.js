/**
 * Basic model for all the constraint models
 * @constructor
 */
function FpJsConstraintModel() {
    this.errors  = [];
    this.message = '';

    this.addError = function(value, message) {
        if (undefined === message) {
            message = this.message;
        }
        this.errors.push(message.replace('{{ value }}', value));
    };
}