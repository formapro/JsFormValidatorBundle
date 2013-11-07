/**
 * Checks if value is (bool) true
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsTrue() {
    this.validate = function(value) {
        if (true !== value) {
            this.addError(value);
        }
    }
}
SymfonyComponentValidatorConstraintsTrue.prototype = new FpJsConstraintModel();
