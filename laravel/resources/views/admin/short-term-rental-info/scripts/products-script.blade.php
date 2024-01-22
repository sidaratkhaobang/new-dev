@push('scripts')
<script>
    function getProductData() {
        var branch_id = $('#branch_id').val();
        var product_id_filter = $('#product_id_filter').val();
        var service_type_id = $('#service_type_id').val();
        var params = {
            service_type_id: service_type_id,
            branch_id: branch_id,
            type_package: "{{$d?->type_package}}",
            product_id_filter,
            product_id_filter
        };
        axios.post("{{ route('admin.short-term-rental.info.product-data') }}", params).then(response => {
            if (response.data.success) {
                $('#carousel-products').carousel('dispose');
                document.querySelector(".carousel-inner").innerHTML = response.data.html;
                $('#carousel-products').carousel({
                    interval: 0
                });

                var product_id_selected = $('#product_id_selected').val();
                if (product_id_selected != "") {
                    $("input[name=product_id][value='" + product_id_selected + "']").prop("checked", true);
                }
            }
        });
    }

    $(document).ready(() => {
        getProductData();

        $('#branch_id').on('select2:select', function() {
            getProductData();
        });

        $('#product_id_filter').on('select2:select', function() {
            getProductData();
        });
    });
</script>
@endpush