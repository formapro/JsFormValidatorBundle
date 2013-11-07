/**
 * Checks if value is a time string
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsTime() {
    this.validate = function(value) {
        if (this.isEmtyValue(value)) {
            return;
        }

        var regexp = /^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/;
        if (!regexp.test(value)) {
            this.addError(value);
        }
    }
}
SymfonyComponentValidatorConstraintsTime.prototype = new FpJsConstraintModel();