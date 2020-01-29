import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsLength from './Length';

const constraintsLength = new SymfonyComponentValidatorConstraintsLength();
constraintsLength.maxMessage = 'max error';
constraintsLength.minMessage = 'min error';
constraintsLength.exactMessage = 'exact error';

test.each([
    [1, 1, [1], []],
    [1, 3, [1], []],
    [1, 3, [1,2], []],
    [1, 3, [], ['min error']],
    [1, 2, [1,2,3], ['max error']],
    [2, 2, [1,2,3], ['exact error']],
])(
    'SymfonyComponentValidatorConstraintsLength',
    (min, max, value, expected) => {
        constraintsLength.min = min;
        constraintsLength.max = max;
        expect(constraintsLength.validate(value)).toStrictEqual(expected);
    },
);
