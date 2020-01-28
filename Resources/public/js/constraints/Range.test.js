import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsRange from './Range';

const constraintsRange = new SymfonyComponentValidatorConstraintsRange();
constraintsRange.maxMessage = 'max error';
constraintsRange.minMessage = 'min error';
constraintsRange.invalidMessage = 'invalid';

test.each([
    [1, 1, 1, []],
    [1, 5, 3, []],
    [1, 1, 'a', ['invalid']],
    [1, 5, 6, ['max error']],
    [5, 10, 3, ['min error']],
])(
    'SymfonyComponentValidatorConstraintsRange',
    (min, max, value, expected) => {
        constraintsRange.min = min;
        constraintsRange.max = max;
        expect(constraintsRange.validate(value)).toStrictEqual(expected);
    },
);

