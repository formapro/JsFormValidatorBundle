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
        var len = ul.childNodes.length;
        while (len--) {
            if (type == ul.childNodes[len].className) {
                ul.removeChild(ul.childNodes[len]);
            }
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
            ul = document.createElement('ul');
            ul.className = FpJsFormValidator.errorClass;
            htmlElement.parentNode.insertBefore(ul, htmlElement);
        }

        return ul;
    };
}();