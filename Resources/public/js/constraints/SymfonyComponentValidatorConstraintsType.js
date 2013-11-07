/**
 * Checks the value type
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsType() {
    this.type = '';

    this.validate = function(value) {
        var isValid = false;

        switch (this.type) {
            case 'array':
                isValid = (!value instanceof Array);
                break;

            case 'bool':
            case 'boolean':
                isValid = (typeof value === 'boolean');
                break;

            case 'callable':
                isValid = (typeof value === 'function');
                break;

            case 'float':
            case 'double':
            case 'real':
                isValid = (value === parseFloat(value));
                break;

            case 'int':
            case 'integer':
            case 'long':
                isValid = (value === parseInt(value));
                break;

            case 'null':
                isValid = (null === value);
                break;

            case 'numeric':
                isValid = !isNaN(value);
                break;

            case 'object':
                isValid = (null !== value) && (typeof value === 'object');
                break;

            case 'scalar':
                isValid = (/boolean|number|string/).test(typeof value);
                break;

            case '':
            case 'string':
                isValid = (typeof value === 'string');
                break;

            // It doesn't have an implementation in javascript
            case 'resource':
                isValid = true;
                break;

            default:
                throw 'Wrong type "'+this.type+'" was passed to the Type constraint';
        }

        return isValid;
    }
}
SymfonyComponentValidatorConstraintsType.prototype = new FpJsConstraintModel();
