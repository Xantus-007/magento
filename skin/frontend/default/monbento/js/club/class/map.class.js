var Club_Map;

(function($){
Club_Map = Klass.create({
    EVENT_READY:'map_ready',
    
    $container:null,
    searchUrl:null,
    isMapReady:false,
    defaultZoom: 5,
    defaultLat: 46.649436,
    defaultLng: 1.82373,
    markers:[],
    filter: null,
    
    map:null,
    clusterer:null,
    mapOptions:null,
    
    currentSearchAjax:null,
    
    init: function($container, filter, searchUrl)
    {
        var self = this;
        
        this.$container = $container;
        $container.slideUp(0);
        //this.$searchForm = $searchForm;
        this.filter = filter;
        this.searchUrl = searchUrl;
        
        
        //Bind on map ready
        $(this).bind(this.EVENT_READY, function(){
            self.mapReadyHandler();
        });
    },
            
    load: function()
    {
        if (navigator.geolocation)
        {
            var self = this;
            
            navigator.geolocation.getCurrentPosition(function(position){
                if(position && typeof(position) !== 'function' && !self.isMapReady)
                {
                    self.defaultLat = position.coords.latitude;
                    self.defaultLng = position.coords.longitude;
                    self.defaultZoom = 7
                    
                    $(self).trigger(self.EVENT_READY);
                }
            });
        }
        else
        {
            $(this).trigger(this.EVENT_READY);
        }
    },
    
    mapReadyHandler: function()
    {
        var self = this;
        this.$container.slideDown({
            duration: 'fast', 
            complete: function(){
                self.isMapReady = true;
                self.mapOptions = {
                    zoom: self.defaultZoom,
                    center: new google.maps.LatLng(self.defaultLat, self.defaultLng),
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };

                self.map = new google.maps.Map(document.getElementById(self.$container.attr('id')), self.mapOptions);


                var mcOptions = {gridSize: 50, maxZoom: 15};
                self.clusterer = new MarkerClusterer(self.map, [], mcOptions);

                google.maps.event.addListener(self.map, 'bounds_changed', function(){
                    self.stopAjax(); 
                });

                google.maps.event.addListener(self.map, 'idle', function(){
                    self.boundsChangeHandler();
                });
            }
        });
    },
    
    boundsChangeHandler: function()
    {
        this.refreshPoints();
    },
    
    refreshPoints: function()
    {
        var self = this;
        this.stopAjax();
        
        this.currentSearchAjax = $.ajax({
            url:this.searchUrl,
            data:{
                SW_lat:this.map.getBounds().getSouthWest().lat(),
                SW_lng:this.map.getBounds().getSouthWest().lng(),
                NE_lat:this.map.getBounds().getNorthEast().lat(),
                NE_lng:this.map.getBounds().getNorthEast().lng(),
                filter:this.filter
            },
            dataType: 'json',
            success: function(result){
                self.refreshPointsHandler(result)
            }
        })
    },
    
    refreshPointsHandler: function(result)
    {
        
        if(result)
        {
            for(i = 0; i< result.length; i++)
            {
                var point = result[i];
                
                if(!this.markers[point.id])
                {
                    this.addMarker(point);
                }
            }
        }
    },
    
    addMarker: function(point)
    {
        var self = this;
        var img = '<img style="border:4px solid #FFF" src="'+point.thumb+'" />';
        
        var marker = new RichMarker({
            map: this.map,
            draggable: false,
            position: new google.maps.LatLng(point.lat, point.lng),
            flat: false,
            anchor: RichMarkerPosition.MIDDLE,
            content: img,
            point: point
        });
        
        google.maps.event.addListener(marker, 'click', function(){
            self.markerClickHandler(marker);
        })
        
        this.clusterer.addMarker(marker);
        
        this.markers[point.id] = marker;
    },
            
    markerClickHandler: function(marker)
    {
        if(marker.point && marker.point.url)
        {
            document.location.href= marker.point.url;
        }
    },
            
    searchFormSubmitHandler: function()
    {
        var self = this;
        var input = this.$searchForm.find('input[name=mapSearch]');
        var query = input.val();
        this.filter = query;
        
        for(var i = 0; i < this.markers.length; i++)
        {
            var tmp = this.markers[i];
            if(tmp)
            {
                tmp.setMap(null);
                this.markers[i] = null;
            }
        }
        
        this.clusterer.clearMarkers();
        this.refreshPoints();
    },
    
    searchFormSuccessHandler: function(result)
    {
        if(result && result.results)
        {
            var first = result.results.pop();
            
            if(first)
            {
                var geom = first.geometry.bounds;
                var NE = new google.maps.LatLng(geom.northeast.lat, geom.northeast.lng);
                var SW = new google.maps.LatLng(geom.southwest.lat, geom.southwest.lng);
                
                var bounds = new google.maps.LatLngBounds(SW, NE);
                
                this.map.fitBounds(bounds);
            }
        }
    },
            
    stopAjax: function()
    {
        if(this.currentSearchAjax)
        {
            this.currentSearchAjax.abort();
        }
    },
});
})(jQuery);