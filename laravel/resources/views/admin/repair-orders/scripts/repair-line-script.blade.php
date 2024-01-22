@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script type="text/javascript" src="https://momentjs.com/downloads/moment-with-locales.min.js"></script>
    <script>
        let getRepairLineVue = new Vue({
            el: '#repair-line',
            data: {
                repair_line: @if (isset($repair_line))
                    @json($repair_line)
                @else
                    []
                @endif ,
                edit_index: null,
                mode: null,
            },
            methods: {
                display: function() {
                    $("#repair-line").show();
                },
                showByDefault: function(e) {
                    var _this = this;
                    var check_line = {};
                    if (e.id) {
                        check_line.id = e.id;
                        check_line.check = e.check;
                        check_line.check_text = e.check_text;
                        check_line.description = e.description;
                        check_line.qc = e.qc;
                        check_line.date = e.date;

                        _this.repair_line.push(check_line);
                        $("#repair-line").show();
                    }
                },
                removeAll: function() {
                    this.repair_line = [];
                },
                formatDate(x) {
                    if (x) {
                        return moment(x).format('DD/MM/YYYY');
                    }
                },
            },
            props: ['title'],
        });
        getRepairLineVue.display();

        function showRepairLineDefault(e) {
            getRepairLineVue.showByDefault(e);
        }

        function removeAll() {
            getRepairLineVue.removeAll();
        }
    </script>
@endpush
