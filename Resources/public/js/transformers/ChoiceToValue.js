//noinspection JSUnusedGlobalSymbols
/**
 * Created by ymaltsev on 11/28/13.
 */
export default function SymfonyComponentFormExtensionCoreDataTransformerChoiceToValueTransformer() {
    this.choiceList = {};

    this.reverseTransform = function(value) {
        for (var i in value) {
            if ('' === value[i]) {
                value.splice(i, 1);
            }
        }

        return value;
    }
}

window.SymfonyComponentFormExtensionCoreDataTransformerChoiceToValueTransformer = SymfonyComponentFormExtensionCoreDataTransformerChoiceToValueTransformer;