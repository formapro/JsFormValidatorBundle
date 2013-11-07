/**
 * Checks if value is not blank
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsNotBlank() {
    this.validate = function(value) {
        if ([false, '', [], {}].indexOf(value) >= 0) {
            this.addError(value);
        }
    }
}
SymfonyComponentValidatorConstraintsNotBlank.prototype = new FpJsConstraintModel();
