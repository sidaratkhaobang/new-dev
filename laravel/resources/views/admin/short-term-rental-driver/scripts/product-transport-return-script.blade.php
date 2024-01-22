@push('scripts')
    <script>
        let addProductTransportReturnVue = new Vue({
            el: '#product-transport-returns',
            data: {
                typeInData: null,
                product_transport_modal_return_list: [],
                product_transport_return_list: @if (isset($product_transport_return_list))
                    @json($product_transport_return_list)
                    @else
                []
                @endif ,
                edit_index: null,
                mode: null,
                pending_delete_driver_ids: [],
                selected_type: null,
                transfer_type: 1,
                car_data: @if(isset($cars)) @json($cars) @else [] @endif,
            },
            methods: {
                addDataTable() {
                    let data = [{
                        id: null,
                        index: null,
                        brand_name: null,
                        class_name: null,
                        license_plate: null,
                        color_name: null,
                        chassis: null,
                        engine: null,
                        product_files_return: null,
                        pending_delete_product_files_return: [],
                        width_m: null,
                        long_m: null,
                        height_m: null,
                        weight_m: null,
                        product_type: null,
                        type: this.selected_type,
                    }]
                    if (this.selected_type) {
                        this.product_transport_modal_return_list.push(data)
                    }
                },
                setInData() {
                    this.typeInData = null
                },
                removeDataTable(index, index_in_list) {
                    if (index != null && index != 'undefined') {
                        this.product_transport_modal_return_list.splice(index, 1);
                    }
                    if (index_in_list != null && index_in_list != 'undefined') {
                        this.product_transport_return_list.splice(index_in_list, 1);
                    }

                    if (this.product_transport_return_list.length == 0) {
                        this.setInData()
                    }

                },
                clearDataTable() {
                    this.product_transport_modal_return_list = []
                },
                display: function () {
                    $("#product-transport-returns").show();
                },
                addProductReturn: function () {
                    this.selected_type = null
                    this.product_transport_modal_return_list = []
                    this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    this.mode = 'add';
                    this.openModal();
                },
                edit: function (index) {
                    this.setIndex(index);
                    this.loadModalData(index);
                    this.mode = 'edit';
                    $("#product-transport-return-modal-label").html('แก้ไขข้อมูล');
                    this.openModal();
                },
                clearModalData: function () {
                },
                loadModalData: function (index) {
                    this.product_transport_return_list[index].index = index;
                    this.product_transport_modal_return_list = [this.product_transport_return_list[index]];
                    this.selected_type = this.product_transport_return_list[index].product_type
                },
                openModal: function () {
                    let type = this.checkTransportSendData()
                    if (!type) {
                        mySwal.fire({
                            title: "กรุณาเพิ่มสินค้านำส่ง",
                            // text: response,
                            icon: 'warning',
                            confirmButtonText: "{{ __('lang.ok') }}",
                        })
                        return false
                    }
                    this.selected_type = type;
                    $("#product-transport-return-modal").modal("show");
                    this.propDisabled('input[name="transport_return"]', true)
                    this.propChecked('input[name="transport_return"]', false)
                    this.propChecked(`#transport_return_${this.selected_type}`, true)

                },
                hideModal: function () {
                    this.clearDataTable()
                    $("#product-transport-return-modal").modal("hide");
                },
                checkTransportSendData: function () {
                    if (addProductTransportVue.product_transport_list.length === 0) {
                        return null;
                    }
                    return addProductTransportVue.product_transport_list[0].product_type
                },
                save: function () {
                    var _this = this;
                    if (_this.mode == 'edit') {
                        var index = _this.edit_index;
                        _this.saveEdit(index);
                    } else {
                        _this.saveAdd();
                    }
                },
                getDataFromModalAdd: function () {
                    let dataModalLength = this.product_transport_modal_return_list.length
                    let dataArray = []
                    for (let i = 0; i < dataModalLength; i++) {
                        var brand_name_el = document.getElementById(`${i}brand_name`);
                        var class_name_el = document.getElementById(`${i}class_name`);
                        var license_plate_el = document.getElementById(`${i}license_plate`);
                        var color_name_el = document.getElementById(`${i}color_name`);
                        var chassis_el = document.getElementById(`${i}chassis`);
                        var engine_el = document.getElementById(`${i}engine`);
                        var width_m_el = document.getElementById(`${i}width_m`);
                        var long_m_el = document.getElementById(`${i}long_m`);
                        var height_m_el = document.getElementById(`${i}height_m`);
                        var weight_m_el = document.getElementById(`${i}weight_m`);
                        var product_type_el = document.getElementById(`${i}product_type`);
                        var index_el = document.getElementById(`${i}index`).value;

                        var brand_name = brand_name_el ? brand_name_el.value : null;
                        var class_name = class_name_el ? class_name_el.value : null;
                        var license_plate = license_plate_el ? license_plate_el.value : null;
                        var color_name = color_name_el ? color_name_el.value : null;
                        var chassis = chassis_el ? chassis_el.value : null;
                        var engine = engine_el ? engine_el.value : null;
                        var weight_m = weight_m_el ? weight_m_el.value : null;
                        var width_m = width_m_el ? width_m_el.value : null;
                        var height_m = height_m_el ? height_m_el.value : null;
                        var long_m = long_m_el ? long_m_el.value : null;
                        var product_type = product_type_el ? product_type_el.value : this.selected_type
                        var index = index_el ? index_el : null;
                        var myDropzone = Dropzone.forElement('#product-img-' + i + '-area');
                        var product_raw_files = myDropzone.files;
                        var product_files_return = product_raw_files.map(item => this.formatFile(item))
                        let data = {
                            id: null,
                            index: index,
                            brand_name: brand_name,
                            class_name: class_name,
                            license_plate: license_plate,
                            color_name: color_name,
                            chassis: chassis,
                            engine: engine,
                            product_files_return: product_files_return,
                            pending_delete_product_files_return: [],
                            weight_m: weight_m,
                            width_m: width_m,
                            height_m: height_m,
                            long_m: long_m,
                            product_type: product_type,
                            type: this.selected_type,
                        }
                        dataArray.push(data)
                    }
                    return dataArray
                },
                validateDataObject: function (driver) {
                    if (this.typeInData && this.typeInData != this.selected_type) {
                        warningAlert("ประเภทสินค้าไม่ตรงกับข้อมูลก่อนหน้า");
                        return false
                    } else if (this.typeInData && this.typeInData == 'car' && this.product_transport_return_list.length == 1 && this.mode != 'edit') {
                        warningAlert("รถยนต์ รองรับสูงสุด 1 คัน");
                        return false
                    } else if (this.typeInData && this.typeInData == 'broken-car' && this.product_transport_return_list.length == 1 && this.mode != 'edit') {
                        warningAlert("รถเสีย รองรับสูงสุด 1 คัน");
                        return false
                    } else if (this.typeInData && this.typeInData == 'big-bike' && this.mode != 'edit') {
                        let totalBigBike = this.product_transport_return_list.length + this.product_transport_modal_return_list.length;
                        if (totalBigBike > 5) {
                            warningAlert("บิ๊กไบค์ รองรับสูงสุด 5 คัน");
                            return false
                        } else {
                            return true
                        }
                    } else {
                        return true
                    }
                },
                saveAdd: function () {
                    if (this.validateDataObject() == true) {
                        var driver = this.getDataFromModalAdd();
                        if (driver) {
                            driver.forEach(function (value) {
                                addProductTransportReturnVue.product_transport_return_list.push(value);
                            })
                            this.typeInData = this.selected_type;
                            this.edit_index = null;
                            this.display();
                            this.hideModal();
                        }
                    }
                },
                saveEdit: function (index) {
                    if (this.validateDataObject() == true) {
                        var driver = this.getDataFromModalAdd();
                        if (driver) {
                            driver.forEach(function (value, k) {
                                console.log(value, k)
                                var index = parseInt(value.index)
                                if (addProductTransportReturnVue.product_transport_return_list[index]) {
                                    let dataOld = addProductTransportReturnVue.product_transport_return_list[index];
                                    var driver_return = addProductTransportReturnVue.product_transport_return_list[index];
                                    driver_return.brand_name = value.brand_name
                                    driver_return.chassis = value.chassis
                                    driver_return.color_name = value.color_name
                                    driver_return.engine = value.engine
                                    driver_return.height_m = value.height_m
                                    driver_return.id = dataOld.id
                                    driver_return.index = value.index
                                    driver_return.license_plate = value.license_plate
                                    driver_return.long_m = value.long_m
                                    var myDropzone = Dropzone.forElement('#product-img-' + k + '-area');
                                    var deleted_product_files_return = myDropzone.options.params.pending_delete_ids;
                                    driver_return.pending_delete_product_files_return = deleted_product_files_return;
                                    driver_return.product_files_return = value.product_files_return
                                    driver_return.product_type = value.product_type
                                    driver_return.type = value.type
                                    driver_return.weight_m = value.weight_m
                                    driver_return.width_m = value.width_m
                                    addProductTransportReturnVue.$set(addProductTransportReturnVue.product_transport_return_list, index, driver_return)
                                } else {
                                    addProductTransportReturnVue.product_transport_return_list.push(value);
                                }
                            })
                            this.edit_index = null;
                            this.display();
                            this.hideModal();
                        }
                    }
                },
                remove: function (index) {
                    if (this.product_transport_return_list[index].id) {
                        this.pending_delete_driver_ids.push(this.product_transport_return_list[index].id);
                    }
                    this.product_transport_return_list.splice(index, 1);
                    if (this.product_transport_return_list.length == 0) {
                        this.setInData()
                    }
                },
                setIndex: function (index) {
                    this.edit_index = index;
                },
                getIndex: function () {
                    return this.edit_index;
                },
                setLastIndex: function () {
                    return this.product_transport_return_list.length;
                },
                formatFile: function (file) {
                    if (file.formated) {
                        return file;
                    }
                    return {
                        media_id: null,
                        url: file.dataURL,
                        url_thumb: file.dataURL,
                        file_name: file.name,
                        name: file.name,
                        size: file.size,
                        raw_file: file,
                        saved: false,
                        formated: true
                    }
                },
                getFiles: function () {
                    return this.product_transport_return_list.map(function (driver, index) {
                        return {
                            driver: driver,
                            product_files_return: driver.product_files_return,
                            index: index
                        }
                    });
                },
                getPendingDeleteMediaIds: function () {
                    return this.product_transport_return_list.map(function (driver, index) {
                        console.log(driver
                            .pending_delete_product_files_return)
                        return {
                            driver: driver,
                            pending_delete_product_files_return: driver
                                .pending_delete_product_files_return,
                            index: index
                        }
                    });
                },
                getFilesPendingCount: function (files) {
                    return (files ? files.filter((file) => {
                        return (!file.saved)
                    }).length : '---');
                },
                propChecked: function (element, type) {
                    $(element).prop('checked', type)
                },
                propDisabled: function (element, type) {
                    $(element).prop('disabled', type)
                }
            },
            props: ['title'],
        });
        addProductTransportReturnVue.display();
        window.addProductTransportReturnVue = addProductTransportReturnVue;

        function addProductReturn() {
            addProductTransportReturnVue.addProductReturn();
        }

        function saveProductReturn() {
            addProductTransportReturnVue.save();
        }
    </script>
@endpush
