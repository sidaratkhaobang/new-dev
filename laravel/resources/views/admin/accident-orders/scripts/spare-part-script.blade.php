@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>

    <script>
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
                    inputs: @if (isset($spare_list))
                        @json($spare_list)
                    @else
                        []
                    @endif ,
                }
            },


            methods: {
                add() {
                    this.inputs.push({
                        supplier: '',
                        spare_part_cost: '',
                        spare_part_discount: '',
                    })
                },

                remove(k) {
                    this.inputs.splice(k, 1)
                },
                formatNumber(k) {
                    this.inputs[k].spare_parts = numeral(this.inputs[k].spare_parts).format('0,0.00');

                    var discount_spare_parts = numeral(this.inputs[k].discount_spare_parts).value();
                    var spare_parts = numeral(this.inputs[k].spare_parts).value();

                    if (!isNaN(discount_spare_parts) && !isNaN(spare_parts)) {
                        this.inputs[k].total = spare_parts - discount_spare_parts;
                        this.inputs[k].total = numeral(this.inputs[k].total).format('0,0.00');
                    }

                },
                formatNumber2(k) {
                    this.inputs[k].discount_spare_parts = numeral(this.inputs[k].discount_spare_parts).format(
                        '0,0.00');

                    var discount_spare_parts = numeral(this.inputs[k].discount_spare_parts).value();
                    var spare_parts = numeral(this.inputs[k].spare_parts).value();

                    if (!isNaN(discount_spare_parts) && !isNaN(spare_parts)) {
                        this.inputs[k].total = spare_parts - discount_spare_parts;
                        this.inputs[k].total = numeral(this.inputs[k].total).format('0,0.00');
                    }
                },
            }
        })

        function openModal() {
            $("#modal-confirm").modal("show");
        }

        function calculateSparePartTotal() {
            var wage = parseFloat($('#wage').val().replace(/,/g, '')) || 0;
            var spare_parts = parseFloat($('#spare_parts').val().replace(/,/g, '')) || 0;
            var discount_spare_parts = parseFloat($('#discount_spare_parts').val().replace(/,/g, '')) || 0;
            var spare_part_total = wage + spare_parts - discount_spare_parts;

            $('#spare_part_total').val(numeral(spare_part_total).format('0,0.00'));
        }

        $(document).ready(function() {
            calculateSparePartTotal();
        });

        $("#wage, #spare_parts, #discount_spare_parts").change(function() {
            calculateSparePartTotal();
        });
    </script>
@endpush
