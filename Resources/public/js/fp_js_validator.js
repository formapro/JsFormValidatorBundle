function FpJsFormElement() {
    this.id = '';
    this.name = '';
    this.type = '';
    this.invalidMessage = '';
    this.cascade = true;
    this.transformers = [];
    this.data = {};
    this.children = {};
    this.parent = null;
    this.domNode = null;

    this.callbacks = {};
    this.errors = [];

    this.groups = function () {
        return ['Default'];
    };

    this.validate = function () {
        var self = this;
        self.errors = [];
        FpJsFormValidator.validateElement(self);
        var type = FpJsFormValidator.errorClass + '-' + this.name + '-main';

        if (FpJsFormValidator.ajax.hasRequest(self)) {
            FpJsFormValidator.ajax.addCallback(self, function () {
                self.showErrors.apply(self.domNode, [self.errors, type]);
                self.postValidate.apply(self.domNode, [self.errors, type]);
            });
        } else {
            self.showErrors.apply(self.domNode, [self.errors, type]);
            self.postValidate.apply(self.domNode, [self.errors, type]);
        }

        return self.errors.length == 0;
    };

    this.validateRecursively = function () {
        this.validate();
        for (var childName in this.children) {
            this.children[childName].validateRecursively();
        }
    };

    this.showErrors = function (errors, type) {
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
                if (type == ul.childNodes[len].className) {
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
            li.className = type;
            li.innerHTML = errors[i];
            ul.appendChild(li);
        }
    };

    this.postValidate = function (errors, type) {
    };
}

function AjaxRequest() {
    this.queue = {};

    this.hasRequest = function (element) {
        return this.queue[element.id] && this.queue[element.id]['count'] > 0;
    };

    this.addCallback = function (element, callback) {
        if (!this.queue[element.id]) {
            this.queue[element.id] = {
                'count': 0,
                'callbacks': []
            };
        }
        this.queue[element.id]['callbacks'].push(callback);
    };

    this.sendRequest = function (path, data, callback, owner) {
        var self = this;
        var request = this.createRequest();

        try {
            request.open("POST", path, true);
            request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            request.onreadystatechange = function () {
                if (4 == request.readyState && 200 == request.status) {
                    callback(request.responseText, owner);
                    self.decreaseQueue(owner);
                }
            };

            request.send(this.serializeData(data, null));
            self.increaseQueue(owner);
        } catch (e) {
            console.log(e.message);
        }
    };

    this.increaseQueue = function (owner) {
        if (!this.queue[owner.id]) {
            this.queue[owner.id] = {
                'count': 0,
                'callbacks': []
            };
        }
        this.queue[owner.id].count++;
    };

    this.decreaseQueue = function (owner) {
        if (undefined != this.queue[owner.id]) {
            this.queue[owner.id].count--;
            if (0 == this.queue[owner.id].count) {
                for (var i in this.queue[owner.id].callbacks) {
                    this.queue[owner.id].callbacks[i](owner);
                }
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

var FpJsFormValidator = new function () {
    this.forms = {};
    this.errorClass = 'form-errors';
    this.config = {};
    this.ajax = new AjaxRequest();

    //noinspection JSUnusedGlobalSymbols
    this.addModel = function (model) {
        var self = this;
        if (!model) return;
        this.onDocumentReady(function () {
            self.forms[model.id] = self.initModel(model);
        });
    };

    this.onDocumentReady = function (callback) {
        var addListener = document.addEventListener || document.attachEvent;
        var removeListener = document.removeEventListener || document.detachEvent;
        var eventName = document.addEventListener ? "DOMContentLoaded" : "onreadystatechange";

        addListener.call(document, eventName, function () {
            removeListener(eventName, arguments.callee, false);
            callback();
        }, false)
    };

    /**
     * @param {Object} model
     */
    this.initModel = function (model) {
        var element = this.createElement(model);
        var form = this.findFormElement(element);
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
        for (var key in model) {
            if ('children' == key) {
                for (var childName in model.children) {
                    element.children[childName] = this.createElement(model.children[childName]);
                    element.children[childName].parent = element;
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

        element.domNode = this.findDomElement(model);
        this.attachElement(element);

        return element;
    };

    /**
     * @param {FpJsFormElement} element
     */
    this.validateElement = function (element) {
        var errors = [];
        var value  = this.getElementValue(element);
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

        element.errors = errors;
    };

    this.checkParentCascadeOption = function(element) {
        var result = true;
        if (element.parent && !element.parent.cascade) {
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
            if (this.checkValidationGroups(groups, constraints[i].groups)) {
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
    this.checkValidationGroups = function (needle, haystack) {
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
     * @param {FpJsFormElement} element
     */
    this.getElementValue = function (element) {
        var i = element.transformers.length;
        var value = this.getInputValue(element);

        if (i && undefined === value) {
            value = this.getMappedValue(element);
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
        if ('checkbox' == element.type || 'radio' == element.type) {
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
    this.parseTransformers = function(list) {
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
        var self = this;
        form.addEventListener('submit', function (event) {
            if (!element.validate()) {
                event.preventDefault();
            }
            if (self.ajax.hasRequest(element)) {
                event.preventDefault();
                self.ajax.addCallback(element, function (owner) {
                    if (!owner.errors.length) {
                        form.submit();
                    }
                })
            }
        });

        for (var childName in element.children) {
            this.attachDefaultEvent(element.children[childName], form);
        }
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
        if ('form' == element.domNode.tagName.toLowerCase()) {
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
        if ('form' == child.tagName.toLowerCase()) {
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
}();
//noinspection JSUnusedGlobalSymbols,JSUnusedGlobalSymbols
/**
 * Checks if value is blank
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsBlank() {
    this.message = '';

    this.validate = function (value) {
        var errors = [];
        if ([undefined, null, false, '', [], {}].indexOf(value) === -1) {
            errors.push(this.message.replace('{{ value }}', String(value)));
        }

        return errors;
    }
}


//noinspection JSUnusedGlobalSymbols
function SymfonyComponentValidatorConstraintsCallback () {
    this.callback = null;
    this.methods  = [];

    /**
     * @param {*} value
     * @param {FpJsFormElement} element
     */
    this.validate = function(value, element) {
        if (!this.callback) {
            this.callback = [];
        }
        if (typeof this.callback == "string") {
            this.callback = [this.callback];
        }
        if (!this.methods) {
            this.methods = [this.callback];
        }

        for (var pairId in this.methods) {
            var className  = (1 == this.methods[pairId].length)
                ? null
                : this.methods[pairId][0];

            var methodName = (1 == this.methods[pairId].length)
                ? this.methods[pairId][0]
                : this.methods[pairId][1];

            var method = function(){};

            if (!element.callbacks[className] && typeof element.callbacks[methodName] == "function") {
                method = element.callbacks[methodName];
            } else if (element.callbacks[className] && typeof element.callbacks[className][methodName] == "function") {
                method = element.callbacks[className][methodName];
            } else if (typeof element.callbacks[methodName] == "function") {
                method = element.callbacks[methodName];
            }
        }

        method.apply(element.domNode);

        return [];
    }
}
//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is in array of choices
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsChoice() {
    this.choices = [];
    this.callback = null;
    this.max = null;
    this.min = null;
    this.message = '';
    this.maxMessage = '';
    this.minMessage = '';
    this.multiple = false;
    this.multipleMessage = '';
    this.strict = false;

    this.validate = function (value, element) {
        var errors = [];
        value = this.getValue(value);
        if (null === value) {
            return errors;
        }

        var invalidList = this.getInvalidChoices(value, this.getChoicesList(element));
        var invalidCnt = invalidList.length;

        if (this.multiple) {
            if (invalidCnt) {
                while (invalidCnt--) {
                    errors.push(this.multipleMessage.replace('{{ value }}', String(invalidList[invalidCnt])));
                }
            }
            if (!isNaN(this.min) && value.length < this.min) {
                errors.push(
                    this.minMessage
                        .replace('{{ value }}', String(value))
                        .replace('{{ limit }}', this.min)
                );
            }
            if (!isNaN(this.max) && value.length > this.max) {
                errors.push(
                    this.maxMessage
                        .replace('{{ value }}', String(value))
                        .replace('{{ limit }}', this.max)
                );
            }
        } else if (invalidCnt) {
            while (invalidCnt--) {
                errors.push(this.message.replace('{{ value }}', String(invalidList[invalidCnt])));
            }
        }

        return errors;
    };

    this.onCreate = function () {
        this.min = parseInt(this.min);
        this.max = parseInt(this.max);
    };

    this.getValue = function (value) {
        if (-1 !== [undefined, null, ''].indexOf(value)) {
            return null;
        } else if (!(value instanceof Array)) {
            return [value];
        } else {
            return value;
        }
    };

    /**
     * @param {FpJsFormElement} element
     * @return {Array}
     */
    this.getChoicesList = function (element) {
        var choices = null;
        if (this.callback) {
            if (typeof element.callbacks[this.callback] == "function") {
                choices = element.callbacks[this.callback].apply(element);
            } else {
                throw new Error('Can not find a "' + this.callback + '" callback for the element id="' + element.id + '" to get a choices list.');
            }
        }

        if (null == choices) {
            choices = (null == this.choices) ? [] : this.choices;
        }

        return choices;
    };

    this.getInvalidChoices = function (value, validChoices) {
        // Compare arrays by value
        var callbackFilter = function (n) {
            return validChoices.indexOf(n) == -1
        };
        // More precise comparison by type
        if (this.strict) {
            callbackFilter = function (n) {
                var result = false;
                for (var i in validChoices) {
                    if (n !== validChoices[i]) {
                        result = true;
                    }
                }
                return result;
            };
        }

        return value.filter(callbackFilter);
    }
}

//noinspection JSUnusedGlobalSymbols
/**
 * Checks count of an array or object
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsCount() {
    this.maxMessage = '';
    this.minMessage = '';
    this.exactMessage = '';
    this.max = null;
    this.min = null;

    this.validate = function (value) {
        var errors = [];

        var count = null;
        if (value instanceof Array) {
            count = value.length;
        } else if (typeof value == 'object') {
            count = 0;
            for (var propName in value) {
                if (value.hasOwnProperty(propName)) {
                    count++;
                }
            }
        }

        if (null !== count) {
            if (this.max === this.min && count !== this.min) {
                errors.push(
                    this.exactMessage
                        .replace('{{ value }}', String(value))
                        .replace('{{ limit }}', this.min)
                );
                return errors;
            }
            if (!isNaN(this.max) && count > this.max) {
                errors.push(
                    this.maxMessage
                        .replace('{{ value }}', String(value))
                        .replace('{{ limit }}', this.max)
                );
            }
            if (!isNaN(this.min) && count < this.min) {
                errors.push(
                    this.minMessage
                        .replace('{{ value }}', String(value))
                        .replace('{{ limit }}', this.min)
                );
            }
        }

        return errors;
    };

    this.onCreate = function () {
        this.min = parseInt(this.min);
        this.max = parseInt(this.max);
    }
}

//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is a date
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsDate() {
    this.message = '';

    this.validate = function (value) {
        var errors = [];
        if (value) {
            var regexp = /^(\d{4})-(\d{2})-(\d{2})$/;
            if (!regexp.test(value)) {
                errors.push(this.message.replace('{{ value }}', String(value)));
            }
        }

        return errors;
    }
}

//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is a datetime string
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsDateTime() {
    this.message = '';

    this.validate = function (value) {
        var errors = [];
        if (value) {
            var regexp = /^(\d{4})-(\d{2})-(\d{2}) (0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/;
            if (!regexp.test(value)) {
                errors.push(this.message.replace('{{ value }}', String(value)));
            }
        }

        return errors;
    }
}
//noinspection JSUnusedGlobalSymbols
/**
 * Checks if values is like an email address
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsEmail() {
    this.message = '';

    this.validate = function (value) {
        var errors = [];
        var regexp = /^[-a-z0-9~!$%^&*_=+}{'?]+(\.[-a-z0-9~!$%^&*_=+}{'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i;
        if (String(value).length > 0 && !regexp.test(value)) {
            errors.push(this.message.replace('{{ value }}', String(value)));
        }

        return errors;
    }
}

//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is equal to the predefined value
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsEqualTo() {
    this.message = '';
    this.value = null;

    this.validate = function (value) {
        if ('' === value) {
            return [];
        }

        var errors = [];
        if (this.value != value) {
            errors.push(
                this.message
                    .replace('{{ value }}', String(this.value))
                    .replace('{{ compared_value }}', String(this.value))
                    .replace('{{ compared_value_type }}', String(this.value))
            );
        }

        return errors;
    }
}

//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is (bool) false
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsFalse() {
    this.message = '';

    this.validate = function (value) {
        if ('' === value) {
            return [];
        }

        var errors = [];
        if (false !== value) {
            errors.push(this.message.replace('{{ value }}', value));
        }

        return errors;
    }
}

//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is greater than the predefined value
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsGreaterThan() {
    this.message = '';
    this.value = null;

    this.validate = function (value) {
        if ('' === value) {
            return [];
        }

        if (value > this.value) {
            return [];
        } else {
            return [
                this.message
                    .replace('{{ value }}', String(this.value))
                    .replace('{{ compared_value }}', String(this.value))
            ];
        }
    }
}

//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is greater than or equal to the predefined value
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsGreaterThanOrEqual() {
    this.message = '';
    this.value = null;

    this.validate = function (value) {
        if ('' === value) {
            return [];
        }

        if (value >= this.value) {
            return [];
        } else {
            return [
                this.message
                    .replace('{{ value }}', String(this.value))
                    .replace('{{ compared_value }}', String(this.value))
            ];
        }
    }
}

//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is identical to the predefined value
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsIdenticalTo() {
    this.message = '';
    this.value = null;

    this.validate = function (value) {
        if ('' === value) {
            return [];
        }

        var errors = [];
        if (this.value !== value) {
            errors.push(
                this.message
                    .replace('{{ value }}', String(value))
                    .replace('{{ compared_value }}', String(this.value))
                    .replace('{{ compared_value_type }}', String(this.value))
            );
        }

        return errors;
    }
}

//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is like an IP address
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsIp() {
    this.message = '';

    this.validate = function (value) {
        var errors = [];
        var regexp = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
        if (String(value).length > 0 && !regexp.test(value)) {
            errors.push(this.message.replace('{{ value }}', String(value)));
        }

        return errors;
    }
}

//noinspection JSUnusedGlobalSymbols
/**
 * Checks minimum and maximum length
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsLength() {
    this.maxMessage = '';
    this.minMessage = '';
    this.exactMessage = '';
    this.max = null;
    this.min = null;

    this.validate = function (value) {
        var errors = [];
        if (typeof value == 'number' || typeof value == 'string' || value instanceof Array) {
            var length = value.length;
            if (length) {
                if (this.max === this.min && length !== this.min) {
                    errors.push(
                        this.exactMessage
                            .replace('{{ value }}', String(value))
                            .replace('{{ limit }}', this.min)
                    );

                    return errors;
                }
                if (!isNaN(this.max) && length > this.max) {
                    errors.push(
                        this.maxMessage
                            .replace('{{ value }}', String(value))
                            .replace('{{ limit }}', this.max)
                    );
                }
                if (!isNaN(this.min) && length < this.min) {
                    errors.push(
                        this.minMessage
                            .replace('{{ value }}', String(value))
                            .replace('{{ limit }}', this.min)
                    );
                }
            }
        }

        return errors;
    };

    this.onCreate = function () {
        this.min = parseInt(this.min);
        this.max = parseInt(this.max);
    }
}

//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is less than the predefined value
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsLessThan() {
    this.message = '';
    this.value = null;

    this.validate = function (value) {
        if ('' === value) {
            return [];
        }

        if (value < this.value) {
            return [];
        } else {
            return [
                this.message
                    .replace('{{ value }}', String(this.value))
                    .replace('{{ compared_value }}', String(this.value))
            ];
        }
    }
}

//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is less than or equal to the predefined value
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsLessThanOrEqual() {
    this.message = '';
    this.value = null;

    this.validate = function (value) {
        if ('' === value) {
            return [];
        }

        if (value <= this.value) {
            return [];
        } else {
            return [
                this.message
                    .replace('{{ value }}', String(this.value))
                    .replace('{{ compared_value }}', String(this.value))
            ];
        }
    }
}

//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is not blank
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsNotBlank() {
    this.message = '';

    this.validate = function (value) {
        var errors = [];
        if ([undefined, null, false, '', [], {}].indexOf(value) >= 0) {
            errors.push(this.message.replace('{{ value }}', String(value)));
        }

        return errors;
    }
}

//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is not equal to the predefined value
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsNotEqualTo() {
    this.message = '';
    this.value = null;

    this.validate = function (value) {
        var errors = [];
        if (this.value == value) {
            errors.push(
                this.message
                    .replace('{{ value }}', String(this.value))
                    .replace('{{ compared_value }}', String(this.value))
                    .replace('{{ compared_value_type }}', String(this.value))
            );
        }

        return errors;
    }
}

//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is not identical to the predefined value
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsNotIdenticalTo() {
    this.message = '';
    this.value = null;

    this.validate = function (value) {
        var errors = [];
        if (this.value === value) {
            errors.push(
                this.message
                    .replace('{{ value }}', String(value))
                    .replace('{{ compared_value }}', String(this.value))
                    .replace('{{ compared_value_type }}', String(this.value))
            );
        }

        return errors;
    }
}

//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is not null
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsNotNull() {
    this.message = '';

    this.validate = function (value) {
        var errors = [];
        if (null === value) {
            errors.push(this.message.replace('{{ value }}', String(value)));
        }

        return errors;
    }
}

//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is null
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsNull() {
    this.message = '';

    this.validate = function(value) {
        var errors = [];
        if (null !== value) {
            errors.push(this.message.replace('{{ value }}', String(value)));
        }

        return errors;
    }
}

//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is a number and is between min and max values
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsRange() {
    this.maxMessage = '';
    this.minMessage = '';
    this.invalidMessage = '';
    this.max = null;
    this.min = null;

    this.validate = function (value) {
        if ('' === value) {
            return [];
        }

        var errors = [];
        if (isNaN(value)) {
            errors.push(
                this.invalidMessage
                    .replace('{{ value }}', String(value))
            );
        }
        if (!isNaN(this.max) && value > this.max) {
            errors.push(
                this.maxMessage
                    .replace('{{ value }}', String(value))
                    .replace('{{ limit }}', this.max)
            );
        }
        if (!isNaN(this.min) && value < this.min) {
            errors.push(
                this.minMessage
                    .replace('{{ value }}', String(value))
                    .replace('{{ limit }}', this.min)
            );
        }

        return errors;
    };

    this.onCreate = function () {
        this.min = parseInt(this.min);
        this.max = parseInt(this.max);
    }
}

//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value matches to the predefined regexp
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsRegex() {
    this.message = '';
    this.pattern = '';
    this.match = true;

    this.validate = function(value) {
        var errors = [];
        if (String(value).length > 0 && !this.pattern.test(value)) {
            errors.push(this.message.replace('{{ value }}', String(value)));
        }

        return errors;
    };

    this.onCreate = function() {
        var flags = this.pattern.match(/\/(\w*)$/);
        this.pattern = new RegExp(this.pattern.trim().replace(/(^\/)|(\/\w*$)/g, ''), flags[1]);
    }
}

//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is a time string
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsTime() {
    this.message = '';

    this.validate = function (value) {
        var errors = [];
        var regexp = /^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/;
        if (String(value).length > 0 && !regexp.test(value)) {
            errors.push(this.message.replace('{{ value }}', String(value)));
        }

        return errors;
    }
}

//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is (bool) true
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsTrue() {
    this.message = '';

    this.validate = function(value) {
        if ('' === value) {
            return [];
        }

        var errors = [];
        if (true !== value) {
            errors.push(this.message.replace('{{ value }}', value));
        }

        return errors;
    }
}


//noinspection JSUnusedGlobalSymbols
/**
 * Checks the value type
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsType() {
    this.message = '';
    this.type = '';

    this.validate = function(value) {
        if ('' === value) {
            return [];
        }

        var errors = [];
        var isValid = false;

        switch (this.type) {
            case 'array':
                isValid = (value instanceof Array);
                break;

            case 'bool':
            case 'boolean':
                isValid = (typeof value === 'boolean');
                break;

            case 'callable':
                isValid = (typeof value === 'function');
                break;

            case 'float':
            case 'double':
            case 'real':
                isValid = typeof value === 'number' && value % 1 != 0;
                break;

            case 'int':
            case 'integer':
            case 'long':
                isValid = (value === parseInt(value));
                break;

            case 'null':
                isValid = (null === value);
                break;

            case 'numeric':
                isValid = !isNaN(value);
                break;

            case 'object':
                isValid = (null !== value) && (typeof value === 'object');
                break;

            case 'scalar':
                isValid = (/boolean|number|string/).test(typeof value);
                value = 'Array';
                break;

            case '':
            case 'string':
                isValid = (typeof value === 'string');
                break;

            // It doesn't have an implementation in javascript
            case 'resource':
                isValid = true;
                break;

            default:
                throw 'The wrong "'+this.type+'" type was passed to the Type constraint';
        }

        if (!isValid) {
            errors.push(
                this.message
                    .replace('{{ value }}', value)
                    .replace('{{ type }}', this.type)
            );
        }

        return errors;
    }
}

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
        var errorPath = this.getErrorPathElement(element);

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
                    errorPath.errors.push(self.message);
                }
            },
            errorPath
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
     * @return {FpJsFormElement}
     */
    this.getErrorPathElement = function(element) {
        var errorPath = this.fields[0];
        if (this.errorPath) {
            errorPath = this.errorPath;
        }

        return element.children[errorPath];
    }
}

//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is like an URL address
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsUrl() {
    this.message = '';

    this.validate = function(value, element) {
        var errors = [];
        var regexp = /(ftp|https?):\/\/(www\.)?[\w\-\.@:%_\+~#=]+\.\w{2,3}(\/\w+)*(\?.*)?/;
        if (String(value).length > 0 && !regexp.test(value)) {
            element.domNode.value = 'http://' + value;
            errors.push(this.message.replace('{{ value }}', String('http://' + value)));
        }

        return errors;
    }
}

//noinspection JSUnusedGlobalSymbols
/**
 * Created by ymaltsev on 11/28/13.
 */
function SymfonyComponentFormExtensionCoreDataTransformerArrayToPartsTransformer() {
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
//noinspection JSUnusedGlobalSymbols
/**
 * Created by ymaltsev on 11/28/13.
 */
function SymfonyComponentFormExtensionCoreDataTransformerBooleanToStringTransformer() {
    this.trueValue = null;

    this.reverseTransform = function(value) {
        if (typeof value === 'boolean') {
            return value;
        } else if (value === this.trueValue) {
            return true;
        } else if (!value) {
            return false;
        } else {
            throw new Error('Wrong type of value');
        }
    }
}
//noinspection JSUnusedGlobalSymbols
/**
 * Created by ymaltsev on 11/29/13.
 */
function SymfonyComponentFormExtensionCoreDataTransformerChoiceToBooleanArrayTransformer() {
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
//noinspection JSUnusedGlobalSymbols
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
//noinspection JSUnusedGlobalSymbols
/**
 * Created by ymaltsev on 11/29/13.
 */
function SymfonyComponentFormExtensionCoreDataTransformerChoicesToBooleanArrayTransformer() {
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
//noinspection JSUnusedGlobalSymbols
/**
 * Created by ymaltsev on 11/28/13.
 */
function SymfonyComponentFormExtensionCoreDataTransformerChoicesToValuesTransformer() {
    this.choiceList = {};

    // This transformer just returns values as is, because we actually receive choices (not values) from input fields
    this.reverseTransform = function(value) {
        return value;
    }
}
//noinspection JSUnusedGlobalSymbols
/**
 * Created by ymaltsev on 11/22/13.
 */
function SymfonyComponentFormExtensionCoreDataTransformerDataTransformerChain(transformers) {
    this.transformers = transformers;

    this.reverseTransform = function(value, element) {
        var len = this.transformers.length;
        for (var i = 0; i < len; i++) {
            value = this.transformers[i].reverseTransform(value, element);
        }

        return value;
    }
}
//noinspection JSUnusedGlobalSymbols
/**
 * Created by ymaltsev on 11/28/13.
 */
function SymfonyComponentFormExtensionCoreDataTransformerDateTimeToArrayTransformer() {
    this.dateFormat = '{0}-{1}-{2}';
    this.timeFormat = '{0}:{1}:{2}';

    this.reverseTransform = function(value) {
        var result = [];

        if (value['year'] || value['month'] || value['day']) {
            result.push(this.formatDate(this.dateFormat, [
                value['year']  ? value['year']  : '1970',
                value['month'] ? this.twoDigits(value['month']) : '01',
                value['day']   ? this.twoDigits(value['day']) : '01'
            ]));
        }
        if (value['hour'] || value['minute'] || value['second']) {
            result.push(this.formatDate(this.timeFormat, [
                value['hour']   ? this.twoDigits(value['hour'])   : '00',
                value['minute'] ? this.twoDigits(value['minute']) : '00',
                value['second'] ? this.twoDigits(value['second']) : '00'
            ]));
        }

        return result.join(' ');
    };

    this.twoDigits = function(value) {
        return ('0' + value).slice(-2);
    };

    this.formatDate = function(format, date) {
        return format.replace(/{(\d+)}/g, function(match, number) {
            return typeof date[number] != 'undefined'
                ? date[number]
                : match
                ;
        });
    }
}
//noinspection JSUnusedGlobalSymbols
function SymfonyComponentFormExtensionCoreDataTransformerValueToDuplicatesTransformer() {
    this.keys = [];

    /**
     *
     * @param {{}} value
     * @param {FpJsFormElement} element
     */
    this.reverseTransform = function(value, element) {
        var initialValue = undefined;
        for (var key in value) {
            if (undefined === initialValue) {
                initialValue = value[key];
            }

            if (value[key] !== initialValue) {
                var child = element.children[this.keys[0]];
                child.showErrors.apply(child.domNode, [[element.invalidMessage], 'value-to-duplicates']);
                break;
            }
        }

        return initialValue;
    }
}
if(window.jQuery) {
    (function($) {
        var methods = {
            'init': function(options) {
                $(this).each(function(){
                    var item = this;

                    if (undefined == item.jsFormValidator) {
                        item.jsFormValidator = {};
                    }
                    for (var optName in options) {
                        switch (optName) {
                            case 'customEvents':
                                options[optName].apply(this);
                                break;
                            default:
                                item.jsFormValidator[optName] = options[optName];
                                break;
                        }
                    }
                });
            },

            'validate': function(opts) {
                var isValid = true;

                $(this).each(function(){
                    var item = this;
                    var method = (true === opts['recursive'])
                        ? 'validateRecursively'
                        : 'validate';

                    if (item.jsFormValidator[method]()) {
                        isValid = false;
                    }
                });

                return isValid;
            },

            'showErrors': function(opts) {
                $(this).each(function(){
                    var item = this;
                    item.jsFormValidator.showErrors.apply(item.jsFormValidator.domNode, [opts['errors'], opts['type']]);
                });
            },

            'get': function() {
                var elements = [];
                this.each(function(){
                    elements.push(this[0].jsFormValidator);
                });

                return elements;
            }
        };

        $.fn.jsFormValidator = function(method) {
            if (!method) {
                return methods.get.apply(this, arguments);
            } else if (typeof method === 'object') {
                return methods.init.apply(this, arguments);
            } else if (methods[method]) {
                return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
            } else {
                $.error('Method ' + method + ' does not exist');
                return this;
            }
        };
    })(jQuery);
}