function AjaxRequest() {
    this.queue = '';

    this.hasRequest = function(element) {
        return this.queue[element.id] && this.queue[element.id]['count'] > 0;
    };

    this.addCallback = function(element, callbalck) {
        if (this.queue[element.id]) {
            this.queue[element.id]['callback'] = callbalck;
        }
    };

    this.sendRequest = function (path, data, callback, owner) {
        console.log(path, data, callback, owner);
        var self = this;
        var request = this.createRequest();

        try {
            request.open("POST", path, true);
            request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            request.request.onreadystatechange = function () {
                if (4 == request.readyState && 200 == request.status) {
                    callback(request.responseText, owner);
                    self.decreaseQueue(owner);
                }
            };

            request.request.send(this.serializeData(data, null));
            self.increaseQueue(owner);
        } catch (e) {
        }
    };

    this.increaseQueue = function (owner) {
        if (undefined == this.queue[owner.id]) {
            this.queue[owner.id] = {
                'count': 0,
                'callback': function() {}
            };
        }
        this.queue[owner.id].count++;
    };

    this.decreaseQueue = function (owner) {
        if (undefined != this.queue[owner.id]) {
            this.queue[owner.id].count--;

            if (0 == this.queue[owner.id].count) {
                this.queue[owner.id].callback(owner);
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
     * @return {XMLHttpRequest|null}
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