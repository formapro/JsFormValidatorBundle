/**
 * Checks if value is a datetime string
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsDateTime() {
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
SymfonyComponentValidatorConstraintsDateTime.prototype = new FpJsConstraintModel();