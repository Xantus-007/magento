/*
 * storeLocator v1.4.9 - jQuery Google Maps Store Locator Plugin
 * (c) Copyright 2013, Bjorn Holine (http://www.bjornblog.com)
 * Released under the MIT license
 * Distance calculation function by Chris Pietschmann: http://pietschsoft.com/post/2008/02/01/Calculate-Distance-Between-Geocodes-in-C-and-JavaScript.aspx
 */

(function ($) {
    $.fn.storeLocator = function (options) {
        var url = jQuery("#url").val();
        var url1 = jQuery("#url1").val();
        var url2 = jQuery("#url2").val();
        var mappingLoading = false;
        var settings = $.extend({
            'mapDiv': 'map_storelocator',
            'listDiv': 'list_storelocator',
            'formContainerDiv': 'form-container',
            'formID': 'search-location',
            'inputID': 'address-search',
            'zoomLevel': 1, //12 = The zoom level of the Google Map. Set to 0 to have the map automatically center and zoom to show all display markers on the map.
            'pinColor': 'fe7569',
            'pinTextColor': '000000',
            'lengthUnit': 'km',
            'storeLimit': -1, //26 = The number of closest locations displayed at one time. Set to -1 for unlimited.
            'listLimit': 15,
            'distanceAlert': -1, //60 = Displays an alert if there are no locations with 60 miles of the user’s location. Set to -1 to disable.
            'dataType': 'xml',
            'dataLocation': url,
            'searchdataLocation': jQuery("#searchurl").val(),
            'listColor1': 'ffffff',
            'listColor2': 'eeeeee',
            'originMarker': false,
            'originpinColor': 'blue',
            'bounceMarker': true,
            'slideMap': true,
            'modalWindow': false,
            'overlayDiv': 'overlay',
            'modalWindowDiv': 'modal-window',
            'modalContentDiv': 'modal-content',
            'modalCloseIconDiv': 'close-icon',
            'defaultLoc': false,
            'defaultLat': '',
            'defaultLng': '',
            'autoGeocode': true,
            'maxDistance': false,
            'maxDistanceID': 'maxdistance',
            'fullMapStart': false,
            'noForm': false,
            'loading': false,
            'loadingDiv': 'loading-map',
            'featuredLocations': false,
            'infowindowTemplatePath': url1,
            'listTemplatePath': url2,
            //'KMLinfowindowTemplatePath': url+'app/design/frontend/default/ramesh/template/storelocator/templates/kml-infowindow-description.html',
            //'KMLlistTemplatePath': url+'app/design/frontend/default/ramesh/template/storelocator/templates/kml-location-list-description.html',
            'callbackBeforeSend': null,
            'callbackComplete': null,
            'callbackSuccess': null,
            'callbackModalOpen': null,
            'callbackModalClose': null,
            'jsonpCallback': null,
            //Language options
            'geocodeErrorAlert': 'Geocode was not successful for the following reason: ',
            'addressErrorAlert': 'Unable to find address',
            'autoGeocodeErrorAlert': 'Automatic location detection failed. Please fill in your address or zip code.',
            'distanceErrorAlert': '',
            'mileLang': 'mile',
            'milesLang': 'miles',
            'kilometerLang': 'kilometer',
            'kilometersLang': 'kilometers',
            'markerIcon': '',
            'clusterIcon': '',
            'autoComplete'             : true,
            'autoCompleteOptions'      : {},
            'isMobile': false,
        }, options);

        return this.each(function () {

            var $this = $(this);
            var listTemplate, infowindowTemplate;

            load_templates();

            //First load external templates and compile with Handlebars - make sure the templates are compiled before moving on
            function load_templates() {

                if (settings.dataType === 'kml') {
                    //KML infowindows
                    $.get(settings.KMLinfowindowTemplatePath, function (template) {
                        var source = template;
                        infowindowTemplate = Handlebars.compile(source);
                    });
                    //KML locations list
                    $.get(settings.KMLlistTemplatePath, function (template) {
                        var source = template;
                        listTemplate = Handlebars.compile(source);

                        //After loading move on to the main script
                        locator();
                    });
                } else {
                    //Infowindows
                    $.get(settings.infowindowTemplatePath, function (template) {
                        var source = template;
                        infowindowTemplate = Handlebars.compile(source);
                    });
                    //Locations list
                    $.get(settings.listTemplatePath, function (template) {
                        var source = template;
                        listTemplate = Handlebars.compile(source);

                        //After loading move on to the main script
                        locator();
                    });
                }
            }

            //The main script
            function locator() {

                var userinput, olat, olng, marker, letter, storenum;
                var locationset = [];
                var featuredset = [];
                var normalset = [];
                var markers = [];
                var prefix = 'storeLocator';

                //Resets for multiple re-submissions
                function reset() {
                    locationset = [];
                    featuredset = [];
                    normalset = [];
                    markers = [];
                    $(document).off('click.' + prefix, '#' + settings.listDiv + ' li');
                }

                //Add modal window divs if set
                if (settings.modalWindow === true) {
                    $this.wrap('<div id="' + settings.overlayDiv + '"><div id="' + settings.modalWindowDiv + '"><div id="' + settings.modalContentDiv + '">');
                    $('#' + settings.modalWindowDiv).prepend('<div id="' + settings.modalCloseIconDiv + '"><\/div>');
                    $('#' + settings.overlayDiv).hide();
                }

                if (settings.slideMap === true) {
                    //Let's hide the map container to begin
                    $this.hide();
                }

                //Calculate geocode distance functions - you could use Google's distance service instead
                var GeoCodeCalc = {};
                if (settings.lengthUnit === "km") {
                    //Kilometers
                    GeoCodeCalc.EarthRadius = 6367.0;
                } else {
                    //Default is miles
                    GeoCodeCalc.EarthRadius = 3956.0;
                }
                GeoCodeCalc.ToRadian = function (v) {
                    return v * (Math.PI / 180);
                };
                GeoCodeCalc.DiffRadian = function (v1, v2) {
                    return GeoCodeCalc.ToRadian(v2) - GeoCodeCalc.ToRadian(v1);
                };
                GeoCodeCalc.CalcDistance = function (lat1, lng1, lat2, lng2, radius) {
                    return radius * 2 * Math.asin(Math.min(1, Math.sqrt((Math.pow(Math.sin((GeoCodeCalc.DiffRadian(lat1, lat2)) / 2.0), 2.0) + Math.cos(GeoCodeCalc.ToRadian(lat1)) * Math.cos(GeoCodeCalc.ToRadian(lat2)) * Math.pow(Math.sin((GeoCodeCalc.DiffRadian(lng1, lng2)) / 2.0), 2.0)))));
                };

                start();

                function start() {
                    //If a default location is set
                    if (settings.defaultLoc === true) {
                        //The address needs to be determined for the directions link
                        var r = new ReverseGoogleGeocode();
                        var latlng = new google.maps.LatLng(settings.defaultLat, settings.defaultLng);
                        r.geocode(latlng, function (data) {
                            if (data !== null) {
                                var originAddress = data.address;
                                mapping(settings.defaultLat, settings.defaultLng, originAddress);
                            } else {
                                //Unable to geocode
                                console.log(settings.addressErrorAlert);
                            }
                        });
                    }

                    //If show full map option is true
                    if (settings.fullMapStart === true) {
                        //Just do the mapping without an origin
                        mapping();
                    }

                    //HTML5 geolocation API option
                    if (settings.autoGeocode === true) {
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(autoGeocode_query, autoGeocode_error);
                        }
                    }


                    // Set up Google Places autocomplete if it's set to true
                    if (settings.autoComplete === true) {
                        var searchInput = document.getElementById(settings.inputID);
                        var autoPlaces = new google.maps.places.Autocomplete(searchInput, settings.autoCompleteOptions);

                        // Add listener when autoComplete selection changes.
                        if (settings.autoComplete === true) {
                            autoPlaces.addListener('place_changed', function(e) {
                                $('#location-submit-from').click();
                            });
                        }
                    }

                }

                //Geocode function for the origin location
                function GoogleGeocode() {
                    geocoder = new google.maps.Geocoder();
                    this.geocode = function (address, callbackFunction) {
                        geocoder.geocode({'address': address}, function (results, status) {
                            if (status === google.maps.GeocoderStatus.OK) {
                                var result = {};
                                result.latitude = results[0].geometry.location.lat();
                                result.longitude = results[0].geometry.location.lng();
                                callbackFunction(result);
                            } else {

                                console.log(settings.geocodeErrorAlert + status);
                                callbackFunction(null);
                            }
                        });
                    };
                }

                //Reverse geocode to get address for automatic options needed for directions link
                function ReverseGoogleGeocode() {
                    geocoder = new google.maps.Geocoder();
                    this.geocode = function (latlng, callbackFunction) {
                        geocoder.geocode({'latLng': latlng}, function (results, status) {
                            if (status === google.maps.GeocoderStatus.OK) {
                                if (results[0]) {
                                    var result = {};
                                    result.address = results[0].formatted_address;
                                    callbackFunction(result);
                                }
                            } else {
                                console.log(settings.geocodeErrorAlert + status);
                                callbackFunction(null);
                            }
                        });
                    };
                }

                //Used to round miles to display
                function roundNumber(num, dec) {
                    return Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec);
                }

                //If location is detected automatically
                function autoGeocode_query(position) {
                    //The address needs to be determined for the directions link
                    var r = new ReverseGoogleGeocode();
                    var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    r.geocode(latlng, function (data) {
                        if (data !== null) {
                            var originAddress = data.address;
                            mapping(position.coords.latitude, position.coords.longitude, originAddress);
                        } else {
                            //Unable to geocode
                            console.log(settings.addressErrorAlert);
                        }
                    });
                }

                function autoGeocode_error(error) {
                    //If automatic detection doesn't work show an error
                    console.log(settings.autoGeocodeErrorAlert);
                }

                //Set up the normal mapping
                function begin_mapping(distance) {
                    var searchzipcode = 'a';
                    //Get the user input and use it
                    var userinput = $('#' + settings.inputID).val();

                    if (userinput === "") {
                        //start();
                    } else {
                        var g = new GoogleGeocode();
                        var address = userinput;
                        g.geocode(address, function (data) {
                            if (data !== null) {
                                olat = data.latitude;
                                olng = data.longitude;
                                mapping(olat, olng, userinput, distance, searchzipcode);
                            } else {
                                //Unable to geocode
                                console.log(settings.addressErrorAlert);
                            }
                        });
                    }
                }

                //Process form input
                $(function () {
                    //Handle form submission
                    function get_form_values(e) {
                        //Stop the form submission
                        e.preventDefault();

                        if (settings.maxDistance === true) {
                            var maxDistance = $('#' + settings.maxDistanceID).val();
                            //Start the mapping
                            begin_mapping(maxDistance);
                        } else {
                            //Start the mapping
                            begin_mapping();
                        }
                    }

                    //ASP.net or regular submission?
                    if (settings.noForm === true) {
                        $(document).on('click.' + prefix, '#' + settings.formContainerDiv + ' #submit', function (e) {
                            get_form_values(e);
                        });
                        $(document).on('keyup.' + prefix, function (e) {
                            if (e.keyCode === 13 && $('#' + settings.inputID).is(':focus')) {
                                get_form_values(e);
                            }
                        });
                    } else {
                        $(document).on('click.' + prefix, '.submit-search', function (e) {
                            get_form_values(e);
                            storeLocation._searchForm();
                        });
                    }
                });

                //Now all the mapping stuff
                function mapping(orig_lat, orig_lng, origin, maxDistance, s) {

                    if (mappingLoading)
                        return false;

                    mappingLoading = true;

                    $(function () {

                        // Enable the visual refresh https://developers.google.com/maps/documentation/javascript/basics#VisualRefresh
                        google.maps.visualRefresh = true;

                        var dataTypeRead;

                        //KML is read as XML
                        if (settings.dataType === 'kml') {
                            dataTypeRead = "xml";
                        } else {
                            dataTypeRead = settings.dataType;
                        }

                        var dataurl;
                        var zipcode = $('#' + settings.inputID).val();
                        dataurl = (s) ? settings.searchdataLocation + "?zipcode=" + zipcode : (settings.dataLocation + (settings.dataType === 'jsonp' ? (settings.dataLocation.match(/\?/) ? '&' : '?') + 'callback=?' : ''));

                        //Process the data
                        $.ajax({
                            type: "GET",
                            url: dataurl,
                            dataType: dataTypeRead,
                            jsonpCallback: (settings.dataType === 'jsonp' ? settings.jsonpCallback : null),
                            beforeSend: function () {
                                // Callback
                                if (settings.callbackBeforeSend) {
                                    settings.callbackBeforeSend.call(this);
                                }

                                //Loading
                                if (settings.loading === true) {
                                    $('#' + settings.formContainerDiv).append('<div id="' + settings.loadingDiv + '"><\/div>');
                                }

                            },
                            error: function(event) {
                            },
                            complete: function (event, request, options) {
                                // Callback
                                if (settings.callbackComplete) {
                                    settings.callbackComplete.call(this, event, request, options);
                                }

                                //Loading remove
                                if (settings.loading === true) {
                                    $('#' + settings.loadingDiv).remove();
                                }
                                mappingLoading = false;
                            },
                            success: function (data, xhr, options) {
                                // Callback
                                if (settings.callbackSuccess) {
                                    settings.callbackSuccess.call(this, data, xhr, options);
                                }

                                //After the store locations file has been read successfully
                                var i = 0;
                                var firstRun;

                                //Set a variable for fullMapStart so we can detect the first run
                                if (settings.fullMapStart === true && $('#' + settings.mapDiv).hasClass('mapOpen') === false) {
                                    firstRun = true;
                                } else {
                                    reset();
                                }

                                $('#' + settings.mapDiv).addClass('mapOpen');

                                //Depending on your data structure and what you want to include in the maps, you may need to change the following variables or comment them out
                                if (settings.dataType === 'json' || settings.dataType === 'jsonp') {
                                    //Process JSON
                                    $.each(data, function () {
                                        var key, value, locationData = {};

                                        // Parse each data variables
                                        for (key in this) {
                                            value = this[key];

                                            if (key === 'web') {
                                                if (value)
                                                    value = value.replace("http://", ""); // Remove scheme (todo: should NOT be done)
                                            }

                                            locationData[key] = value;
                                        }

                                        if (!locationData['distance']) {
                                            locationData['distance'] = GeoCodeCalc.CalcDistance(orig_lat, orig_lng, locationData['lat'], locationData['lng'], GeoCodeCalc.EarthRadius);
                                        }

                                        //Create the array
                                        if (settings.maxDistance === true && firstRun !== true && maxDistance) {
                                            if (locationData['distance'] < maxDistance) {
                                                locationset[i] = locationData;
                                            } else {
                                                return;
                                            }
                                        } else {
                                            locationset[i] = locationData;
                                        }

                                        i++;
                                    });
                                } else if (settings.dataType === 'kml') {
                                    //Process KML
                                    $(data).find('Placemark').each(function () {
                                        var locationData = {
                                            'name': $(this).find('name').text(),
                                            'lat': $(this).find('coordinates').text().split(",")[1],
                                            'lng': $(this).find('coordinates').text().split(",")[0],
                                            'description': $(this).find('description').text()
                                        };

                                        locationData['distance'] = GeoCodeCalc.CalcDistance(orig_lat, orig_lng, locationData['lat'], locationData['lng'], GeoCodeCalc.EarthRadius);

                                        //Create the array
                                        if (settings.maxDistance === true && firstRun !== true && maxDistance) {
                                            if (locationData['distance'] < maxDistance) {
                                                locationset[i] = locationData;
                                            } else {
                                                return;
                                            }
                                        } else {
                                            locationset[i] = locationData;
                                        }

                                        i++;
                                    });
                                } else {
                                    //Process XML
                                    $(data).find('marker').each(function () {
                                        var locationData = {
                                            'storeid': $(this).attr('storeid'),
                                            'name': $(this).attr('name'),
                                            'lat': $(this).attr('lat'),
                                            'lng': $(this).attr('lng'),
                                            'address_display': $(this).attr('address_display'),
                                            'phone': $(this).attr('phone'),
                                            'category': $(this).attr('category'),
                                            'web': $(this).attr('web')
                                        };
                                        if (locationData['web'])
                                            locationData['web'] = locationData['web'].replace("http://", ""); // Remove scheme (todo: should NOT be done)

                                        locationData['distance'] = GeoCodeCalc.CalcDistance(orig_lat, orig_lng, locationData['lat'], locationData['lng'], GeoCodeCalc.EarthRadius);

                                        //Create the array
                                        if (settings.maxDistance === true && firstRun !== true && maxDistance) {
                                            if (locationData['distance'] < maxDistance) {
                                                locationset[i] = locationData;
                                            } else {
                                                return;
                                            }
                                        } else {
                                            locationset[i] = locationData;
                                        }

                                        i++;
                                    });
                                }

                                if (locationset.length == 0){
                                    $('#list').empty().append('<div class="c-storeLocator__item">' + no_location_msg + '</div>');
                                    return false;
                                }

                                //Distance sorting function
                                function sort_numerically(locationsarray) {
                                    locationsarray.sort(function (a, b) {
                                        return ((a['distance'] < b['distance']) ? -1 : ((a['distance'] > b['distance']) ? 1 : 0));
                                    });
                                }

                                //Sort the multi-dimensional array by distance
                                sort_numerically(locationset);

                                //Featured locations filtering
                                if (settings.featuredLocations === true) {
                                    //Create array for featured locations
                                    featuredset = $.grep(locationset, function (val, i) {
                                        return val['featured'] === "true";
                                    });

                                    //Create array for normal locations
                                    normalset = $.grep(locationset, function (val, i) {
                                        return val['featured'] !== "true";
                                    });

                                    //Combine the arrays
                                    locationset = [];
                                    locationset = featuredset.concat(normalset);
                                }

                                //Get the length unit
                                var distUnit = (settings.lengthUnit === "km") ? settings.kilometersLang : settings.milesLang;

                                //Check the closest marker
                                if (settings.maxDistance === true && firstRun !== true && maxDistance) {
                                    if (locationset[0] === undefined || locationset[0]['distance'] > maxDistance) {
                                        console.log(settings.distanceErrorAlert + maxDistance + " " + distUnit);
                                        return;
                                    }
                                } else {
                                    if (settings.distanceAlert !== -1 && locationset[0]['distance'] > settings.distanceAlert) {
                                        console.log(settings.distanceErrorAlert + settings.distanceAlert + " " + distUnit);
                                    }
                                }

                                //Create the map with jQuery
                                $(function () {

                                    var key, value, locationData = {};

                                    //Instead of repeating the same thing twice below
                                    function create_location_variables(loopcount) {
                                        for (key in locationset[loopcount]) {
                                            value = locationset[loopcount][key];

                                            if (key === 'distance') {
                                                value = roundNumber(value, 2);
                                            }

                                            locationData[key] = value;
                                        }
                                    }

                                    //Define the location data for the templates
                                    function define_location_data(currentMarker) {
                                        create_location_variables(currentMarker.get("id"));

                                        var distLength;
                                        if (locationData['distance'] <= 1) {
                                            if (settings.lengthUnit === "km") {
                                                distLength = settings.kilometerLang;
                                            } else {
                                                distLength = settings.mileLang;
                                            }
                                        } else {
                                            if (settings.lengthUnit === "km") {
                                                distLength = settings.kilometersLang;
                                            } else {
                                                distLength = settings.milesLang;
                                            }
                                        }

                                        //Set up alpha character
                                        var markerId = currentMarker.get("id");
                                        //Use dot markers instead of alpha if there are more than 26 locations
                                        if (settings.storeLimit === -1 || settings.storeLimit > 26) {
                                            var indicator = markerId + 1;
                                        } else {
                                            var indicator = String.fromCharCode("A".charCodeAt(0) + markerId);
                                        }

                                        //Define location data
                                        var locations = {
                                            location: [$.extend(locationData, {
                                                    'markerid': markerId,
                                                    'marker': indicator,
                                                    'length': distLength,
                                                    'origin': origin
                                                })]
                                        };

                                        return locations;
                                    }

                                    //Slide in the map container
                                    if (settings.slideMap === true) {
                                        $this.slideDown();
                                    }
                                    //Set up the modal window
                                    if (settings.modalWindow === true) {
                                        // Callback
                                        if (settings.callbackModalOpen) {
                                            settings.callbackModalOpen.call(this);
                                        }

                                        function modalClose() {
                                            // Callback
                                            if (settings.callbackModalOpen) {
                                                settings.callbackModalOpen.call(this);
                                            }

                                            $('#' + settings.overlayDiv).hide();
                                        }

                                        //Pop up the modal window
                                        $('#' + settings.overlayDiv).fadeIn();
                                        //Close modal when close icon is clicked and when background overlay is clicked
                                        $(document).on('click.' + prefix, '#' + settings.modalCloseIconDiv + ', #' + settings.overlayDiv, function () {
                                            modalClose();
                                        });
                                        //Prevent clicks within the modal window from closing the entire thing
                                        $(document).on('click.' + prefix, '#' + settings.modalWindowDiv, function (e) {
                                            e.stopPropagation();
                                        });
                                        //Close modal when escape key is pressed
                                        $(document).on('keyup.' + prefix, function (e) {
                                            if (e.keyCode === 27) {
                                                modalClose();
                                            }
                                        });
                                    }

                                    //Google maps settings


                                    var myOptions = {
                                        zoom: settings.zoomLevel,
                                        center: new google.maps.LatLng(orig_lat, orig_lng),
                                        mapTypeId: google.maps.MapTypeId.ROADMAP,
                                        styles: [
                                                    {
                                                        "featureType": "administrative",
                                                        "elementType": "labels.text.fill",
                                                        "stylers": [
                                                            {
                                                                "color": "#444444"
                                                            }
                                                        ]
                                                    },
                                                    {
                                                        "featureType": "administrative.country",
                                                        "elementType": "geometry.stroke",
                                                        "stylers": [
                                                            {
                                                                "visibility": "on"
                                                            },
                                                            {
                                                                "lightness": "100"
                                                            }
                                                        ]
                                                    },
                                                    {
                                                        "featureType": "landscape",
                                                        "elementType": "all",
                                                        "stylers": [
                                                            {
                                                                "color": "#f2f2f2"
                                                            }
                                                        ]
                                                    },
                                                    {
                                                        "featureType": "poi",
                                                        "elementType": "all",
                                                        "stylers": [
                                                            {
                                                                "visibility": "off"
                                                            }
                                                        ]
                                                    },
                                                    {
                                                        "featureType": "poi",
                                                        "elementType": "geometry.stroke",
                                                        "stylers": [
                                                            {
                                                                "lightness": "42"
                                                            },
                                                            {
                                                                "visibility": "off"
                                                            }
                                                        ]
                                                    },
                                                    {
                                                        "featureType": "road",
                                                        "elementType": "all",
                                                        "stylers": [
                                                            {
                                                                "saturation": -100
                                                            },
                                                            {
                                                                "lightness": 45
                                                            }
                                                        ]
                                                    },
                                                    {
                                                        "featureType": "road.highway",
                                                        "elementType": "all",
                                                        "stylers": [
                                                            {
                                                                "visibility": "simplified"
                                                            }
                                                        ]
                                                    },
                                                    {
                                                        "featureType": "road.arterial",
                                                        "elementType": "labels.icon",
                                                        "stylers": [
                                                            {
                                                                "visibility": "off"
                                                            }
                                                        ]
                                                    },
                                                    {
                                                        "featureType": "transit",
                                                        "elementType": "all",
                                                        "stylers": [
                                                            {
                                                                "visibility": "off"
                                                            }
                                                        ]
                                                    },
                                                    {
                                                        "featureType": "water",
                                                        "elementType": "all",
                                                        "stylers": [
                                                            {
                                                                "color": "#b9d9e6"
                                                            },
                                                            {
                                                                "visibility": "on"
                                                            }
                                                        ]
                                                    }
                                                ],
                                        scrollwheel: false,
                                        streetViewControl: false,
                                        mapTypeControl: false,
                                        zoomControlOptions: {
                                            position: google.maps.ControlPosition.RIGHT_TOP
                                        },
                                    };


                                    if ((settings.fullMapStart === true && firstRun === true) || settings.zoomLevel === 0 || s == 'a') {
                                        /*var myOptions = {
                                            mapTypeId: google.maps.MapTypeId.ROADMAP
                                        };*/
                                        var bounds = new google.maps.LatLngBounds();
                                    }
                                    else
                                    {
                                    }

                                    var map = new google.maps.Map(document.getElementById(settings.mapDiv), myOptions);
                                    $this.data('map', map);

                                    //Create one infowindow to fill later
                                    var infowindow = new google.maps.InfoWindow();

                                    //Avoid error if number of locations is less than the default of 26
                                    if (settings.storeLimit === -1 || (locationset.length - 1) < settings.storeLimit - 1) {
                                        storenum = locationset.length - 1;
                                    } else {
                                        storenum = settings.storeLimit - 1;
                                    }

                                    //Add origin marker if the setting is set
                                    if (settings.originMarker === true && settings.fullMapStart === false) {
                                        var originPoint = new google.maps.LatLng(orig_lat, orig_lng);
                                        var marker = new google.maps.Marker({
                                            position: originPoint,
                                            map: map,
                                            icon: 'http://maps.google.com/mapfiles/ms/icons/' + settings.originpinColor + '-dot.png',
                                            draggable: false
                                        });
                                    }

                                    //Add markers and infowindows loop
                                    for (var y = 0; y <= storenum; y++) {
                                        var letter = String.fromCharCode("A".charCodeAt(0) + y);
                                        var point = new google.maps.LatLng(locationset[y]['lat'], locationset[y]['lng']);

                                        marker = createMarker(point, locationset[y]['name'], locationset[y]['address'], y);
                                        marker.set("id", y);
                                        markers[y] = marker;
                                        if ((settings.fullMapStart === true && firstRun === true) || settings.zoomLevel === 0 || s == 'a') {
                                            bounds.extend(point);
                                        }

                                        //Pass variables to the pop-up infowindows
                                        //create_infowindow(marker);

                                        var locations = define_location_data(marker);
                                        //Set up the list template with the location data
                                        var listHtml = listTemplate(locations);
                                        addClickListener(marker, listHtml);
                                        addHoverListener(marker);
                                    }

                                    var markerCluster = new MarkerClusterer(map, markers, {
                                        gridSize: 50,
                                        maxZoom: 10,
                                        styles: [{
                                                url: settings.clusterIcon,
                                                height: 48,
                                                width: 36,
                                                anchor: [0, 0],
                                                textColor: '#ffffff',
                                                textSize: 12
                                            }]
                                    });


                                    google.maps.event.addListener(markerCluster, 'clusterclick', function(cluster) {

                                        if (markerCluster.isZoomOnClick()) {
                                            map.fitBounds(cluster.getBounds());
                                            map.setZoom(map.getZoom()-1);
                                        }
                                    });

                                    google.maps.event.addListenerOnce(map, 'idle', function(){
                                        if($('#loading').size() > 0)
                                        {
                                            $('#loading').hide();
                                        }
                                    });


                                    //Center and zoom if no origin or zoom was provided
                                    if ((settings.fullMapStart === true && firstRun === true) || settings.zoomLevel === 0) {
                                        map.fitBounds(bounds);

                                        if (firstRun && settings.isMobile){
                                            setTimeout(function(){
                                                map.setZoom(1);
                                            }, 2000);
                                        }

                                        zoomChangeBoundsListener =
                                            google.maps.event.addListenerOnce(map, 'bounds_changed', function(event) {
                                                if (this.getZoom()){
                                                    this.setZoom(3);
                                                }

                                                if (firstRun){
                                                    map.setCenter(new google.maps.LatLng(settings.defaultLat, settings.defaultLng));
                                                }
                                        });
                                        setTimeout(function(){
                                            google.maps.event.removeListener(zoomChangeBoundsListener)
                                        }, 2000);
                                    }

                                    if (s == 'a') {
                                        map.fitBounds(bounds);
                                        map.setZoom(map.getZoom()-1);
                                    }

                                    //Create the links that focus on the related marker
                                    $("#" + settings.listDiv + ' #list').empty();


                                    var listLimit = settings.listLimit;
                                    var listIncrement = 1;
                                    $(markers).each(function (x, marker) {
                                        var letter = String.fromCharCode("A".charCodeAt(0) + x);
                                        //This needs to happen outside the loop or there will be a closure problem with creating the infowindows attached to the list click
                                        var currentMarker = markers[x];

                                        if (listLimit == -1 || listIncrement <= listLimit)
                                            listClick(currentMarker);

                                        listIncrement++;
                                    });

                                    function listClick(marker) {
                                        //Define the location data
                                        var locations = define_location_data(marker);

                                        //Set up the list template with the location data
                                        var listHtml = listTemplate(locations);
                                        $('#' + settings.listDiv + ' #list').append(listHtml);
                                    }

                                    $(document).on('mouseover.' + prefix, '.c-storeLocator__item', function () {
                                        var markerId = $(this).data('markerid');
                                        var selectedMarker = markers[markerId];

                                        selectedMarker.setIcon(settings.clusterIcon);
                                        selectedMarker.set('labelClass', 'c-map__label--hover');
                                    });

                                    $(document).on('mouseout.' + prefix, '.c-storeLocator__item', function () {
                                        var markerId = $(this).data('markerid');
                                        var selectedMarker = markers[markerId];

                                        selectedMarker.setIcon(settings.markerIcon);
                                        selectedMarker.set('labelClass', 'c-map__label');
                                    });

                                    //Handle clicks from the list
                                    $(document).on('click.' + prefix, '.c-storeLocator__item', function () {
                                        var markerId = $(this).data('markerid');

                                        var selectedMarker = markers[markerId];

                                        map.panTo(selectedMarker.getPosition());
                                        map.setZoom(12);
                                        var listLoc = "left";
                                        if (settings.bounceMarker === true) {
                                            selectedMarker.setAnimation(google.maps.Animation.BOUNCE);
                                            setTimeout(function () {
                                                selectedMarker.setAnimation(null);
                                                //create_infowindow(selectedMarker, listLoc);
                                            }, 700);
                                        } else {
                                            //create_infowindow(selectedMarker, listLoc);
                                        }
                                    });

                                    //Add the list li background colors
                                    //$("#" + settings.listDiv + " #list li:even").css('background', "#" + settings.listColor1);
                                    //$("#" + settings.listDiv + " #list li:odd").css('background', "#" + settings.listColor2);

                                    //Custom marker function - alphabetical
                                    function createMarker(point, name, address, markerid) {
                                        

                                        //var markerLabel = markerid + 1;

                                        //var left = 4;
                                        //if (markerLabel > 9) {
                                        //    left = 7;
                                        //}

                                        var left = 7;

                                        var marker = new MarkerWithLabel({
                                            position: point,
                                            map: map,
                                            title: name,
                                            labelContent: "",
                                            labelAnchor: new google.maps.Point(left, 35),
                                            labelClass: 'c-map__label',
                                            icon: settings.markerIcon,
                                            draggable: false
                                        });

                                        return marker;
                                    }

                                    //Infowindows
                                    function create_infowindow(marker, location) {

                                        //Define the location data
                                        var locations = define_location_data(marker);

                                        //Set up the infowindow template with the location data
                                        var formattedAddress = infowindowTemplate(locations);

                                        //Opens the infowindow when list item is clicked
                                        if (location === "left") {
                                            infowindow.setContent(formattedAddress);
                                            infowindow.open(marker.get('map'), marker);
                                        }
                                        //Opens the infowindow when the marker is clicked
                                        else {
                                            google.maps.event.addListener(marker, 'click', function () {
                                                infowindow.setContent(formattedAddress);
                                                infowindow.open(marker.get('map'), marker);
                                                //Focus on the list
                                                $('#' + settings.listDiv + ' li').removeClass('list-focus');
                                                markerId = marker.get("id");
                                                $('#' + settings.listDiv + ' li[data-markerid=' + markerId + ']').addClass('list-focus');

                                                //Scroll list to selected marker
                                                var container = $('#' + settings.listDiv), scrollTo = $('#' + settings.listDiv + ' div[data-markerid=' + markerId + ']');
                                                $('#' + settings.listDiv).animate({
                                                    scrollTop: scrollTo.offset().top - container.offset().top + container.scrollTop()
                                                });
                                            });
                                        }

                                    }
                                    
                                    function addClickListener(marker, location) {
                                        google.maps.event.addListener(marker, 'click', (function(marker) {
                                            return function() {
                                                $('.address-aside, .address-form').val('');
                                                $('#' + settings.listDiv + ' #list').empty();
                                                $('#' + settings.listDiv + ' #list').append(location);
                                                $('#' + settings.listDiv + ' #list').find('.c-storeLocator__item').addClass('is-active');
                                                storeLocation._searchForm();
                                            }
                                        })(marker, i));
                                    }
                                    
                                    function addHoverListener(marker) {
                                        google.maps.event.addListener(marker, 'mouseover', (function() {
                                            return function() {
                                                markerId = marker.get("id");
                                                $('#' + settings.listDiv + ' #list div[data-markerid=' + markerId + ']').addClass('is-active');
                                                storeLocation._searchForm();
                                                marker.setIcon(settings.clusterIcon);
                                                marker.set('labelClass', 'c-map__label--hover');
                                                
                                                //Scroll list to selected marker
                                                var container = $('#' + settings.listDiv), scrollTo = $('#' + settings.listDiv + ' #list div[data-markerid=' + markerId + ']');
                                                $('#' + settings.listDiv).animate({
                                                    scrollTop: scrollTo.offset().top - container.offset().top + container.scrollTop()
                                                });
                                            }
                                        })(marker, i));

                                        google.maps.event.addListener(marker, 'mouseout', (function() {
                                            return function() {
                                                markerId = marker.get("id");
                                                $('#' + settings.listDiv + ' #list div[data-markerid=' + markerId + ']').removeClass('is-active');
                                                storeLocation._searchForm();
                                                marker.setIcon(settings.markerIcon);  
                                                marker.set('labelClass', 'c-map__label');                                                                                            
                                            }
                                        })(marker, i));
                                    }

                                });
                            }
                        });
                    });
                }

            }

        });
    };
})(jQuery);
