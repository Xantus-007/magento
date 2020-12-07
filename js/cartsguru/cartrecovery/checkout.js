(function (document) {
    // Check if document already loaded otherwise set listener
    if (document.readyState && document.readyState === 'complete') {
        setupTracking();
    } else {
        document.addEventListener('DOMContentLoaded', setupTracking);
    }

    var fields = {
        firstname: 'billing:firstname',
        lastname: 'billing:lastname',
        telephone: 'billing:telephone',
        email: ['billing:email', 'billing[email]'],
        country: 'billing:country_id'
    };

    function setupTracking() {
        for (var item in fields) {
            if (fields.hasOwnProperty(item)) {
                var el, match;
                if (Array.isArray(fields[item])) {
                    for (var i = 0; i < fields[item].length; i++) {
                        el = document.getElementById(fields[item][i]);
                        if (el) {
                            break;
                        }
                    }
                    // Try to find by name
                    if (!el) {
                        for (var j = 0; j < fields[item].length; j++) {
                            match = document.getElementsByName(fields[item][j]);
                            if (match && match.length > 0) {
                                el = match[0];
                                break;
                            }
                        }
                    }
                } else {
                    el = document.getElementById(fields[item]);
                    if (!el) {
                        match = document.getElementsByName(fields[item]);
                        if (match && match.length > 0) {
                            el = match[0];
                        }
                    }
                }
                fields[item] = el;
            }
        }
        if (fields.email && fields.firstname) {
            for (item in fields) {
                if (fields.hasOwnProperty(item)) {
                    fields[item].addEventListener('blur', trackData);
                }
            }
        }
    }

    function collectData() {
        var data = [];
        for (var item in fields) {
            if (fields.hasOwnProperty(item)) {
                // Only if email is set
                if (item === 'email' && fields[item].value === '') {
                    return false;
                }
                if (item === 'country') {
                    data.push((encodeURIComponent(item) + "=" + encodeURIComponent(fields[item].options[fields[item].selectedIndex].value)));
                } else {
                    data.push((encodeURIComponent(item) + "=" + encodeURIComponent(fields[item].value)));
                }
            }
        }
        return data;
    }

    var timer;

    function trackData() {

        var data = collectData(),
            trackingURL = typeof cartsguru_tracking_url !== 'undefined' ? cartsguru_tracking_url : '/cartsguru/saveaccount';
        clearTimeout(timer);
        timer = setTimeout(sendData, 100, data, trackingURL);
    }

    function sendData(data, trackingURL) {
        if (data) {
            console.log(trackingURL);
            xhr = new XMLHttpRequest();
            xhr.open('POST', trackingURL, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send(data.join("&"));
        }
    }
})(document);
