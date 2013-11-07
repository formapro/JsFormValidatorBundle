/**
 * Checks if value is a date
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsDate() {
    this.validate = function(value) {
        if (this.isEmtyValue(value)) {
            return;
        }

        var date = new Date(value);
        if (!date) {
            this.addError(value);
        }
    }
}
SymfonyComponentValidatorConstraintsDate.prototype = new FpJsConstraintModel();