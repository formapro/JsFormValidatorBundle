//noinspection JSUnusedGlobalSymbols
/**
 * Validates that a value passes the Luhn algorithm (typically a credit card number)
 * @constructor
 * @author dalexandre@jolicode.com
 */
export default function SymfonyComponentValidatorConstraintsLuhn() {
    this.message = '';

    this.validate = function (value) {
        var errors = [];
        var f = FpJsFormValidator;

        if (f.isValueEmty(value)) {
            return errors;
        }

        // Work with strings only
        var strValue = String(value);

        // Check if the value contains only digits
        if (!/^\d+$/.test(strValue)) {
            errors.push(this.message.replace('{{ value }}', FpJsBaseConstraint.formatValue(value)));
            return errors;
        }

        // Luhn algorithm
        var checkSum = 0;
        var length = strValue.length;

        for (var i = length - 1; i >= 0; i--) {
            var digit = parseInt(strValue.charAt(i), 10);

            if ((i % 2) ^ (length % 2)) {
                // Add every second digit starting from the last
                checkSum += digit;
            } else {
                // Double every second digit and add it to the check sum
                // For doubles greater than 9, sum the individual digits
                var doubled = digit * 2;
                checkSum += (doubled >= 10) ? (Math.floor(doubled / 10) + (doubled % 10)) : doubled;
            }
        }

        // Checksum must be non-zero and a multiple of 10
        if (0 === checkSum || 0 !== checkSum % 10) {
            errors.push(this.message.replace('{{ value }}', FpJsBaseConstraint.formatValue(value)));
        }

        return errors;
    }
}

window.SymfonyComponentValidatorConstraintsLuhn = SymfonyComponentValidatorConstraintsLuhn;
