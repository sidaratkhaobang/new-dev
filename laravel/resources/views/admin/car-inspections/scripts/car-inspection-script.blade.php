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
            el: '#app',

            data() {
                return {
                    car_part_type_list: @if (isset($car_part_type_list)) @json($car_part_type_list) @else [] @endif,
                    inputs: @if (isset($list)) @json($list) @else [{
                        status_section: false,
                        seq: '',
                        name: '',
                        subseq: []
                    }] @endif, 
                    del_input_id: [],
                    del_input_sub_id: [],
                }
            },
            methods: {
                add() {
                    this.inputs.push({
                        status_section: false,
                        seq: '',
                        name: '',
                        subseq: [],
                        id:null,
                    })
                    
                },
                addSub(k) {
                    this.inputs[k].subseq.push({
                        status_list: false,
                        seq2: '',
                        name2: '',
                        car_part: null,
                        id:null,
                    })
                },
                hide(k) {
                    if($("#sub-section"+k).is(":hidden")){
                        $("hd").removeClass('hidden');
                        $('#arrow-' + k).removeClass('fa-angle-right');
                        $('#arrow-' + k).addClass('fa-angle-down');
                        $("#sub-section"+k).show();
                    }else{
                        $("#sub-section"+k).hide()
                        $("hd").addClass('hidden')
                        $('#arrow-' + k).removeClass('fa-angle-down');
                        $('#arrow-' + k).addClass('fa-angle-right');
                        $("#sub-section"+k).hide()
                        
                    }
                },

                remove(k) {
                    this.del_input_id.push(this.inputs[k].id);
                    console.log(this.del_input_id);
                    this.inputs.splice(k, 1)
                },
                removeList(k, k2) {
                    this.del_input_sub_id.push(this.inputs[k].subseq[k2].id);
                    console.log(this.inputs[k].subseq.length);
                    this.inputs[k].subseq.splice(k2, 1)
                },
            }
        })

        function add2() {
            carVue2.add();
        }

        function addSub2() {
            carVue2.addSub();
        }

        function remove2() {
            carVue2.remove();
        }

        function removeList2(k) {
            carVue2.removeList(k);
        }

        let carVue2 = new Vue({
            el: '#app2',
            data() {            
                return {
                    inputs: @if (isset($question_list)) @json($question_list) @else [
                    ] @endif, 
                }
            },

            methods: {
                add() {
                    this.inputs.push({
                        status_question: false,
                        seq: '',
                        name: '',
                    })
                },

                remove(k) {
                    this.inputs.splice(k, 1)
                },
            }
        })
        function openModal() {
            $("#modal-confirm").modal("show");
        }
    </script>
@endpush
