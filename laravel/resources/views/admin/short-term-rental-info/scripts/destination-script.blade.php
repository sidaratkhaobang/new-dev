@push('scripts')
<script>
    function openDestinationModal() {
        resetDestinationInputs();
        $("#modal-destination-modal").modal("show");
    }

    function addNewDestination() {
        destination_name = $('#destination_name_temp').val();
        destination_address = $('#destination_address_temp').val();
        destination_lat = $('.destination_lat').val();
        destination_lng = $('.destination_lng').val();
        if (!destination_name || !destination_address) {
            return warningAlert("{{ __('lang.required_field_inform') }}");
        }

        $('#destination_name').val(destination_name);
        $('#destination_address').val(destination_address);
        $('#destination_lat').val(destination_lat);
        $('#destination_lng').val(destination_lng);
        $("#destination_id").val('').change();
        appendDestinationSelection(destination_name);
        $("#modal-destination-modal").modal("hide");
    }

    function resetDestinationInputs() {
        $('#modal-destination-modal').find('input:text').val('');
    }

    function appendDestinationSelection(name) {
        var defaultDestinationOption = {
            id: 'ADDITIONAL',
            text: name,
        };
        var tempDestinationOption = new Option(defaultDestinationOption.text, defaultDestinationOption.id, true, true);
        $("#destination_id").append(tempDestinationOption).trigger('change');
    }
</script>
@endpush