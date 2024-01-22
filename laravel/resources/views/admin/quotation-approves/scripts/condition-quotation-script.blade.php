@push('scripts')
    <script>
        function addConditionQuotation() {
            getConditionQuotationVue.addConditionQuotation();
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

        let getConditionQuotationVue = new Vue({
            el: '#condition-quotation',

            data() {
                return {
                    quotation_forms: @if (isset($quotation_form)) @json($quotation_form) @else [{
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
                    this.quotation_forms.push({
                        quotation_form_status: true,
                        seq: '',
                        name: '',
                        sub_quotation_form_checklist: [],
                        id: null,
                    })

                },
                addConditionQuotationChecklist(k) {
                    this.quotation_forms[k].sub_quotation_form_checklist.push({
                        quotation_form_checklist_status: true,
                        quotation_form_checklist_seq: '',
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
