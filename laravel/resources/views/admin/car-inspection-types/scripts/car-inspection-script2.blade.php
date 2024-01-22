@push('scripts')
    <script>
        function add3() {
            carVue3.add();
            carVue3.selectTwo();
        }

        function addSub3() {
            carVue3.addSub();
        }

        function remove3() {
            carVue3.remove();
        }

        function removeList3(k) {
            carVue3.removeList(k);
        }


        let carVue3 = new Vue({
            el: '#app3',
            data() {
                return {
                    form_list: @if (isset($form_list))
                        []
                    @else
                        []
                    @endif ,
                    listCondition: @if (isset($listCondition))
                        @json($listCondition)
                    @else
                        []
                    @endif ,
                    listForm: @if (isset($form_list))
                        @json($form_list)
                    @else
                        []
                    @endif ,
                    flow_type: @if (isset($d))
                        @json($d->id)
                    @else
                        []
                    @endif ,
                    inputs2: @if (isset($question_list2))

                        @json($question_list2)
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
                            transfer_type: 1,
                        }]
                    @endif ,
                    del_input_id: [],

                }
            },
            mounted: function() {
                // $('#in_form_in0').select2({
                //     data: this.form_list,
                //     placeholder: '- กรุณาเลือก -',
                // })
                // $('#department_in0').select2({
                //     data: this.form_list,
                //     placeholder: '- กรุณาเลือก -',
                // })
                // $('#condition_in0').select2({
                //     data: this.form_list,
                //     placeholder: '- กรุณาเลือก -'
                // })
                // $('.list_in').select2({
                //     data: this.form_list,
                //     placeholder: '- กรุณาเลือก -'
                // })
                // $('#section_in0').select2({
                //     data: this.form_list,
                //     placeholder: '- กรุณาเลือก -'
                // })
                // $('.list').one('select2:open', function(e) {
                //     $('input.select2-search__field').prop('placeholder', 'ค้นหา...');
                // });
            },

            methods: {
                add() {

                    this.inputs2.push({
                        seq: '',
                        condition: null,
                        department: null,
                        role: null,
                        in_form: '',
                        photo: false,
                        inspector_signature: false,
                        send_mobile: false,
                        dpf_oil: false,
                        transfer_type: 1,

                    })

                },

                async selectTwo() {
                    await this.$nextTick()
                    var index = this.inputs2.length;
                    index = index - 1;
                    $("#in_form_in" + index).select2({
                        data: this.form_list,
                        placeholder: '- กรุณาเลือก -',
                        allowClear: true,
                    })
                    $("#condition_in" + index).select2({
                        data: this.form_list,
                        placeholder: '- กรุณาเลือก -',
                        allowClear: true,
                    })
                    $("#department_in" + index).select2({
                        data: this.form_list,
                        placeholder: '- กรุณาเลือก -',
                        allowClear: true,
                    })
                    $("#section_in" + index).select2({
                        data: this.form_list,
                        placeholder: '- กรุณาเลือก -',
                        allowClear: true,
                    })
                    // $("#in_form_in" + index).data('select2').$dropdown.find(':input.select2-search__field')
                    //     .attr('placeholder', 'ค้นหา...')
                    // $("#condition_in" + index).data('select2').$dropdown.find(':input.select2-search__field')
                    //     .attr('placeholder', 'ค้นหา...')
                    // $("#department_in" + index).data('select2').$dropdown.find(':input.select2-search__field')
                    //     .attr('placeholder', 'ค้นหา...')
                    // $("#role_in" + index).data('select2').$dropdown.find(':input.select2-search__field').attr(
                    //     'placeholder', 'ค้นหา...')
                },

                remove2(k) {
                    this.del_input_id.push(this.inputs2[k].id);
                    this.inputs2.splice(k, 1)
                    this.inputs2.forEach(function(item, index) {
                        console.log(item, index)
                        $("#in_form_in" + index).val(item.in_form).trigger('change')
                        $("#condition_in" + index).val(item.condition).trigger('change')
                        $("#department_in" + index).val(item.department).trigger('change')
                        $("#section_in" + index).val(item.role).trigger('change')
                    });
                },
            }
        })

        function openModal() {
            $("#modal-confirm").modal("show");
        }
    </script>
@endpush
