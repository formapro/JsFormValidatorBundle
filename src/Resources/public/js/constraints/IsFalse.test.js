import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsIsFalse from './IsFalse';

const constraintsIsFalse = new SymfonyComponentValidatorConstraintsIsFalse();
constraintsIsFalse.message = '{{ value }} is not false';

test.each([
    [false, []],
    [0, ['0 is not false']],
    [null, ['null is not false']],
    [true, ['true is not false']],
    ['1', ['\"1\" is not false']],
    [1, ['1 is not false']],
])(
    'SymfonyComponentValidatorConstraintsIsFalse',
    (value, expected) => {
        expect(constraintsIsFalse.validate(value)).toStrictEqual(expected);
    },
);
