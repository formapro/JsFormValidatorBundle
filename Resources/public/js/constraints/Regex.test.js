import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsRegex from './Regex';

const constraintsRegex = new SymfonyComponentValidatorConstraintsRegex();
constraintsRegex.message = '{{ value }} is not matched';

test.each([
    [/a|b/, 'a', []],
    [/a|b/, 'bbb', []],
    [/a|b/, 'c', ['\"c\" is not matched']],
])(
    'SymfonyComponentValidatorConstraintsRegex',
    (pattern, value, expected) => {
        constraintsRegex.pattern = pattern;
        expect(constraintsRegex.validate(value)).toStrictEqual(expected);
    },
);
