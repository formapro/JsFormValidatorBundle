/**
 * Checks if value matches to the predefined regexp
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsRegex() {
    this.pattern = '';
    this.match = true;

    this.validate = function(value) {
        if (this.isEmtyValue(value)) {
            return;
        }

        if (!this.pattern.test(value)) {
            this.addError(value);
        }
    };

    this.onCreate = function() {
        var flags = this.pattern.match(/\/(\w*)$/);
        this.pattern = new RegExp(this.pattern.trim().replace(/(^\/)|(\/\w*$)/g, ''), flags[1]);
    }
}
SymfonyComponentValidatorConstraintsRegex.prototype = new FpJsConstraintModel();