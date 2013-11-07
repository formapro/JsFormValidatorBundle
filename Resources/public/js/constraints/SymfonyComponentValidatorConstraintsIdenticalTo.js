/**
 * Checks if value is identical to the predefined value
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsIdenticalTo() {
    this.compareValues = function(value_1, value_2) {
        return value_1 === value_2;
    };
}
SymfonyComponentValidatorConstraintsIdenticalTo.prototype = new FpJsComparsionModel();
