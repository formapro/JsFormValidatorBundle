import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsLuhn from './Luhn';

const constraintsLuhn = new SymfonyComponentValidatorConstraintsLuhn();
constraintsLuhn.message = '{{ value }} is not a valid card number';

test.each([
    // Valid Luhn numbers
    ['79927398713', []], // Valid Luhn checksum
    ['4532015112830366', []], // Example credit card number
    ['6011111111111117', []], // Example credit card number
    ['378282246310005', []], // Example AMEX number
    ['5105105105105100', []], // Example MasterCard number
    ['4111111111111111', []], // Example Visa number

    // Invalid Luhn numbers
    ['79927398712', ['"79927398712" is not a valid card number']],
    ['1234567890123456', ['"1234567890123456" is not a valid card number']],
    ['1111111111111111', ['"1111111111111111" is not a valid card number']],
    ['0', ['"0" is not a valid card number']], // Single zero fails Luhn
    ['00', ['"00" is not a valid card number']], // Multiple zeros fail Luhn

    // Non-numeric strings
    ['abc', ['"abc" is not a valid card number']],
    ['4532-0151-1283-0366', ['"4532-0151-1283-0366" is not a valid card number']], // Contains dashes
    ['4532 0151 1283 0366', ['"4532 0151 1283 0366" is not a valid card number']], // Contains spaces
    ['', []], // Empty string
    [null, []], // null
    [undefined, []], // undefined
    [false, []], // false
])(
    'SymfonyComponentValidatorConstraintsLuhn',
    (value, expected) => {
        expect(constraintsLuhn.validate(value)).toStrictEqual(expected);
    },
);
