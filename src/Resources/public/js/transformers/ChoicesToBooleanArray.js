//noinspection JSUnusedGlobalSymbols
/**
 * Created by ymaltsev on 11/29/13.
 */
export default function SymfonyComponentFormExtensionCoreDataTransformerChoicesToBooleanArrayTransformer() {
    this.choiceList = {};

    this.reverseTransform = function(value){
        if (typeof value !== 'object') {
            throw new Error('Unexpected value type')
        }

        var result = [];
        var unknown = [];
        for (var i in value) {
            if (value[i]) {
                if (undefined !== this.choiceList[i]) {
                    result.push(this.choiceList[i]);
                } else {
                    unknown.push(i);
                }
            }
        }

        if (unknown.length) {
            throw new Error('The choices "'+unknown.join(', ')+'" were not found.');
        }

        return result;
    }
}

window.SymfonyComponentFormExtensionCoreDataTransformerChoicesToBooleanArrayTransformer = SymfonyComponentFormExtensionCoreDataTransformerChoicesToBooleanArrayTransformer;