/**
 * Created by ymaltsev on 11/28/13.
 */
function SymfonyComponentFormExtensionCoreDataTransformerChoiceToValueTransformer() {
    this.choiceList = {};

    // This transformer just returns values as is, because we actually receive choices (not values) from input fields
    this.reverseTransform = function(value) {
        return value;
    }
}