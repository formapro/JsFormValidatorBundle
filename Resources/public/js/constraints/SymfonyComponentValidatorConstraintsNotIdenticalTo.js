/**
 * Checks if value is not identical to the predefined value
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsNotIdenticalTo() {
    this.compareValues = function(value_1, value_2) {
        return value_1 !== value_2;
    };
}
SymfonyComponentValidatorConstraintsNotIdenticalTo.prototype = new FpJsComparsionModel();
