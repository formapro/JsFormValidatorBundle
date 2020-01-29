import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsTime from './Time';

const constraintsTime = new SymfonyComponentValidatorConstraintsTime();
constraintsTime.message = '{{ value }} is not valid time';

test.each([
    ['', []],
    ['00', ['\"00\" is not valid time']],
    ['00:00', ['\"00:00\" is not valid time']],
    ['00:00:00', []],
    ['61:00:00', ['\"61:00:00\" is not valid time']],
    ['00:60:00', ['\"00:60:00\" is not valid time']],
    ['00:00:60', ['\"00:00:60\" is not valid time']],
])(
    'SymfonyComponentValidatorConstraintsTime',
    (value, expected) => {
        expect(constraintsTime.validate(value)).toStrictEqual(expected);
    },
);
