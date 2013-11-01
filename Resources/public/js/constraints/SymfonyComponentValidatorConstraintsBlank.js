/**
 * Check if value is blank
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsBlank() {
    this.validate = function(value) {
        if ('' !== value && null !== value) {
            this.addError(value);
        }
    }
}
SymfonyComponentValidatorConstraintsBlank.prototype = new FpJsConstraintModel();
