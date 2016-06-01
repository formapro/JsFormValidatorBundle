function FpJsFormElement() {
    this.id = '';
    this.name = '';
    this.type = '';
    this.invalidMessage = '';
    this.cascade = false;
    this.bubbling = false;
    this.disabled = false;
    this.transformers = [];
    this.data = {};
    this.children = {};
    this.parent = null;
    this.domNode = null;

    this.callbacks = {};
    this.errors = {};

    this.groups = function () {
        return ['Default'];
    };

    this.validate = function () {
        if (this.disabled) {
            return true;
        }

        var self = this;
        var sourceId = 'form-error-' + String(this.id).replace(/_/g, '-');
        self.errors[sourceId] = [];

        if (this.domNode && this.domNode.disabled) {
            return true;
        }

        self.errors[sourceId] = FpJsFormValidator.validateElement(self);

        var errorPath = FpJsFormValidator.getErrorPathElement(self);
        var domNode = errorPath.domNode;
        if (!domNode) {
            for (var childName in errorPath.children) {
                var childDomNode = errorPath.children[childName].domNode;
                if (childDomNode) {
                    domNode = childDomNode;
                    break;
                }
            }
        }
        errorPath.showErrors.apply(domNode, [self.errors[sourceId], sourceId]);

        return self.errors[sourceId].length == 0;
    };

    this.validateRecursively = function () {
        this.validate();
        for (var childName in this.children) {
            this.children[childName].validateRecursively();
        }
    };

    this.isValid = function () {
        for (var id in this.errors) {
            if (this.errors[id].length > 0) {
                return false;
            }
        }

        for (var childName in this.children) {
            if (!this.children[childName].isValid()) {
                return false;
            }
        }

        return true;
    };

    this.showErrors = function (errors, sourceId) {
        if (!(this instanceof HTMLElement)) {
            return;
        }
        //noinspection JSValidateTypes
        /**
         * @type {HTMLElement}
         */
        var domNode = this;
        var ul = FpJsFormValidator.getDefaultErrorContainerNode(domNode);
        if (ul) {
            var len = ul.childNodes.length;
            while (len--) {
                if (sourceId == ul.childNodes[len].className) {
                    ul.removeChild(ul.childNodes[len]);
                }
            }
        }

        if (!errors.length) {
            if (ul && !ul.childNodes) {
                ul.parentNode.removeChild(ul);
            }
            return;
        }

        if (!ul) {
            ul = document.createElement('ul');
            ul.className = FpJsFormValidator.errorClass;
            domNode.parentNode.insertBefore(ul, domNode);
        }

        var li;
        for (var i in errors) {
            li = document.createElement('li');
            li.className = sourceId;
            li.innerHTML = errors[i];
            ul.appendChild(li);
        }
    };

    this.onValidate = function (errors, event) {
    };

    this.submitForm = function (form) {
        form.submit();
    };
}

function FpJsAjaxRequest() {
    this.queue = 0;
    this.callbacks = [];

    this.sendRequest = function (path, data, callback) {
        var self = this;
        var request = this.createRequest();

        try {
            request.open("POST", path, true);
            request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            request.onreadystatechange = function () {
                if (4 == request.readyState && 200 == request.status) {
                    callback(request.responseText);
                    self.queue--;
                    self.checkQueue();
                }
            };

            request.send(this.serializeData(data, null));
            self.queue++;
        } catch (e) {
            console.log(e.message);
        }
    };

    this.checkQueue = function () {
        if (0 == this.queue) {
            for (var i in this.callbacks) {
                this.callbacks[i]();
            }
        }
    };

    this.serializeData = function (obj, prefix) {
        var queryParts = [];
        for (var paramName in obj) {
            var key = prefix
                ? prefix + "[" + paramName + "]"
                : paramName;

            var child = obj[paramName];

            queryParts.push(
                (typeof child == "object")
                    ? this.serializeData(child, key)
                    : encodeURIComponent(key) + "=" + encodeURIComponent(child)
            );
        }

        return queryParts.join("&");
    };

    /**
     * @return {XMLHttpRequest}
     */
    this.createRequest = function () {
        var request = null;
        if (window.XMLHttpRequest) {
            //IE7+, Firefox, Chrome, Opera, Safari
            request = new XMLHttpRequest();
        } else {
            //IE6, IE5
            try {
                request = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {
            }
            try {
                request = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
            }
            try {
                request = new ActiveXObject("Msxml2.XMLHTTP.6.0");
            } catch (e) {
            }
            try {
                request = new ActiveXObject("Msxml2.XMLHTTP.3.0");
            } catch (e) {
            }
        }

        return request;
    };
}

function FpJsCustomizeMethods() {
    this.init = function (options) {
        FpJsFormValidator.each(this, function (item) {
            if (!item.jsFormValidator) {
                item.jsFormValidator = {};
            }

            for (var optName in options) {
                switch (optName) {
                    case 'customEvents':
                        options[optName].apply(item);
                        break;
                    default:
                        item.jsFormValidator[optName] = options[optName];
                        break;
                }
            }
        }, false);

        return this;
    };

    this.validate = function (opts) {
        var isValid = true;
        //noinspection JSCheckFunctionSignatures
        FpJsFormValidator.each(this, function (item) {
            var method = (opts && true === opts['recursive'])
                ? 'validateRecursively'
                : 'validate';

            var validateUnique = (!opts || false !== opts['findUniqueConstraint']);
            if (validateUnique && item.jsFormValidator.parent) {
                var data = item.jsFormValidator.parent.data;
                if (data['entity'] && data['entity']['constraints']) {
                    for (var i in data['entity']['constraints']) {
                        var constraint = data['entity']['constraints'][i];
                        if (constraint instanceof FpJsFormValidatorBundleFormConstraintUniqueEntity && constraint.fields.indexOf(item.jsFormValidator.name) > -1) {
                            var owner = item.jsFormValidator.parent;
                            constraint.validate(null, owner);
                        }
                    }
                }
            }

            if (!item.jsFormValidator[method]()) {
                isValid = false;
            }
        });

        return isValid;
    };

    this.showErrors = function (opts) {
        //noinspection JSCheckFunctionSignatures
        FpJsFormValidator.each(this, function (item) {
            item.jsFormValidator.errors[opts['sourceId']] = opts['errors'];
            item.jsFormValidator.showErrors.apply(item, [opts['errors'], opts['sourceId']]);
        });
    };

    this.submitForm = function (event) {
        //noinspection JSCheckFunctionSignatures
        FpJsFormValidator.each(this, function (item) {
            var element = item.jsFormValidator;
            if (event) {
                event.preventDefault();
            }
            element.validateRecursively();
            if (FpJsFormValidator.ajax.queue) {
                if (event) {
                    event.preventDefault();
                }
                FpJsFormValidator.ajax.callbacks.push(function () {
                    element.onValidate.apply(element.domNode, [FpJsFormValidator.getAllErrors(element, {}), event]);
                    if (element.isValid()) {
                        element.submitForm.apply(item, [item]);
                    }
                });
            } else {
                element.onValidate.apply(element.domNode, [FpJsFormValidator.getAllErrors(element, {}), event]);
                if (element.isValid()) {
                    element.submitForm.apply(item, [item]);
                }
            }
        });
    };

    this.get = function () {
        var elements = [];
        //noinspection JSCheckFunctionSignatures
        FpJsFormValidator.each(this, function (item) {
            elements.push(item.jsFormValidator);
        });

        return elements;
    };

    //noinspection JSUnusedGlobalSymbols
    this.addPrototype = function(name) {
        //noinspection JSCheckFunctionSignatures
        FpJsFormValidator.each(this, function (item) {
            var prototype = FpJsFormValidator.preparePrototype(
                FpJsFormValidator.cloneObject(item.jsFormValidator.prototype),
                name,
                item.jsFormValidator.id + '_' + name
            );
            item.jsFormValidator.children[name] = FpJsFormValidator.createElement(prototype);
            item.jsFormValidator.children[name].parent = item.jsFormValidator;
        });
    };

    //noinspection JSUnusedGlobalSymbols
    this.delPrototype = function(name) {
        //noinspection JSCheckFunctionSignatures
        FpJsFormValidator.each(this, function (item) {
            delete (item.jsFormValidator.children[name]);
        });
    };
}

var FpJsBaseConstraint = {
    prepareMessage: function (message, params, plural) {
        var realMsg = message;
        var listMsg = message.split('|');
        if (listMsg.length > 1) {
            if (plural == 1) {
                realMsg = listMsg[0];
            } else {
                realMsg = listMsg[1];
            }
        }

        for (var paramName in params) {
            var regex = new RegExp(paramName, 'g');
            realMsg = realMsg.replace(regex, params[paramName]);
        }

        return realMsg;
    },

    formatValue: function (value) {
        switch (Object.prototype.toString.call(value)) {
            case '[object Date]':
                return value.format('Y-m-d H:i:s');

            case '[object Object]':
                return 'object';

            case '[object Array]':
                return 'array';

            case '[object String]':
                return '"' + value + '"';

            case '[object Null]':
                return 'null';

            case '[object Boolean]':
                return value ? 'true' : 'false';

            default:
                return String(value);
        }
    }
};

var FpJsFormValidator = new function () {
    this.forms = {};
    this.errorClass = 'form-errors';
    this.config = {};
    this.ajax = new FpJsAjaxRequest();
    this.customizeMethods = new FpJsCustomizeMethods();
    this.constraintsCounter = 0;

    function elementIsType(element, type) {
        return element.type.indexOf(type) >= 0;
    }

    //noinspection JSUnusedGlobalSymbols
    this.addModel = function (model, onLoad) {
        var self = this;
        if (!model) return;
        if (onLoad !== false) {
            this.onDocumentReady(function () {
                self.forms[model.id] = self.initModel(model);
            });
        } else {
            self.forms[model.id] = self.initModel(model);
        }
    };

    this.onDocumentReady = function (callback) {
        var addListener = document.addEventListener || document.attachEvent;
        var removeListener = document.removeEventListener || document.detachEvent;
        var eventName = document.addEventListener ? "DOMContentLoaded" : "onreadystatechange";

        addListener.call(document, eventName, function () {
            removeListener.call(this, eventName, arguments.callee, false);
            callback();
        }, false)
    };

    /**
     * @param {Object} model
     */
    this.initModel = function (model) {
        var element = this.createElement(model);
        var form = this.findFormElement(element);
        element.domNode = form;
        this.attachElement(element);
        if (form) {
            this.attachDefaultEvent(element, form);
        }

        return element;
    };

    /**
     * @param {Object} model
     *
     * @return {FpJsFormElement}
     */
    this.createElement = function (model) {
        var element = new FpJsFormElement();
        element.domNode = this.findDomElement(model);
        if (model.children instanceof Array && !model.length && !element.domNode) {
            return null;
        }

        for (var key in model) {
            if ('children' == key) {
                for (var childName in model.children) {
                    var childElement = this.createElement(model.children[childName]);
                    if (childElement) {
                        element.children[childName] = childElement;
                        element.children[childName].parent = element;
                    }
                }
            } else if ('transformers' == key) {
                element.transformers = this.parseTransformers(model[key]);
            } else {
                element[key] = model[key];
            }
        }

        // Parse constraints
        for (var type in element.data) {
            var constraints = [];
            if (element.data[type].constraints) {
                constraints = this.parseConstraints(element.data[type].constraints);
            }
            element.data[type].constraints = constraints;

            var getters = {};
            if (element.data[type].getters) {
                for (var getterName in element.data[type].getters) {
                    getters[getterName] = this.parseConstraints(element.data[type].getters[getterName]);
                }
            }
            element.data[type].getters = getters;
        }

        this.attachElement(element);

        return element;
    };

    /**
     * @param {FpJsFormElement} element
     */
    this.validateElement = function (element) {
        var errors = [];
        var value = this.getElementValue(element);
        for (var type in element.data) {

            if (!this.checkParentCascadeOption(element) && 'entity' == type) {
                continue;
            }

            if (element.parent && !this.checkParentCascadeOption(element.parent) && 'parent' == type) {
                continue;
            }


            // Evaluate groups
            var groupsValue = element.data[type]['groups'];
            if (typeof groupsValue == "string") {
                groupsValue = this.getParentElementById(groupsValue, element).groups.apply(element.domNode);
            }
            errors = errors.concat(this.validateConstraints(
                value,
                element.data[type]['constraints'],
                groupsValue,
                element
            ));

            for (var getterName in element.data[type]['getters']) {
                if (typeof element.callbacks[getterName] == "function") {
                    var receivedValue = element.callbacks[getterName].apply(element.domNode);
                    errors = errors.concat(this.validateConstraints(
                        receivedValue,
                        element.data[type]['getters'][getterName],
                        groupsValue,
                        element
                    ));
                }
            }
        }

        return errors;
    };

    this.checkParentCascadeOption = function (element) {
        var result = true;
        if (element.parent && !element.parent.cascade && !elementIsType(element.parent, 'collection')) {
            result = false;
        } else if (element.parent) {
            result = this.checkParentCascadeOption(element.parent);
        }

        return result;
    };

    /**
     * @param value
     * @param {Array} constraints
     * @param {Array} groups
     * @param {FpJsFormElement} owner
     *
     * @return {Array}
     */
    this.validateConstraints = function (value, constraints, groups, owner) {
        var errors = [];
        var i = constraints.length;
        while (i--) {
            if (this.checkValidationGroups(groups, constraints[i])) {
                errors = errors.concat(constraints[i].validate(value, owner));
            }
        }

        return errors;
    };

    /**
     * @param {Array} needle
     * @param {Array} haystack
     * @return {boolean}
     */
    this.checkValidationGroups = function (needle, constraint) {
        var result = false;
        var i = needle.length;
        // For symfony 2.6 Api
        var haystack = constraint.groups || ['Default'];
        while (i--) {
            if (-1 !== haystack.indexOf(needle[i])) {
                result = true;
                break;
            }
        }

        return result;
    };

    /**
     * @param {FpJsFormElement} element
     */
    this.getElementValue = function (element) {
        var i = element.transformers.length;
        var value = this.getInputValue(element);

        if (i && undefined === value) {
            value = this.getMappedValue(element);
        } else if (elementIsType(element, 'collection')) {
            value = {};
            for (var childName in element.children) {
                value[childName] = this.getMappedValue(element.children[childName]);
            }
        } else {
            value = this.getSpecifiedElementTypeValue(element);
        }

        while (i--) {
            value = element.transformers[i].reverseTransform(value, element);
        }

        return value;
    };

    this.getInputValue = function (element) {
        return element.domNode ? element.domNode.value : undefined;
    };

    this.getMappedValue = function (element) {
        var result = this.getSpecifiedElementTypeValue(element);

        if (undefined === result) {
            result = {};
            for (var childName in element.children) {
                var child = element.children[childName];
                result[child.name] = this.getMappedValue(child);
            }
        }

        return result;
    };

    this.getSpecifiedElementTypeValue = function (element) {
        if (!element.domNode) {
            return undefined;
        }

        var value;
        if (elementIsType(element, 'checkbox') || elementIsType(element, 'radio')) {
            value = element.domNode.checked;
        } else if ('select' === element.domNode.tagName.toLowerCase()) {
            value = [];
            var field = element.domNode;
            var len = field.length;
            while (len--) {
                if (field.options[len].selected) {
                    value.push(field.options[len].value);
                }
            }
        } else {
            value = this.getInputValue(element);
        }

        return value;
    };

    /**
     * @param {Object} list
     */
    this.parseConstraints = function (list) {
        var constraints = [];
        for (var name in list) {
            var className = name.replace(/\\/g, '');
            if (undefined !== window[className]) {
                var i = list[name].length;
                while (i--) {
                    var constraint = new window[className]();
                    for (var param in list[name][i]) {
                        constraint[param] = list[name][i][param];
                    }
                    constraint.uniqueId = this.constraintsCounter;
                    this.constraintsCounter++;
                    if (typeof constraint.onCreate === 'function') {
                        constraint.onCreate();
                    }
                    constraints.push(constraint);
                }
            }
        }

        return constraints;
    };

    /**
     * @param list
     * @returns {Array}
     */
    this.parseTransformers = function (list) {
        var transformers = [];
        var i = list.length;
        while (i--) {
            var className = String(list[i]['name']).replace(/\\/g, '');
            if (undefined !== window[className]) {
                var transformer = new window[className]();
                for (var propName in list[i]) {
                    transformer[propName] = list[i][propName];
                }
                if (undefined !== transformer.transformers) {
                    transformer.transformers = this.parseTransformers(transformer.transformers);
                }
                transformers.push(transformer);
            }
        }

        return transformers;
    };

    /**
     * @param {String} id
     * @param {FpJsFormElement} element
     */
    this.getParentElementById = function (id, element) {
        if (id == element.id) {
            return element;
        } else if (element.parent) {
            return this.getParentElementById(id, element.parent);
        } else {
            return null;
        }
    };

    /**
     * @param {FpJsFormElement} element
     */
    this.attachElement = function (element) {
        if (!element.domNode) {
            return;
        }

        if (undefined !== element.domNode.jsFormValidator) {
            for (var key in element.domNode.jsFormValidator) {
                element[key] = element.domNode.jsFormValidator[key];
            }
        }

        element.domNode.jsFormValidator = element;
    };

    /**
     * @param {FpJsFormElement} element
     * @param {HTMLFormElement} form
     */
    this.attachDefaultEvent = function (element, form) {
        form.addEventListener('submit', function (event) {
            FpJsFormValidator.customize(form, 'submitForm', event);
        });
    };

    /**
     * @param {Object} model
     *
     * @return {HTMLElement|null}
     */
    this.findDomElement = function (model) {
        var domElement = document.getElementById(model.id);
        if (!domElement) {
            var list = document.getElementsByName(model.name);
            if (list.length) {
                domElement = list[0];
            }
        }

        return domElement;
    };

    /**
     * @param {FpJsFormElement} element
     *
     * @return {HTMLFormElement|null}
     */
    this.findFormElement = function (element) {
        var form = null;
        if (element.domNode && 'form' == element.domNode.tagName.toLowerCase()) {
            form = element.domNode;
        } else {
            var realChild = this.findRealChildElement(element);
            if (realChild) {
                form = this.findParentForm(realChild);
            }
        }

        return form;
    };

    /**
     * @param {FpJsFormElement} element
     *
     * @return {HTMLElement|null}
     */
    this.findRealChildElement = function (element) {
        var child = element.domNode;
        if (!child) {
            for (var childName in element.children) {
                child = element.children[childName].domNode;
                if (child) {
                    break;
                }
            }
        }

        return child;
    };

    /**
     * @param {HTMLElement|Node} child
     *
     * @return {HTMLElement|null}
     */
    this.findParentForm = function (child) {
        if (child.tagName && 'form' == child.tagName.toLowerCase()) {
            return child;
        } else if (child.parentNode) {
            return this.findParentForm(child.parentNode);
        } else {
            return null;
        }
    };

    /**
     * @param {HTMLElement} htmlElement
     * @returns {Node}
     */
    this.getDefaultErrorContainerNode = function (htmlElement) {
        var ul = htmlElement.previousSibling;
        if (!ul || ul.className !== this.errorClass) {
            return null;
        } else {
            return ul;
        }
    };

    /**
     * Get related element to show error list
     * @param {FpJsFormElement} element
     */
    this.getErrorPathElement = function (element) {
        if (!element.bubbling) {
            return element;
        } else {
            return this.getRootElement(element);
        }
    };

    /**
     * Find recursively for the root (from) element
     * @param {FpJsFormElement} element
     */
    this.getRootElement = function (element) {
        if (element.parent) {
            return this.getRootElement(element.parent);
        } else {
            return element;
        }
    };

    /**
     * Applies customizing for the specified elements
     *
     * @param items
     * @param method
     * @returns {*}
     */
    this.customize = function (items, method) {
        if (!Array.isArray(items)) {
            items = [items];
        }

        if (!method) {
            return this.customizeMethods.get.apply(items, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object') {
            return this.customizeMethods.init.apply(items, Array.prototype.slice.call(arguments, 1));
        } else if (this.customizeMethods[method]) {
            return this.customizeMethods[method].apply(items, Array.prototype.slice.call(arguments, 2));
        } else {
            $.error('Method ' + method + ' does not exist');
            return this;
        }
    };

    /**
     * Loop an array of elements
     *
     * @param list
     * @param callback
     * @param skipEmpty
     */
    this.each = function (list, callback, skipEmpty) {
        skipEmpty = (undefined == skipEmpty) ? true : skipEmpty;
        var len = list.length;
        while (len--) {
            if (skipEmpty && (!list[len] || !list[len].jsFormValidator)) {
                continue;
            }
            callback(list[len]);
        }
    };

    /**
     * Looks for the callback in a specified element by string or array
     *
     * @param {FpJsFormElement} element
     * @param {Array|String} data
     * @returns {Function|null}
     */
    this.getRealCallback = function (element, data) {
        var className = null;
        var methodName = null;
        if (typeof data == "string") {
            methodName = data;
        } else if (Array.isArray(data)) {
            if (1 == data.length) {
                methodName = data[0];
            } else {
                className = data[0];
                methodName = data[1];
            }
        }

        var callback = null;

        if (!element.callbacks[className] && typeof element.callbacks[methodName] == "function") {
            callback = element.callbacks[methodName];
        } else if (element.callbacks[className] && typeof element.callbacks[className][methodName] == "function") {
            callback = element.callbacks[className][methodName];
        } else if (typeof element.callbacks[methodName] == "function") {
            callback = element.callbacks[methodName];
        }

        return callback;
    };

    /**
     * Returns an object with all the element's and children's errors
     *
     * @param {FpJsFormElement} element
     * @param {Object} container
     *
     * @returns {Object}
     */
    this.getAllErrors = function (element, container) {
        if (container == null || typeof container !== 'object') {
            container = {};
        }

        var hasErrors = false;
        for (var sourceId in element.errors) {
            if (element.errors[sourceId].length) {
                hasErrors = true;
                break;
            }
        }

        if (hasErrors) {
            container[element.id] = element.errors;
        }

        for (var childName in element.children) {
            container = this.getAllErrors(element.children[childName], container);
        }

        return container;
    };

    /**
     * Replace patterns with real values for the specified prototype
     *
     * @param {Object} prototype
     * @param {String} name
     * @param {String} id
     */
    this.preparePrototype = function (prototype, name, id) {
        prototype.name = prototype.name.replace(/__name__/g, name);
        prototype.id = prototype.id.replace(/__name__/g, id);

        if (typeof prototype.children == 'object') {
            for (var childName in prototype.children) {
                prototype[childName] = this.preparePrototype(prototype.children[childName], name, id);
            }
        }

        return prototype;
    };

    /**
     * Clone object recursively
     *
     * @param {{}} object
     * @returns {{}}
     */
    this.cloneObject = function (object) {
        var clone = {};
        for (var i in object) {
            if (typeof object[i] == 'object' && !(object[i] instanceof Array)) {
                clone[i] = this.cloneObject(object[i]);
            } else {
                clone[i] = object[i];
            }
        }

        return clone;
    };

    /**
     * Check if a mixed value is emty
     *
     * @param value
     *
     * @returns boolean
     */
    this.isValueEmty = function (value) {
        return [undefined, null, false].indexOf(value) >= 0 || 0 === this.getValueLength(value);
    };

    /**
     * Check if a value is array
     *
     * @param value
     *
     * @returns boolean
     */
    this.isValueArray = function (value) {
        return value instanceof Array;
    };

    /**
     * Check if a value is object
     *
     * @param value
     *
     * @returns boolean
     */
    this.isValueObject = function (value) {
        return typeof value == 'object' && null !== value;
    };

    /**
     * Returns length of a mixed value
     *
     * @param value
     *
     * @returns int|null
     */
    this.getValueLength = function (value) {
        var length = null;
        if (typeof value == 'number' || typeof value == 'string' || this.isValueArray(value)) {
            length = value.length;
        } else if (this.isValueObject(value)) {
            var count = 0;
            for (var propName in value) {
                if (value.hasOwnProperty(propName)) {
                    count++;
                }
            }
            length = count;
        }

        return length;
    };
}();
