import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsNotBlank from './NotBlank';

const constraintNotBlank = new SymfonyComponentValidatorConstraintsNotBlank();
constraintNotBlank.message = '{{ value }} have to be not blank';

test.each([
    [0, []],
    [[0], []],
    ['FpJsFormValidator', []],
    ['', ['\"\" have to be not blank']],
    [null, ['null have to be not blank']],
    [undefined, ['undefined have to be not blank']],
    [[], ['array have to be not blank']],
])(
    'SymfonyComponentValidatorConstraintsNotBlank',
    (value, expected) => {
        expect(constraintNotBlank.validate(value)).toStrictEqual(expected);
    },
);
