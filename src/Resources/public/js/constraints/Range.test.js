import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsRange from './Range';

const createConstraint = () => {
    const constraintsRange = new SymfonyComponentValidatorConstraintsRange();
    constraintsRange.maxMessage = 'max error';
    constraintsRange.minMessage = 'min error';
    constraintsRange.invalidMessage = 'invalid';

    return constraintsRange;
};

test.each([
    [1, 1, 1, []],
    [1, 5, 3, []],
    [1, 1, 'a', ['invalid']],
    [1, 5, 6, ['max error']],
    [5, 10, 3, ['min error']],
])(
    'SymfonyComponentValidatorConstraintsRange',
    (min, max, value, expected) => {
        const constraintsRange = createConstraint();
        constraintsRange.min = min;
        constraintsRange.max = max;
        expect(constraintsRange.validate(value)).toStrictEqual(expected);
    },
);

test.each([
    [1, 5, 0, ['range error from 1 to 5 for 0']],
    [1, 5, 6, ['range error from 1 to 5 for 6']],
    [1, 5, 3, []],
])(
    'SymfonyComponentValidatorConstraintsRange.notInRangeMessage',
    (min, max, value, expected) => {
        const constraintsRange = createConstraint();
        constraintsRange.notInRangeMessage = 'range error from {{ min }} to {{ max }} for {{ value }}';
        constraintsRange.min = min;
        constraintsRange.max = max;
        expect(constraintsRange.validate(value)).toStrictEqual(expected);
    },
);
