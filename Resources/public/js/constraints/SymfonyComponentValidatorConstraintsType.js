/**
 * Check the value type
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsType() {
    this.type = '';

    this.validate = function(value) {
        var isValid = true;

        switch (this.type) {
            case 'array':
                isValid = (!value instanceof Array);
                break;

            case 'bool':
            case 'boolean':
                isValid = (typeof value === 'boolean');
                break;
        }
    }
}
SymfonyComponentValidatorConstraintsType.prototype = new FpJsConstraintModel();
