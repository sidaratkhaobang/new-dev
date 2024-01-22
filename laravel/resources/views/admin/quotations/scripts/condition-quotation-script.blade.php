@push('scripts')
    <script>
        function addConditionQuotation() {
            getConditionQuotationVue.addConditionQuotation();
        }

        function addCondition(id) {
            getConditionQuotationVue.addCondition(id);
        }

        function addConditionQuotationChecklist() {
            getConditionQuotationVue.addConditionQuotationChecklist();
        }

        function removeConditionQuotation() {
            getConditionQuotationVue.removeConditionQuotation();
        }

        function removeConditionQuotationChecklist(k) {
            getConditionQuotationVue.removeConditionQuotationChecklist(k);
        }

        function GetMaxSeq(Forms = null, Type = "Seq") {
            let SeqArray = [];
            var MaxSeq = 0;
            if (Type === "Seq") {
                for (let i = 0; i < Forms.length; i++) {
                    SeqArray.push(Forms[i].seq)
                }
            }
            if (Type === "SubSeq") {
                for (let i = 0; i < Forms.length; i++) {
                    SeqArray.push(Forms[i].quotation_form_checklist_seq)
                }
            }
            if (SeqArray.length > 0) {
                MaxSeq = Math.max(...SeqArray);
            }
            MaxSeq = MaxSeq + 1
            return MaxSeq
        }


        let getConditionQuotationVue = new Vue({
            el: '#condition-quotation',

            data() {
                return {
                    quotation_forms: @if (isset($quotation_form))
                        @json($quotation_form)
                        @else
                    [{
                            quotation_form_status: true,
                            seq: '',
                            name: '',
                            sub_quotation_form_checklist: []
                        }]
                    @endif ,
                    del_input_id: [],
                    del_input_sub_id: [],
                }
            },
            methods: {
                addConditionQuotation() {
                    let seq = GetMaxSeq(this.quotation_forms)
                    this.quotation_forms.push({
                        quotation_form_status: true,
                        seq: seq,
                        name: '',
                        sub_quotation_form_checklist: [],
                        id: null,
                    })

                },
                addCondition(id) {
                    axios.get("{{ route('admin.quotations.condition') }}", {
                        params: {
                            id: id
                        }
                    }).then(response => {
                        if (response.data) {
                            quotation_forms_length = this.quotation_forms.length;
                            response.data.forEach((e, index) => {

                                this.quotation_forms.push({
                                    quotation_form_status: true,
                                    seq: e.seq,
                                    name: e.name,
                                    sub_quotation_form_checklist: [],
                                    id: null,
                                })
                                if (e.sub_quotation_form_checklist.length > 0) {
                                    e.sub_quotation_form_checklist.forEach((k) => {
                                        this.quotation_forms[quotation_forms_length + index].sub_quotation_form_checklist.push({
                                            quotation_form_checklist_status: true,
                                            quotation_form_checklist_seq: k.seq,
                                            quotation_form_checklist_name: k.name,
                                            id: null,
                                        })
                                    })
                                }

                            })
                        }
                    });


                },
                addConditionQuotationChecklist(k) {
                    let seq = GetMaxSeq(this.quotation_forms[k].sub_quotation_form_checklist, "SubSeq")

                    this.quotation_forms[k].sub_quotation_form_checklist.push({
                        quotation_form_checklist_status: true,
                        quotation_form_checklist_seq: seq,
                        quotation_form_checklist_name: '',
                        id: null,
                    })
                },
                hide(k) {
                    if ($("#sub-quotation-form-checklist" + k).is(":hidden")) {
                        $("hd").removeClass('hidden');
                        $('#arrow-' + k).removeClass('fa-angle-right');
                        $('#arrow-' + k).addClass('fa-angle-down');
                        $("#sub-quotation-form-checklist" + k).show();
                    } else {
                        $("#sub-quotation-form-checklist" + k).hide()
                        $("hd").addClass('hidden')
                        $('#arrow-' + k).removeClass('fa-angle-down');
                        $('#arrow-' + k).addClass('fa-angle-right');
                        $("#sub-quotation-form-checklist" + k).hide()

                    }
                },

                removeConditionQuotation(k) {
                    this.del_input_id.push(this.quotation_forms[k].id);
                    this.quotation_forms.splice(k, 1)
                },
                removeConditionQuotationChecklist(k, k2) {
                    this.del_input_sub_id.push(this.quotation_forms[k].sub_quotation_form_checklist[k2].id);
                    this.quotation_forms[k].sub_quotation_form_checklist.splice(k2, 1)
                },
            }
        })
    </script>
@endpush
