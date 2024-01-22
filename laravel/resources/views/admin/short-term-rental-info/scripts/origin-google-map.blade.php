@push('scripts')
    <script type="text/javascript">
        function supports_geolocation() {
            return !!navigator.geolocation;
        }

        function get_origin_location() {
            if (supports_geolocation()) {
                @if (isset($view_only) && (empty($d->origin_lat) || empty($d->origin_lng)))
                    $("#msg-origin").text('Location not found');
                    $("#map-origin").css("height", "0");
                @elseif (isset($location) && $location == false)
                    // navigator.geolocation.getCurrentPosition(show_origin_map, handle_origin_error);
                @else
                    navigator.geolocation.getCurrentPosition(show_origin_map, handle_origin_error);
                @endif
            } else {
                // no native support;
                $("#msg-origin").text('Your browser doesn\'t support geolocation!');
                $("#map-origin").css("height", "0");
            }
        }

        function show_origin_map(position) {
            @if (!empty($d->origin_lat) && !empty($d->origin_lng))
                var latitude = '{{ $d->origin_lat }}';
                var longitude = '{{ $d->origin_lng }}';
            @else
                var latitude = position.coords.latitude;
                var longitude = position.coords.longitude;
            @endif

            var latlng = new google.maps.LatLng(latitude, longitude);
            document.getElementById("origin_lat_temp").value = latitude;
            document.getElementById("origin_lng_temp").value = longitude;

            document.getElementById("origin_lat_temp").addEventListener("keyup", () => {
                var latitude = document.getElementById("origin_lat_temp").value;
                var longitude = document.getElementById("origin_lng_temp").value;
                var latlng = new google.maps.LatLng(latitude, longitude);
                marker.setPosition(latlng);
                map.setCenter(latlng);
                map.setZoom(13)
            });

            document.getElementById("origin_lng_temp").addEventListener("keyup", () => {
                var latitude = document.getElementById("origin_lat_temp").value;
                var longitude = document.getElementById("origin_lng_temp").value;
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
            var map = new google.maps.Map(document.getElementById("map-origin"), myOptions);
            window._map = map;
            var marker = new google.maps.Marker({
                position: latlng,
                draggable: @if (isset($view_only))
                    false
                @else
                    true
                @endif ,
            });

            // To add the marker to the map, call setMap();
            marker.setMap(map);

            google.maps.event.addListener(marker, 'dragend', function(marker) {
                var latLng = marker.latLng;
                currentLatitude = latLng.lat();
                currentLongitude = latLng.lng();
                document.getElementById("origin_lat_temp").value = currentLatitude;
                document.getElementById("origin_lng_temp").value = currentLongitude;
            });

            // add button get coordinates
            @if (isset($location_btn))
                {
                    const centerControlDiv = document.createElement("div");
                    centerControl(centerControlDiv, map, marker, marker.latLng);
                    map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(centerControlDiv);
                }
            @endif
        }

        function handle_origin_error(err) {
            if (err.code == 1) {
                $("#msg-origin").text('Please allow sharing your location.');
            }
        }

        get_origin_location();
    </script>
@endpush
