import { FpJsFormElement } from '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsUrl from './Url';

const constraintsUrl = new SymfonyComponentValidatorConstraintsUrl();
constraintsUrl.message = '{{ value }} is not valid url';

const element = new FpJsFormElement();

test.each([
    ['http://www.google.com', []],
    ['http://stackoverflow.com/questions/', []],
    ['http://stackoverflow.com/questions/', []],
    ['http://google.cz/search?hl=en&sxsrf=ACY', []],
    ['a', ['\"a\" is not valid url']],
    ['http://a', ['\"http://a\" is not valid url']],
])(
    'SymfonyComponentValidatorConstraintsUrl',
    (value, expected) => {
        element.domNode = { value };
        expect(constraintsUrl.validate(value, element)).toStrictEqual(expected);
    },
);
