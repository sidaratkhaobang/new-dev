@push('scripts')
    <script>
        function add() {
            addConditionRepairServiceVue.add();
        }

        let addConditionRepairServiceVue = new Vue({
            el: '#condition-repair-service',
            data() {
                return {
                    condition_service: @if (isset($condition_service))
                        @json($condition_service)
                    @else
                        []
                    @endif ,
                    del_input_id: [],
                    del_input_sub_id: [],
                }
            },
            methods: {
                add() {
                    this.condition_service.push({
                        status_section: false,
                        seq: '',
                        name: '',
                        sub_condition_service: [],
                        id: null,
                    })
                },
                addSub(k) {
                    this.condition_service[k].sub_condition_service.push({
                        status_list: false,
                        seq: '',
                        name: '',
                        id: null,
                    });
                },
                hide(k) {
                    if ($("#sub-service" + k).is(":hidden")) {
                        $("hd").removeClass('hidden');
                        $('#arrow-' + k).removeClass('fa-angle-right');
                        $('#arrow-' + k).addClass('fa-angle-down');
                        $("#sub-service" + k).show();
                    } else {
                        $("#sub-service" + k).hide();
                        $("hd").addClass('hidden');
                        $('#arrow-' + k).removeClass('fa-angle-down');
                        $('#arrow-' + k).addClass('fa-angle-right');
                        $("#sub-service" + k).hide();

                    }
                },
                remove(k) {
                    this.del_input_id.push(this.condition_service[k].id);
                    this.condition_service.splice(k, 1);
                },
                removeList(k, k2) {
                    this.del_input_sub_id.push(this.condition_service[k].sub_condition_service[k2].id);
                    this.condition_service[k].sub_condition_service.splice(k2, 1);
                },
            }
        })
    </script>
@endpush
