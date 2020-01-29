import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsCount from './Count';

const constraintsCount = new SymfonyComponentValidatorConstraintsCount();
constraintsCount.minMessage = 'not min';
constraintsCount.maxMessage = 'not max';
constraintsCount.exactMessage = 'not exact';

test.each([
    [1, 1, [1], []],
    [1, 2, [1], []],
    [2, 2, [1, 2], []],
    [2, 5, [1], ['not min']],
    [1, 2, [1,2,3], ['not max']],
    [2, 2, [1], ['not exact']],
])(
    'SymfonyComponentValidatorConstraintsCount',
    (min, max, value, expected) => {
        constraintsCount.min = min;
        constraintsCount.max = max;
        expect(constraintsCount.validate(value)).toStrictEqual(expected);
    },
);

