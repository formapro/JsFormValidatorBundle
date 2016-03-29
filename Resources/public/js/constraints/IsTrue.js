//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is (bool) true
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsIsTrue() {
    this.message = '';

    this.validate = function(value) {
        if ('' === value) {
            return [];
        }

        var errors = [];
        if (true !== value) {
            errors.push(this.message.replace('{{ value }}', FpJsBaseConstraint.formatValue(value)));
        }

        return errors;
    }
}

