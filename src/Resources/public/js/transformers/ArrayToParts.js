//noinspection JSUnusedGlobalSymbols
/**
 * Created by ymaltsev on 11/28/13.
 */
export default function SymfonyComponentFormExtensionCoreDataTransformerArrayToPartsTransformer() {
    this.partMapping = {};

    this.reverseTransform = function(value) {
        if (typeof value !== 'object') {
            throw new Error('Expected an object.');
        }

        var result = {};
        for (var partKey in this.partMapping) {
            if (undefined !== value[partKey]) {
                var i = this.partMapping[partKey].length;
                while (i--) {
                    var originalKey = this.partMapping[partKey][i];
                    if (undefined !== value[partKey][originalKey]) {
                        result[originalKey] = value[partKey][originalKey];
                    }
                }
            }
        }

        return result;
    }
}

window.SymfonyComponentFormExtensionCoreDataTransformerArrayToPartsTransformer = SymfonyComponentFormExtensionCoreDataTransformerArrayToPartsTransformer;