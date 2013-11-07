/**
 * Checks if value is equal to the predefined value
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsEqualTo() {
    this.compareValues = function(value_1, value_2) {
        return value_1 == value_2;
    };
}
SymfonyComponentValidatorConstraintsEqualTo.prototype = new FpJsComparsionModel();
