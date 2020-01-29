//noinspection JSUnusedGlobalSymbols
/**
 * Checks the value type
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
export default function SymfonyComponentValidatorConstraintsType() {
    this.message = '';
    this.type = '';

    this.validate = function(value) {
        if ('' === value) {
            return [];
        }

        var errors = [];
        var isValid = false;

        switch (this.type) {
            case 'array':
                isValid = (value instanceof Array);
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
                isValid = typeof value === 'number' && value % 1 != 0;
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
                throw 'The wrong "'+this.type+'" type was passed to the Type constraint';
        }

        if (!isValid) {
            errors.push(
                this.message
                    .replace('{{ value }}', FpJsBaseConstraint.formatValue(value))
                    .replace('{{ type }}', this.type)
            );
        }

        return errors;
    }
}

window.SymfonyComponentValidatorConstraintsType = SymfonyComponentValidatorConstraintsType;