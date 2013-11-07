/**
 * Checks if value is not equal to the predefined value
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsNotEqualTo() {
    this.compareValues = function(value_1, value_2) {
        return value_1 != value_2;
    };
}
SymfonyComponentValidatorConstraintsNotEqualTo.prototype = new FpJsComparsionModel();
