/**
 * Check if value is (bool) false
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsFalse() {
    this.validate = function(value) {
        if (false !== value) {
            this.addError(value);
        }
    }
}
SymfonyComponentValidatorConstraintsFalse.prototype = new FpJsConstraintModel();
