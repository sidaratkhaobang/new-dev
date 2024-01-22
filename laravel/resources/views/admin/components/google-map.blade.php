@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.map_api_key') }}"></script>
<script type="text/javascript">
    function supports_geolocation() {
        return !!navigator.geolocation;
    }

    function get_location() {
        if (supports_geolocation()) {
            @if( isset($view_only) && (empty($d->lat) || empty($d->lng)) )
                $("#msg").text('Location not found');
                $("#map").css("height","0");
            @elseif(isset($location) && $location == false)
            // navigator.geolocation.getCurrentPosition(show_map, handle_error);
            @else
                navigator.geolocation.getCurrentPosition(show_map, handle_error);
            @endif
        } else {
            // no native support;
            $("#msg").text('Your browser doesn\'t support geolocation!');
            $("#map").css("height","0");
        }
    }

    function show_map(position) {
        @if( !empty($d->lat) && !empty($d->lng) ) 
            var latitude = '{{ $d->lat }}';
            var longitude = '{{ $d->lng }}';
        @else
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;
        @endif
    
        var latlng = new google.maps.LatLng(latitude, longitude);
        document.getElementById("lat").value = latitude;
        document.getElementById("lng").value = longitude;

        document.getElementById("lat").addEventListener("keyup", () => {
            var latitude = document.getElementById("lat").value;
            var longitude = document.getElementById("lng").value;
            var latlng = new google.maps.LatLng(latitude, longitude);
            marker.setPosition(latlng);
            map.setCenter(latlng);
            map.setZoom(13)
        });

        document.getElementById("lng").addEventListener("keyup", () => {
            var latitude = document.getElementById("lat").value;
            var longitude = document.getElementById("lng").value;
            var latlng = new google.maps.LatLng(latitude, longitude);
            marker.setPosition(latlng);
            map.setCenter(latlng);
            map.setZoom(13)
        });

        var myOptions = {
            zoom: 13,
            center: latlng,
            streetViewControl: true,
            mapTypeControl: false,
        };
        var map = new google.maps.Map(document.getElementById("map"), myOptions);
        window._map = map;
        var marker = new google.maps.Marker({
            position: latlng,
            draggable: @if(isset($view_only)) false @else true @endif,
        });

        // To add the marker to the map, call setMap();
        marker.setMap(map);

        google.maps.event.addListener(marker, 'dragend', function(marker) {
            var latLng = marker.latLng;
            currentLatitude = latLng.lat();
            currentLongitude = latLng.lng();
            document.getElementById("lat").value = currentLatitude;
            document.getElementById("lng").value = currentLongitude;
        });

        // add button get coordinates
        @if (isset($location_btn)) {
            const centerControlDiv = document.createElement("div");
            centerControl(centerControlDiv, map, marker, marker.latLng);
            map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(centerControlDiv);
        }
        @endif
    }

    function handle_error(err) {
        if (err.code == 1) {
            $("#msg").text('Please allow sharing your location.');
        }
    }

    function centerControl(controlDiv, map, marker, latlng) {
        // Set CSS for the control border.
        const controlUI = document.createElement("div");
        controlUI.style.backgroundColor = "#fff";
        controlUI.style.border = "2px solid #fff";
        controlUI.style.borderRadius = "2px";
        controlUI.style.boxShadow = "0px 1px 4px -1px rgba(0,0,0,.3)";
        controlUI.style.cursor = "pointer";
        controlUI.style.margin = "10px";
        controlUI.style.width = "40px";
        controlUI.style.hright = "40px";
        controlUI.style.textAlign = "center";
        controlUI.title = "Click to recenter the map";
        controlDiv.appendChild(controlUI);

        // Set CSS for the control interior.
        const controlText = document.createElement("div");
        controlText.className = "btn-location-arrow"
        controlText.style.color = "rgb(102,102,102)";
        controlText.style.fontFamily = "Roboto,Arial,sans-serif";
        controlText.style.fontSize = "16px";
        controlText.style.lineHeight = "38px";
        controlText.style.paddingLeft = "5px";
        controlText.style.paddingRight = "5px";
        controlText.innerHTML = '<i class="fa fa-location-arrow" aria-hidden="true"></i>';
        controlUI.appendChild(controlText);

        controlUI.addEventListener("click", () => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };
                        map.setCenter(pos);
                        marker.setPosition(pos);
                        document.getElementById("lat").value = pos.lat;
                        document.getElementById("lng").value = pos.lng;
                    }, handle_error
                );
            }
        });
    }

    get_location();
</script>
@endpush
