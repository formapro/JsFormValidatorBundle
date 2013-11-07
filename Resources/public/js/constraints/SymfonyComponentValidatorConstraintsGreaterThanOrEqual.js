/**
 * Checks if value is greater than or equal to the predefined value
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsGreaterThanOrEqual() {
    this.compareValues = function(value_1, value_2) {
        return value_1 >= value_2;
    };
}
SymfonyComponentValidatorConstraintsGreaterThanOrEqual.prototype = new FpJsComparsionModel();
