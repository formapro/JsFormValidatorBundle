/**
 * Basic model for all the constraint models
 * @constructor
 */
function FpJsConstraintModel() {
    this.errors  = [];
    this.message = '';

    this.isEmtyValue = function(value) {
        return -1 === [undefined, null, ''].indexOf(value);
    };

    this.addError = function(value, message) {
        if (undefined === message) {
            message = this.message;
        }
        this.errors.push(message.replace('{{ value }}', value));
    };

    this.transChoice = function(message, number, parameters) {
        var string = message.split('|');
        var isPlural = (number > 1) && (string.length > 1) ? 1 : 0;
        string = string[isPlural];

        for (var tag in parameters) {
            var regexp = new RegExp(tag, 'g');
            string = string.replace(regexp, parameters[tag]);
        }

        return string;
    };
}