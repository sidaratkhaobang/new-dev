@push('scripts')
    <script>
        function addSubConditionChecklist() {
            getConditionQuotationVue.addSubConditionChecklist();
        }

        let getConditionQuotationVue = new Vue({
            el: '#sub-condition',

            data() {
                return {
                    sub_condition_checklist: @if (isset($sub_condition_checklist)) @json($sub_condition_checklist) @else [{
                            status: true,
                            seq: '',
                            name: '',
                        }]
                    @endif ,
                    del_checklist_id: [],
                }
            },
            methods: {
                addSubConditionChecklist() {
                    this.sub_condition_checklist.push({
                        status: true,
                        seq: '',
                        name: '',
                        id: null,
                    })
                },
                removeCheckList(k) {
                    this.del_checklist_id.push(this.sub_condition_checklist[k].id);
                    console.log(this.del_checklist_id);
                    this.sub_condition_checklist.splice(k, 1)
                },
            }
        })
    </script>
@endpush
