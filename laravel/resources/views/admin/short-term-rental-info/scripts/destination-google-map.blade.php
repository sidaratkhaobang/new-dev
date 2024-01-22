@push('scripts')
    <script type="text/javascript">
        function supports_geolocation() {
            return !!navigator.geolocation;
        }

        function get_destination_location() {
            if (supports_geolocation()) {
                @if (isset($view_only) && (empty($d->destination_lat) || empty($d->destination_lng)))
                    $("#msg-destination").text('Location not found');
                    $("#map-destination").css("height", "0");
                @elseif (isset($location) && $location == false)
                    // navigator.geolocation.getCurrentPosition(show_destination_map, handle_destination_error);
                @else
                    navigator.geolocation.getCurrentPosition(show_destination_map, handle_destination_error);
                @endif
            } else {
                // no native support;
                $("#msg-destination").text('Your browser doesn\'t support geolocation!');
                $("#map-destination").css("height", "0");
            }
        }

        function show_destination_map(position) {
            @if (!empty($d->destination_lat) && !empty($d->destination_lng))
                var latitude = '{{ $d->destination_lat }}';
                var longitude = '{{ $d->destination_lng }}';
            @else
                var latitude = position.coords.latitude;
                var longitude = position.coords.longitude;
            @endif

            var latlng = new google.maps.LatLng(latitude, longitude);
            document.getElementById("destination_lat_temp").value = latitude;
            document.getElementById("destination_lng_temp").value = longitude;

            document.getElementById("destination_lat_temp").addEventListener("keyup", () => {
                var latitude = document.getElementById("destination_lat_temp").value;
                var longitude = document.getElementById("destination_lng_temp").value;
                var latlng = new google.maps.LatLng(latitude, longitude);
                marker.setPosition(latlng);
                map.setCenter(latlng);
                map.setZoom(13)
            });

            document.getElementById("destination_lng_temp").addEventListener("keyup", () => {
                var latitude = document.getElementById("destination_lat_temp").value;
                var longitude = document.getElementById("destination_lng_temp").value;
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
            var map = new google.maps.Map(document.getElementById("map-destination"), myOptions);
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
                document.getElementById("destination_lat_temp").value = currentLatitude;
                document.getElementById("destination_lng_temp").value = currentLongitude;
            });
        }

        function handle_destination_error(err) {
            if (err.code == 1) {
                $("#msg-destination").text('Please allow sharing your location.');
            }
        }
        get_destination_location();
    </script>
@endpush
