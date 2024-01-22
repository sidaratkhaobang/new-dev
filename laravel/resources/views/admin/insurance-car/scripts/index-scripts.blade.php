@push('scripts')
    <script>
        window.index_insurancecar = new window.Vue({
            el: '#vue-item',
            data: {
                term_start_date: @if(isset($term_start_date)) @json($term_start_date) @else "" @endif,
                term_end_date: @if(isset($term_end_date)) @json($term_end_date) @else "" @endif,
            },
            methods: {
                getDataRenewAll(type,renew_type){
                    let url_get_car_data = `{{route('admin.insurance-car.car-data')}}`
                    let checkboxes = $('input.form-check-input-each[data-type="'+type+'"]:checked');
                    $('#type').val(type)
                    var arr = []
                    $(checkboxes).each(function(){
                        let id = $(this).data('car_id');
                        arr.push(id)
                    })
                        this.getLot()
                    axios.post(url_get_car_data, {car_id: arr,type:type,renew_type:renew_type,term_start_date:this.term_start_date,term_end_date:this.term_end_date})
                        .then(response => {
                            if (response.data.car_id) {
                                modal_renew_cmi.saveCmiRenewData(response.data.car_id);
                            } else {
                                modal_renew_cmi.saveCmiRenewData([]);
                            }
                        })
                        .catch(error => {
                            modal_renew_cmi.saveCmiRenewData([]);
                        });
                },
                getLot(){
                    let url_get_lot  = `{{route('admin.insurance-car.get-lot')}}`
                    axios.post(url_get_lot)
                        .then(response => {
                            if (response.data) {
                                $('#lot').val(response.data);
                            } else {
                                $('#lot').val('');
                            }
                        })
                        .catch(error => {
                            $('#lot').val('');
                        });
                },
                getDataCmiRenew(type,car_id,id){
                    console.log(1)
                    let url_get_car_data = `{{route('admin.insurance-car.car-data')}}`
                    let car_renew_id = $(`#${car_id}`).val()
                    let insurance_id = $('.form-check-input-each:checked[data-type="'+type+'"][data-car_id="'+id+'"]').data('id')
                   if(type == "{{InsuranceCarEnum::CMI}}"){
                       $('#insurance_cmi_id').val(insurance_id)
                   }
                    if(type == "{{InsuranceCarEnum::VMI}}"){
                        $('#insurance_vmi_id').val(insurance_id)
                    }
                    $('#type').val(type)
                    var arr = []
                    arr.push(car_renew_id)
                    this.getLot()
                    axios.post(url_get_car_data, {car_id: arr,type:type,insurance_id:insurance_id})

                        .then(response => {
                            if (response.data) {
                                modal_renew_cmi.saveCmiRenewData(response.data.car_id);
                            } else {
                                modal_renew_cmi.saveCmiRenewData([]);
                            }
                            if(response.data.year){
                                $('#modal_renew_cmi_year').val(response.data.year+'ปี')
                            }
                        })
                        .catch(error => {
                            modal_renew_cmi.saveCmiRenewData([]);
                        });
                    $('#modal-cmi-renew').modal('toggle')
                }
            },
            props: ['title'],
        });
    //     Modal Table RenewCarCMI
        window.modal_renew_cmi = new window.Vue({
            el: '#modal-renew',
            data: {
                renew_cmi_list: [],
            },
            methods: {
                saveCmiRenewData(data){
                    this.renew_cmi_list = data;
                    var jsonString = JSON.stringify(data);

                    if(JSON.stringify(data) != JSON.stringify([])){
                        $('#car_id').val(jsonString)
                    }else{
                        $('#car_id').val('')
                    }
                },
            },
            props: ['title'],
        });
    </script>
@endpush
