@push('scripts')
    <script>
        $(document).ready(function() {
            var $selectAll = $('#selectAll');
            var $table = $('.table');
            var $tdCheckbox = $table.find('tbody input:checkbox');
            var tdCheckboxChecked = 0;

            $selectAll.on('click', function() {
                $tdCheckbox.prop('checked', this.checked);
            });

            $tdCheckbox.on('change', function(e) {
                tdCheckboxChecked = $table.find('tbody input:checkbox:checked').length;
                $selectAll.prop('checked', (tdCheckboxChecked === $tdCheckbox.length));
            })
        });

        function openModalSendCheck() {
            var enum_pending = '{{ \App\Enums\GPSStatusEnum::PENDING }}';
            var check_list = @json($list);
            var arr_check = [];
            sendCarToCheckVue.removeAll();
            if (check_list.data.length > 0) {
                check_list.data.forEach(function(item, index) {
                    this_checkbox = $('input[name="row_' + item.id + '"]');
                    var is_check = this_checkbox.prop('checked');
                    if (is_check) {
                        if (item.status == enum_pending) {
                            sendCarToCheckVue.addByDefault(item);
                        }
                    }
                });
            }
            $('#modal-send-check-siganl').modal('show');
        }
    </script>
@endpush
