//noinspection JSUnusedGlobalSymbols
/**
 * Created by ymaltsev on 11/26/13.
 */
function FpJsFormValidatorBundleFormConstraintUniqueEntity() {
    this.message          = 'This value is already used.';
    this.service          = 'doctrine.orm.validator.unique';
    this.em               = null;
    this.repositoryMethod = 'findBy';
    this.fields           = [];
    this.errorPath        = null;
    this.ignoreNull       = true;
    this.entityName       = null;

    this.groups           = [];

    /**
     * @param {*} value
     * @param {FpJsFormElement} element
     */
    this.validate = function(value, element) {
        var self   = this;
        var route  = null;
        var config = FpJsFormValidator.config;
        if (config['routing'] && config['routing']['check_unique_entity']) {
            route = config['routing']['check_unique_entity'];
        }

        if (!route) {
            return [];
        }

        FpJsFormValidator.ajax.sendRequest(
            route,
            {
                message:          this.message,
                service:          this.service,
                em:               this.em,
                repositoryMethod: this.repositoryMethod,
                fields:           this.fields,
                errorPath:        this.errorPath,
                ignoreNull:       this.ignoreNull ? 1 : 0,
                groups:           this.groups,

                entityName:       this.entityName,
                data:             this.getValues(element, this.fields)
            },
            function(response){
                response = JSON.parse(response);
                if (false === response) {
                    self.showErrors(element);
                }
            },
            element
        );

        return [];
    };

    this.onCreate = function() {
        if (typeof this.fields === 'string') {
            this.fields = [this.fields];
        }
    };

    /**
     * @param {FpJsFormElement} element
     * @param {Array} fields
     * @returns {{}}
     */
    this.getValues = function(element, fields) {
        var value;
        var result = {};
        for (var i = 0; i < fields.length; i++) {
            value = FpJsFormValidator.getElementValue(element.children[this.fields[i]]);
            value = value ? value : '';
            result[fields[i]] = value;
        }

        return result;
    };

    /**
     * @param {FpJsFormElement} element
     */
    this.showErrors = function(element) {
        var fields = this.fields;
        if (this.errorPath) {
            fields = [this.errorPath];
        }
        var values = this.getValues(element, fields);
        for (var i = 0; i < fields.length; i++) {
            var child = element.children[fields[i]];
            if (child) {
                var value = String(values[fields[i]]);
                var error = this.message.replace('{{ value }}', value);
                element.errors.push(error);
                child.showErrors.apply(element, [[error]]);
                child.postValidate.apply(element, [[error]]);
            }
        }
    }
}
