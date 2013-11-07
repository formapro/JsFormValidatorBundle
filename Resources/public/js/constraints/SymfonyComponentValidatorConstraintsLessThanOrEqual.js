/**
 * Checks if value is less than or equal to the predefined value
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsLessThanOrEqual() {
    this.compareValues = function(value_1, value_2) {
        return value_1 <= value_2;
    };
}
SymfonyComponentValidatorConstraintsLessThanOrEqual.prototype = new FpJsComparsionModel();
