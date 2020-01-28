import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsIsTrue from './IsTrue';

const constraintsIsTrue = new SymfonyComponentValidatorConstraintsIsTrue();
constraintsIsTrue.message = '{{ value }} is not true';

test.each([
    [true, []],
    [1, ['1 is not true']],
    [null, ['null is not true']],
    [false, ['false is not true']],
    ['1', ['\"1\" is not true']],
])(
    'SymfonyComponentValidatorConstraintsIsTrue',
    (value, expected) => {
        expect(constraintsIsTrue.validate(value)).toStrictEqual(expected);
    },
);
