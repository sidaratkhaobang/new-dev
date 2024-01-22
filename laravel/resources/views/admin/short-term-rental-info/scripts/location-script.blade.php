@push('scripts')
    <script>
        let addRentalLocationVue = new Vue({
            el: '#location',
            data: {
                location_list: @if (isset($location_list)) @json($location_list) @else [] @endif,
                edit_index: null,
                mode: null,
            },
            methods: {
                display: function() {
                    $("#location").show();
                },
                addLocation: function(){
                    this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    this.mode = 'add';
                    this.openModal();
                },
                editLocation: function(index){
                    this.setIndex(index);
                    this.loadModalData(index);
                    this.mode = 'edit';
                    $("#location-modal-label").html('แก้ไขข้อมูล');
                    this.openModal();
                },
                clearModalData: function(){
                    $("#location_field").val('').change();
                    $("#description_field").val('');
                },
                loadModalData: function(index){
                    var temp = null;
                    temp = this.location_list[index];
                    $("#location_field").val(temp.location_id).change();
                    $("#description_field").val(temp.location_description);
                    var defaultLocationOption = {
                            id: temp.location_id,
                            text: temp.location_text,
                    };
                    var tempLocationOption = new Option(defaultLocationOption.text, defaultLocationOption.id, false, false);
                    $("#location_field").append(tempLocationOption).trigger('change');
                },
                openModal: function(){
                    $("#modal-location").modal("show");
                },
                hideModal: function(){
                    $("#modal-location").modal("hide");
                },
                saveLocation: function() {
                    var _this = this;
                    var location = _this.getCarDataFromModal();
                    if (_this.validateCarObject(location)) {
                        if(_this.mode == 'edit') {
                            var index = _this.edit_index;
                            _this.saveEdit(location, index);
                        }else {
                            _this.saveAdd(location);
                        }
                        _this.edit_index = null;
                        _this.display();
                        _this.hideModal();
                    }else{
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                getCarDataFromModal: function(){
                    var location_id = document.getElementById("location_field").value;
                    var location_text = (location_id) ? document.getElementById('location_field').selectedOptions[0].text : '';
                    var location_description = document.getElementById("description_field").value;
                    return {
                        location_id: location_id,
                        location_text: location_text,
                        location_description: location_description,
                    };
                },
                validateCarObject: function(location){
                    if (location.location_id) {
                        return true;
                    } else {
                        return false;
                    }
                },
                saveAdd: function(location){
                    this.location_list.push(location);
                },
                saveEdit: function(location, index) {
                    addRentalLocationVue.$set(this.location_list, index, location);
                },
                removeLocation: function(index) {
                    this.location_list.splice(index, 1);
                },
                setIndex: function(index) {
                    this.edit_index = index;
                },
                getIndex: function(){
                    return this.edit_index;
                },
                setLastIndex: function(){
                   return this.location_list.length;
                },
            },
            props: ['title'],
        });
        addRentalLocationVue.display();

        function addLocation(){
            addRentalLocationVue.addLocation();
        }

        function saveLocation() {
            addRentalLocationVue.saveLocation();
        }
    </script>
@endpush
