@push('scripts')
    <script>
        let addAccidentListVue = new Vue({
            el: '#accident-list-vue',
            data: {
                accident_list: @if (isset($accident_list))
                    @json($accident_list)
                @else
                    []
                @endif ,
                edit_index: null,
                mode: null,
                selectedImageUrl: '',
                pending_delete_cost_ids: [],
                RepairClaimEnum: {
                    HARD_BUMP: 'HARD_BUMP',
                    SOFT_BUMP: 'SOFT_BUMP',
                    TTL: 'TTL',
                },
            },
            methods: {
                display: function() {
                    $("#cost-vue").show();
                },
                openModalImage(event) {
                    this.selectedImageUrl = event;
                    $('#imageModal').modal('show');
                },

                addAccident(data) {
                    this.accident_list = data;
                    if (data.length > 0) {
                        $('#accident-all').show();
                        $('#accident-open').show();
                    }else{
                        $('#accident-all').show();
                    }
                },
                print_text(text) {
                    // console.log(text)
                },


            },
            props: ['title'],
        });
        addAccidentListVue.display();
        window.addAccidentListVue = addAccidentListVue;
    </script>
@endpush
