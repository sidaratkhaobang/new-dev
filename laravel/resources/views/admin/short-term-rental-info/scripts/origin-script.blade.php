@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.map_api_key') }}"></script>
<script>
    function openOriginModal() {
        resetOriginInputs();
        $("#modal-origin-modal").modal("show");
    }

    function addNewOrgin() {
        origin_name = $('#origin_name_temp').val();
        origin_address = $('#origin_address_temp').val();
        origin_lat = $('.origin_lat').val();
        origin_lng = $('.origin_lng').val();
        console.log([origin_name, origin_address]);
        if (!origin_name || !origin_address) {
            warningAlert("{{ __('lang.required_field_inform') }}");
        }

        $('#origin_name').val(origin_name);
        $('#origin_address').val(origin_address);
        $('#origin_lat').val(origin_lat);
        $('#origin_lng').val(origin_lng);
        $("#origin_id").val('').change();
        appendOriginSelection(origin_name);
        $("#modal-origin-modal").modal("hide");
    }

    function resetOriginInputs() {
        $('#modal-origin-modal').find('input:text').val('');
    }

    function appendOriginSelection(name) {
        var defaultOriginOption = {
            id: 'ADDITIONAL',
            text: name,
        };
        var tempOriginOption = new Option(defaultOriginOption.text, defaultOriginOption.id, true, true);
        $("#origin_id").append(tempOriginOption).trigger('change');
        console.log('append');
    }
</script>
@endpush