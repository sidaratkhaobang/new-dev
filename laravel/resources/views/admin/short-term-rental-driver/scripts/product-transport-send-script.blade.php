@push('scripts')
    <script>
        function getProductTransportData() {
            axios.post("{{ route('admin.short-term-rental.info.product-data') }}", params).then(response => {
                if (response.data.success) {
                    $('#carousel-products').carousel('dispose');
                    document.querySelector(".carousel-inner").innerHTML = response.data.html;
                    $('#carousel-products').carousel({
                        interval: 0
                    });
                    var product_id_selected = $('#product_id_selected').val();
                    if (product_id_selected != "") {
                        $("input[name=product_id][value='" + product_id_selected + "']").prop("checked", true);
                    }
                }
            });
        }

        let addProductTransportVue = new Vue({
            el: '#product-transports',
            data: {
                typeInData: null,
                product_transport_modal_list: [],
                product_transport_list: @if (isset($product_transport_list))
                    @json($product_transport_list)
                    @else
                []
                @endif ,
                edit_index: null,
                mode: null,
                pending_delete_driver_ids: [],
                selected_type: null,
                transfer_type: 2,
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
                        product_files: null,
                        pending_delete_product_files: [],
                        width_m: null,
                        long_m: null,
                        height_m: null,
                        weight_m: null,
                        product_type: null,
                        type: this.selected_type,
                    }]
                    if (this.selected_type) {
                        this.product_transport_modal_list.push(data)
                    }
                },
                setInData() {
                    this.typeInData = null
                },
                removeDataTable(index, index_in_list) {
                    if (index != null && index != 'undefined') {
                        this.product_transport_modal_list.splice(index, 1);
                    }
                    if (index_in_list != null && index_in_list != 'undefined') {
                        this.product_transport_list.splice(index_in_list, 1);
                    }

                    if (this.product_transport_list.length == 0) {
                        this.setInData()
                    }

                },
                clearDataTable() {
                    this.product_transport_modal_list = []
                },
                display: function () {
                    // console.log(this.product_transport_list.length);
                    $("#product-transports").show();
                },
                addProduct: function () {
                    this.selected_type = null
                    this.product_transport_modal_list = []
                    this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    this.mode = 'add';
                    this.openModal();
                },
                edit: function (index) {
                    this.setIndex(index);
                    this.loadModalData(index);
                    this.mode = 'edit';
                    $("#product-transport-modal-label").html('แก้ไขข้อมูล');
                    this.openModal();
                },
                clearModalData: function () {
                },
                loadModalData: function (index) {
                    this.product_transport_list[index].index = index;
                    this.product_transport_modal_list = [this.product_transport_list[index]];
                    this.selected_type = this.product_transport_list[index].product_type
                },
                openModal: function () {
                    $("#product-transport-modal").modal("show");
                    this.propDisabled('input[name="transport"]', false)
                    this.propChecked('input[name="transport"]', false)
                    if (this.mode == 'edit') {
                        this.propDisabled('input[name="transport"]', true)
                        this.propChecked('input[name="transport"]', false)
                        this.propChecked(`#transport_send_${this.selected_type}`, true)
                    }
                },
                hideModal: function () {
                    this.clearDataTable()
                    $("#product-transport-modal").modal("hide");
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

                    let dataModalLength = this.product_transport_modal_list.length
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
                        var product_files = product_raw_files.map(item => this.formatFile(item))
                        let data = {
                            id: null,
                            index: index,
                            brand_name: brand_name,
                            class_name: class_name,
                            license_plate: license_plate,
                            color_name: color_name,
                            chassis: chassis,
                            engine: engine,
                            product_files: product_files,
                            pending_delete_product_files: [],
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
                validateDataObject: function () {
                    if (this.typeInData && this.typeInData != this.selected_type) {
                        warningAlert("ประเภทสินค้าไม่ตรงกับข้อมูลก่อนหน้า");
                        return false
                    } else if (this.typeInData && this.typeInData == 'car' && this.product_transport_list.length == 1 && this.mode != 'edit') {
                        warningAlert("รถยนต์ รองรับสูงสุด 1 คัน");
                        return false
                    } else if (this.typeInData && this.typeInData == 'broken-car' && this.product_transport_list.length == 1 && this.mode != 'edit') {
                        warningAlert("รถเสีย รองรับสูงสุด 1 คัน");
                        return false
                    } else if (this.typeInData && this.typeInData == 'big-bike' && this.mode != 'edit') {
                        let totalBigBike = this.product_transport_list.length + this.product_transport_modal_list.length;
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
                                console.log(value)
                                addProductTransportVue.product_transport_list.push(value);
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
                                var index = parseInt(value.index)
                                if (addProductTransportVue.product_transport_list[index]) {
                                    let dataOld = addProductTransportVue.product_transport_list[index];
                                    var driver_return = addProductTransportVue.product_transport_list[index];
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
                                    driver_return.pending_delete_product_files = deleted_product_files_return;
                                    driver_return.product_files = value.product_files
                                    driver_return.product_type = value.product_type
                                    driver_return.type = value.type
                                    driver_return.weight_m = value.weight_m
                                    driver_return.width_m = value.width_m
                                    addProductTransportVue.$set(addProductTransportVue.product_transport_list, index, driver_return)
                                } else {
                                    addProductTransportVue.product_transport_list.push(value);
                                }
                            })

                            this.edit_index = null;
                            this.display();
                            this.hideModal();
                        }
                    }
                },
                remove: function (index) {
                    if (this.product_transport_list[index].id) {
                        this.pending_delete_driver_ids.push(this.product_transport_list[index].id);
                    }
                    this.product_transport_list.splice(index, 1);
                    if (this.product_transport_list.length == 0) {
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
                    return this.product_transport_list.length;
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
                    return this.product_transport_list.map(function (driver, index) {
                        return {
                            driver: driver,
                            product_files: driver.product_files,
                            index: index
                        }
                    });
                },
                getPendingDeleteMediaIds: function () {
                    return this.product_transport_list.map(function (driver, index) {
                        return {
                            driver: driver,
                            pending_delete_product_files: driver.pending_delete_product_files,
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
        addProductTransportVue.display();
        window.addProductTransportVue = addProductTransportVue;

        function addProduct() {
            addProductTransportVue.addProduct();
        }

        function saveProduct() {
            addProductTransportVue.save();
        }
    </script>
@endpush
