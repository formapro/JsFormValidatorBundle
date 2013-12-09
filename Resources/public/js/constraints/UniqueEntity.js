//noinspection JSUnusedGlobalSymbols
/**
 * Created by ymaltsev on 11/26/13.
 */
function SymfonyBridgeDoctrineValidatorConstraintsUniqueEntity() {
    this.message          = 'This value is already used.';
    this.service          = 'doctrine.orm.validator.unique';
    this.em               = null;
    this.repositoryMethod = 'findBy';
    this.fields           = [];
    this.errorPath        = null;
    this.ignoreNull       = true;

    this.groups           = [];

    /**
     * @param {*} value
     * @param {FpJsFormElement} model
     */
    this.validate = function(value, model) {
        var self = this;
        if (typeof this.fields === 'string') {
            this.fields = [this.fields];
        }

        model.addRequest(
            model.getConfig()['routing']['check_unique_entity'],
            {
                message:          this.message,
                service:          this.service,
                em:               this.em,
                repositoryMethod: this.repositoryMethod,
                fields:           this.fields,
                errorPath:        this.errorPath,
                ignoreNull:       this.ignoreNull ? 1 : 0,
                groups:           this.groups,

                entity:           model.getDataClass(),
                data:             this.getValues(model, this.fields)
            },
            function(response){
                response = JSON.parse(response);
                if (false === response) {
                    self.addErrors(model);
                }
            }
        );
    };

    /**
     * @param {FpJsFormElement} model
     * @param {Array} fields
     * @returns {{}}
     */
    this.getValues = function(model, fields) {
        var value;
        var result = {};
        for (var i = 0; i < fields.length; i++) {
            value = model.getChild(this.fields[i]).getValue();
            value = value ? value : '';
            result[fields[i]] = value;
        }

        return result;
    };

    /**
     * @param {FpJsFormElement} model
     */
    this.addErrors = function(model) {
        var fields = this.fields;
        if (null !== this.errorPath) {
            fields = [this.errorPath];
        }
        var values = this.getValues(model, fields);
        for (var i = 0; i < fields.length; i++) {
            var child = model.getChild(fields[i]);
            if (child) {
                var value = String(values[fields[i]]);
                var error = this.message.replace('{{ value }}', value);
                child.addErrors(error);
            }
        }
    }
}
