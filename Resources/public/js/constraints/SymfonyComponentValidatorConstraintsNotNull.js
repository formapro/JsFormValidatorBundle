/**
 * Checks if value is not null
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsNotNull() {
    this.validate = function(value) {
        if (null === value) {
            this.addError(value);
        }
    }
}
SymfonyComponentValidatorConstraintsNotNull.prototype = new FpJsConstraintModel();