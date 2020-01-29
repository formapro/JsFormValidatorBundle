//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is (bool) false
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
export default function SymfonyComponentValidatorConstraintsIsFalse() {
    this.message = '';

    this.validate = function (value) {
        var errors = [];
        if ('' !== value && false !== value) {
            errors.push(this.message.replace('{{ value }}', FpJsBaseConstraint.formatValue(value)));
        }

        return errors;
    }
}

window.SymfonyComponentValidatorConstraintsIsFalse = SymfonyComponentValidatorConstraintsIsFalse;
window.SymfonyComponentValidatorConstraintsFalse = SymfonyComponentValidatorConstraintsIsFalse;