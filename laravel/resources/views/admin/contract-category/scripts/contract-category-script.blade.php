@push('scripts')
    <script>
        function add() {
            carVue.add();
        }

        function addSub() {
            carVue.addSub();
        }

        function remove() {
            carVue.remove();
        }

        function removeList(k) {
            carVue.removeList(k);
        }


        let carVue = new Vue({
            el: '#app' ,

            data() {
                return {
                    inputs: @if (count($data->condition_qoutations) > 0) @json($data->condition_qoutations) @else [{
                        status: true ,
                        seq: null ,
                        name: null ,
                        id: null ,
                        condition_qoutation_checklists: []
                    }] @endif,
                    del_input_id: [] ,
                    del_input_sub_id: [] ,
                }
            } ,
            methods: {
                add() {
                    this.inputs.push({
                        status: true ,
                        seq: null ,
                        name: null ,
                        condition_qoutation_checklists: [] ,
                        id: null ,
                    })
                } ,
                addSub(k) {
                    this.inputs[k].condition_qoutation_checklists.push({
                        status: true ,
                        seq: null ,
                        name: null ,
                        id: null ,
                    })
                } ,
                hide(k) {
                    if ($("#sub-section" + k).is(":hidden")) {
                        $("hd").removeClass('hidden');
                        $('#arrow-' + k).removeClass('fa-angle-right');
                        $('#arrow-' + k).addClass('fa-angle-down');
                        $("#sub-section" + k).show();
                    }
                    else {
                        $("#sub-section" + k).hide()
                        $("hd").addClass('hidden')
                        $('#arrow-' + k).removeClass('fa-angle-down');
                        $('#arrow-' + k).addClass('fa-angle-right');
                        $("#sub-section" + k).hide()
                    }
                } ,

                remove(k) {
                    if (this.inputs[k].id != null) {
                        this.del_input_id.push(this.inputs[k].id);
                        console.log(this.del_input_id);
                    }
                    this.inputs.splice(k , 1)
                } ,
                removeList(k , k2) {
                    if (this.inputs[k].condition_qoutation_checklists[k2].id != null) {
                        this.del_input_sub_id.push(this.inputs[k].condition_qoutation_checklists[k2].id);
                    }
                    this.inputs[k].condition_qoutation_checklists.splice(k2 , 1)
                } ,
            }
        })
    </script>
@endpush
