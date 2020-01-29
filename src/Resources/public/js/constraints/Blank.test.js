import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsBlank from './Blank';

const constraintBlank = new SymfonyComponentValidatorConstraintsBlank();
constraintBlank.message = '{{ value }} have to be blank';

test.each([
    [[], []],
    ['', []],
    [null, []],
    [undefined, []],
    [0, ['0 have to be blank']],
    ['1', ['\"1\" have to be blank']],
    ['a', ['\"a\" have to be blank']],
    ['FpJsFormValidator', ['\"FpJsFormValidator\" have to be blank']]
])(
    'SymfonyComponentValidatorConstraintsBlank.validate',
    (value, expected) => {
        expect(constraintBlank.validate(value)).toStrictEqual(expected);
    },
);
