@push('scripts')
    <script>
        function add2() {
            carVue2.add();
            carVue2.selectTwo();
        }

        function addSub2() {
            carVue2.addSub();
        }

        function remove(k) {
            carVue2.remove(k);
        }

        function removeList2(k) {
            carVue2.removeList(k);
        }

        let carVue2 = new Vue({
            el: '#app2',
            data() {
                return {
                    form_list: @if (isset($form_list))
                        []
                    @else
                        []
                    @endif ,
                    listConditionOut: @if (isset($listConditionOut))
                        @json($listConditionOut)
                    @else
                        []
                    @endif ,
                    listCondition: @if (isset($listCondition))
                        @json($listCondition)
                    @else
                        []
                    @endif ,
                    flow_type: @if (isset($d))
                        @json($d->id)
                    @else
                        []
                    @endif ,
                    listForm: @if (isset($form_list))
                    @json($form_list)
                    @else
                        []
                    @endif ,
                    inputs: @if (isset($question_list))

                        @json($question_list)
                    @else
                        [{
                            seq: '',
                            condition: null,
                            department: null,
                            role: null,
                            in_form: '',
                            photo: false,
                            inspector_signature: false,
                            send_mobile: false,
                            dpf_oil:false,
                            transfer_type: 2,
                        }]
                    @endif ,
                    del_input_id: [],

                }
            },
            mounted: function() {
                // $('#in_form0').select2({
                //     data: this.form_list,
                //     placeholder: '- กรุณาเลือก -',
                // })
                // $('#department0').select2({
                //     data: this.form_list,
                //     placeholder: '- กรุณาเลือก -'
                // })
                // $('#condition0').select2({
                //     data: this.form_list,
                //     placeholder: '- กรุณาเลือก -'
                // })
                // /* $('.list').select2({
                //     data: this.form_list,
                //     placeholder: '- กรุณาเลือก -'
                // }) */
                // $('#role0').select2({
                //     data: this.form_list,
                //     placeholder: '- กรุณาเลือก -'
                // })
                // $('.list').one('select2:open', function(e) {
                //     $('input.select2-search__field').prop('placeholder', 'ค้นหา...');
                // });
            },
            methods: {
                add() {

                    this.inputs.push({
                        seq: '',
                        condition: null,
                        department: null,
                        role: null,
                        in_form: '',
                        photo: false,
                        inspector_signature: false,
                        send_mobile: false,
                        transfer_type: 2,

                    })
                },

                async selectTwo() {
                    await this.$nextTick()
                    var index = this.inputs.length;
                    index = index - 1;
                    $("#in_form" + index).select2({
                        data: this.form_list,
                        placeholder: '- กรุณาเลือก -',
                        allowClear: true,

                    })
                    $("#condition" + index).select2({
                        data: this.form_list,
                        placeholder: '- กรุณาเลือก -',
                        allowClear: true,
                    })
                    $("#department" + index).select2({
                        data: this.form_list,
                        placeholder: '- กรุณาเลือก -',
                        allowClear: true,
                    })
                    $("#role" + index).select2({
                        data: this.form_list,
                        placeholder: '- กรุณาเลือก -',
                        allowClear: true,
                    })
                    // $("#in_form" + index).data('select2').$dropdown.find(':input.select2-search__field').attr(
                    //     'placeholder', 'ค้นหา...')
                    // $("#condition" + index).data('select2').$dropdown.find(':input.select2-search__field').attr(
                    //     'placeholder', 'ค้นหา...')
                    // $("#department" + index).data('select2').$dropdown.find(':input.select2-search__field')
                    //     .attr('placeholder', 'ค้นหา...')
                    // $("#role" + index).data('select2').$dropdown.find(':input.select2-search__field').attr(
                    //     'placeholder', 'ค้นหา...')
                },

                remove(k) {
                    // console.log(this.inputs);
                    this.del_input_id.push(this.inputs[k].id);
                    this.inputs.splice(k, 1)
                    this.inputs.forEach(function(item, index) {
                        console.log(item, index)
                        $("#in_form" + index).val(item.in_form).trigger('change')
                        $("#condition" + index).val(item.condition).trigger('change')
                        $("#department" + index).val(item.department).trigger('change')
                        $("#section" + index).val(item.section).trigger('change')
                    });

                },
            }
        })

        function openModal() {
            $("#modal-confirm").modal("show");
        }
    </script>
@endpush
