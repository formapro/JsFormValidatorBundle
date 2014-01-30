//noinspection JSUnusedGlobalSymbols
/**
 * Created by ymaltsev on 11/28/13.
 */
function SymfonyComponentFormExtensionCoreDataTransformerBooleanToStringTransformer() {
    this.trueValue = null;

    this.reverseTransform = function(value) {
        if (typeof value === 'boolean') {
            return value;
        } else if (value === this.trueValue) {
            return true;
        } else if (!value) {
            return false;
        } else {
            throw new Error('Wrong type of value');
        }
    }
}