@push('scripts')
    <script>
        function add() {
            addConditionRepairServiceVue.add();
        }

        let addConditionRepairServiceVue = new Vue({
            el: '#condition-repair',
            data() {
                return {
                    condition_repair: @if (isset($condition_repair))
                        @json($condition_repair)
                    @else
                        []
                    @endif ,
                    del_input_id: [],
                    del_input_sub_id: [],
                }
            },
            methods: {
                add() {
                    this.condition_repair.push({
                        status_section: false,
                        seq: '',
                        name: '',
                        sub_condition_repair: [],
                        id: null,
                    })
                },
                addSub(k) {
                    this.condition_repair[k].sub_condition_repair.push({
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
                    this.del_input_id.push(this.condition_repair[k].id);
                    this.condition_repair.splice(k, 1);
                },
                removeList(k, k2) {
                    this.del_input_sub_id.push(this.condition_repair[k].sub_condition_repair[k2].id);
                    this.condition_repair[k].sub_condition_repair.splice(k2, 1);
                },
            }
        })
    </script>
@endpush
