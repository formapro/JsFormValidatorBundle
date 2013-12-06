/**
 * Created by ymaltsev on 11/21/13.
 */
function FpJsValidationData(options) {
    this.type        = null;
    this.groups      = null;
    this.constraints = [];
    this.getters     = [];
    this.model       = null;

    for (var optionName in options) {
        this[optionName] = options[optionName];
    }

    /**
     * @returns {FpJsValidationData}
     */
    this.initialize = function(model) {
        this.model       = model;
        this.constraints = this.parseConstraints(this.constraints);
        this.getters     = this.parseGetters(this.getters);

        return this;
    };

    /**
     * @returns FpJsFormElement
     */
    this.getModel = function() {
        return this.model;
    };

    /**
     * @param {Object} mapping
     * @returns {Array}
     */
    this.parseConstraints = function(mapping) {
        var i;
        var constraints = [];
        for (var constrName in mapping) {
            if (undefined !== window[constrName]) {
                i = mapping[constrName].length;
                while (i--) {
                    constraints.push(this.createConstraint(constrName, mapping[constrName][i]));
                }
            }
        }

        return constraints;
    };

    /**
     *
     * @param {String} name
     * @param {Object} options
     * @returns {{}}
     */
    this.createConstraint = function(name, options) {
        var constraint = new window[name]();
        if (typeof constraint.validate !== 'function') {
            throw 'The constraint "'+name+'" does not have the "validate" function';
        }

        for (var param in options) {
            constraint[param] = options[param];
        }

        if (typeof constraint['onCreate'] === 'function') {
            constraint['onCreate']();
        }

        if (
            name === 'SymfonyComponentValidatorConstraintsChoice'
            && null !== constraint.callback
            && typeof constraint.callback === 'string'
        ) {
            constraint.callback = [
                this.getModel().getDataClass().replace(/\\/g, ''),
                constraint.callback
            ]
        }

        return constraint;
    };

    /**
     * @param {Object} mapping
     * @returns {Array}
     */
    this.parseGetters = function(mapping) {
        var getters = [];
        for (var getterName in mapping) {
            var model = this.getEntityModel(mapping[getterName]);
            if (model) {
                getters.push({
                    model:       model,
                    method:      mapping[getterName]['name'],
                    constraints: this.parseConstraints(mapping[getterName]['constraints'])
                });
            }
        }

        return getters;
    };

    /**
     *
     * @param {Object} getter
     * @returns {Object}|{null}
     */
    this.getEntityModel = function(getter) {
        if (undefined !== window[getter['class']]) {
            var model = new window[getter['class']]();

            if (typeof model[getter['name']] === 'function') {
                return model;
            }
        }

        return null;
    };

    /**
     * @returns {Array}
     */
    this.getGroups = function() {
        var groups = [];

        if (this.groups instanceof Array) {
            groups = this.groups;
        } else if (this.groups instanceof String && undefined !== window[this.groups]) {
            var model = new window[this.groups]();
            if (typeof model['getValidationGroups'] === 'function') {
                groups = model['getValidationGroups'](this.getModel());
            }
        }

        return groups;
    };

    /**
     * @param {Array} list
     * @returns {Array}
     */
    this.siftConstraints = function(list) {
        var constraints = [];
        var groups = this.getGroups();
        var i = list.length;
        while (i--) {
            if (this.checkValidationGroups(list[i].groups, groups)) {
                constraints.push(list[i]);
            }
        }

        return constraints;
    };

    /**
     * @param {Array} needle
     * @param {Array} haystack
     * @returns {boolean}
     */
    this.checkValidationGroups = function(needle, haystack) {
        var result = false;
        var i = needle.length;
        while (i--) {
            if (-1 !== haystack.indexOf(needle[i])) {
                result = true;
                break;
            }
        }

        return result;
    };

    /**
     * @param value
     * @returns {FpJsValidationData}
     */
    this.validate = function(value) {
        this.validateConstraints(value, this.constraints);
        this.validateGetters();

        return this;
    };

    /**
     * @param value
     * @param {Array} constraints
     * @returns {FpJsValidationData}
     */
    this.validateConstraints = function(value, constraints) {
        constraints = this.siftConstraints(constraints);

        var i = constraints.length;
        while (i--) {
            this
                .getModel()
                .addErrors(
                    constraints[i].validate(value, this.getModel())
                )
            ;
        }

        return this;
    };

    /**
     * @returns {FpJsValidationData}
     */
    this.validateGetters = function() {
        var i = this.getters.length;
        while (i--) {
            var getterModel = this.getters[i].model;
            var method = this.getters[i].method;
            var value = getterModel[method](this.getModel());

            this.validateConstraints(value, this.getters[i].constraints);
        }

        return this;
    };
}