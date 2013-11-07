/**
 * Checks if value is like an URL address
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsUrl() {
    this.validate = function(value) {
        if (this.isEmtyValue(value)) {
            return;
        }

        var regexp = /(ftp|https?):\/\/(www\.)?[\w\-\.@:%_\+~#=]+\.\w{2,3}(\/\w+)*(\?.*)?/;
        if (!regexp.test(value)) {
            this.addError(value);
        }
    }
}
SymfonyComponentValidatorConstraintsUrl.prototype = new FpJsConstraintModel();