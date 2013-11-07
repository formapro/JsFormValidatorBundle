/**
 * Basic model for all the constraint models
 * @constructor
 */
function FpJsComparsionModel() {
    this.value = undefined;
    this.errors  = [];
    this.message = '';

    this.validate = function(value) {
        if (typeof this['compareValues'] === 'function' && !this['compareValues'](value, this.value)) {
            this.errors.push(this.message.replace('{{ value }}', value));
        }
    };

    this.onCreate = function() {
        this.message = this.message.replace('{{ compared_value }}', this.value);
    };
}