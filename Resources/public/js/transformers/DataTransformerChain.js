/**
 * Created by ymaltsev on 11/22/13.
 */
function SymfonyComponentFormExtensionCoreDataTransformerDataTransformerChain(transformers) {
    this.transformers = transformers;

    this.reverseTransform = function(value, model) {
        var len = this.transformers.length;
        for (var i = 0; i < len; i++) {
            value = this.transformers[i].reverseTransform(value, model);
        }

        return value;
    }
}