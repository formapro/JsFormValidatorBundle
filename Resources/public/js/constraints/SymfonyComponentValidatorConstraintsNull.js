/**
 * Checks if value is null
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsNull() {
    this.validate = function(value) {
        if (null !== value) {
            this.addError(value);
        }
    }
}
SymfonyComponentValidatorConstraintsNull.prototype = new FpJsConstraintModel();
