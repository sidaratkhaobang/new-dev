@push('scripts')
    <script>
        $informer = '{{ $d->informer }}';
        $('#informer_type').on('select2:select', function(e) {
            var informer_type = document.getElementById("informer_type").value;
            if (informer_type === "{{ RepairEnum::TLS }}") {
                document.getElementById("informer_id").style.display = "block"
            } else {
                document.getElementById("informer_id").style.display = "none"
                $("#informer").val($informer).change();
            }
        });

        $('input[name="in_center"]').on("click", function() {
            if ($('input[name="in_center"]:checked').val() === '{{ BOOL_FALSE }}') {
                document.getElementById("driver_in_center").style.display = "block"
            } else {
                document.getElementById("driver_in_center").style.display = "none"
                $('input[name="is_driver_in_center"]').prop('checked', false);
            }
        });
        $is_driver_in_center = '{{$d->is_driver_in_center}}';
        if($is_driver_in_center == '{{ BOOL_TRUE }}') {
            $('input[name="in_center"]').prop('disabled', true);
            $('input[name="is_driver_in_center"]').prop('disabled', true);
            $('#in_center_date').prop('disabled', true);
        }

        $('input[name="out_center"]').on("click", function() {
            if ($('input[name="out_center"]:checked').val() === '{{ BOOL_FALSE }}') {
                document.getElementById("driver_out_center").style.display = "block"
            } else {
                document.getElementById("driver_out_center").style.display = "none"
                $('input[name="is_driver_out_center"]').prop('checked', false);
            }
        });
        $is_driver_out_center = '{{$d->is_driver_out_center}}';
        if($is_driver_out_center == '{{ BOOL_TRUE }}') {
            $('input[name="out_center"]').prop('disabled', true);
            $('input[name="is_driver_out_center"]').prop('disabled', true);
            $('#out_center_date').prop('disabled', true);
        }

        $replacement_date = '{{ $d->replacement_date }}';
        $replacement_type = '{{ $d->replacement_type }}';
        $replacement_place = '{{ $d->replacement_place }}';
        $('input[name="is_replacement"]').on("click", function() {
            if ($('input[name="is_replacement"]:checked').val() === '{{ BOOL_FALSE }}') {
                document.getElementById("re_date").style.display = "none"
                document.getElementById("re_type").style.display = "none"
                document.getElementById("re_place").style.display = "none"
                $("#replacement_date").val($replacement_date);
                $("#replacement_type").val($replacement_type).change();
                $("#replacement_place").val($replacement_place);
            } else {
                document.getElementById("re_date").style.display = "block"
                document.getElementById("re_type").style.display = "block"
                document.getElementById("re_place").style.display = "block"
            }
        });

        $is_replacement = '{{$d->is_replacement}}';
        if($is_replacement == '{{ BOOL_TRUE }}') {
            $('input[name="is_replacement"]').prop('disabled', true);
            $('#replacement_date').prop('disabled', true);
            $('#replacement_type').prop('disabled', true);
            $('#replacement_place').prop('disabled', true);
        }
    </script>
@endpush
