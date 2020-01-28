import { FpJsFormElement } from '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsChoice from './Choice';

const prepareValidator = (choices, multiple = false, min = 0, max = '-') => {
    const constraintsChoice = new SymfonyComponentValidatorConstraintsChoice();
    constraintsChoice.message = '{{ value }} is not a valid value';
    constraintsChoice.choices = choices;

    if (multiple) {
        constraintsChoice.multiple = true;
        constraintsChoice.min = min;
        constraintsChoice.max = max;

        constraintsChoice.multipleMessage = '{{ value }} is not a valid value';
        constraintsChoice.maxMessage = 'Max error';
        constraintsChoice.minMessage = 'Min error';
    }

    return constraintsChoice;
};

const element = new FpJsFormElement();

test.each([
    [prepareValidator([]), null, []],
    [prepareValidator([1,2,3]), 1, []],
    [prepareValidator([1,2,3]), 5, ['5 is not a valid value']],
    [prepareValidator(['a', 'b']), 'a', []],
    [prepareValidator(['a', 'b']), 'c', ['\"c\" is not a valid value']],
    [prepareValidator([1,2,3,4,5], true, 3), 2, ['Min error']],
    [prepareValidator([1,2,3,4,5], true, 3), [1,2,3,4], []],
    [prepareValidator([1,2,3,4,5], true, 3, 5), 2, ['Min error']],
    [prepareValidator([1,2,3,4,5], true, 1, 5), [1,2,3,4,5], []],
    [prepareValidator([1,2,3,4,5], true, 1, 5), [1,2,3,4,5,6], ['6 is not a valid value', 'Max error']],
    [prepareValidator([1,2,3,4,5], true, 5, 5), [6], ['6 is not a valid value', 'Min error']],
])(
    'SymfonyComponentValidatorConstraintsChoice',
    (constraintsChoice, value, expected) => {
        expect(constraintsChoice.validate(value, element)).toStrictEqual(expected);
    },
);
