/**
 * Checks if value is greater than the predefined value
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsGreaterThan() {
    this.compareValues = function(value_1, value_2) {
        return value_1 > value_2;
    };
}
SymfonyComponentValidatorConstraintsGreaterThan.prototype = new FpJsComparsionModel();
