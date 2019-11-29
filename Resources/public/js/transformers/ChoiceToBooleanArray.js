//noinspection JSUnusedGlobalSymbols
/**
 * Created by ymaltsev on 11/29/13.
 */
export default function SymfonyComponentFormExtensionCoreDataTransformerChoiceToBooleanArrayTransformer() {
    this.choiceList = {};
    this.placeholderPresent = false;

    this.reverseTransform = function(value){
        if (typeof value !== 'object') {
            throw new Error('Unexpected value type')
        }

        for (var i in value) {
            if (value[i]) {
                if (undefined !== this.choiceList[i]) {
                    return this.choiceList[i] === '' ? null : this.choiceList[i];
                } else if (this.placeholderPresent && 'placeholder' == i) {
                    return null;
                } else {
                    throw new Error('The choice "' + i + '" does not exist');
                }
            }
        }

        return null;
    }
}

window.SymfonyComponentFormExtensionCoreDataTransformerChoiceToBooleanArrayTransformer = SymfonyComponentFormExtensionCoreDataTransformerChoiceToBooleanArrayTransformer;