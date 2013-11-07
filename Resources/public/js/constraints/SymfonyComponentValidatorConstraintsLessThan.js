/**
 * Checks if value is less than the predefined value
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsLessThan() {
    this.compareValues = function(value_1, value_2) {
        return value_1 < value_2;
    };
}
SymfonyComponentValidatorConstraintsLessThan.prototype = new FpJsComparsionModel();
