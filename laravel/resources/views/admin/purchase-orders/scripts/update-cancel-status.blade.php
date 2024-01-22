@push('scripts')
    <script>
        $(".btn-cancel-status").on("click", function () {
            var data = {
                purchase_order_status: '{{ \App\Enums\POStatusEnum::CANCEL }}',
                purchase_order_id: document.getElementById("id").value,
                redirect_route: '{{ route("admin.purchase-orders.index") }}',
            };
             console.log(document.getElementById("id").value);
            mySwal.fire({
                title: "{{ __('purchase_orders.cancel_confirm') }}",
                html: 'กรุณากรอกเหตุผล ยกเลิกใบสั่งซื้อในครั้งนี้ <span class="text-danger">*</span>',
                input: 'text',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-danger m-1',
                    cancelButton: 'btn btn-secondary m-1'
                },
                showCancelButton: true,
                confirmButtonText: "{{ __('lang.confirm') }}",
                cancelButtonText: "{{ __('lang.cancel') }}",
                preConfirm: e => {
                    return new Promise(resolve => {
                        setTimeout(() => {
                            resolve();
                        }, 50);
                    });
                }
                
            }).then((result) => {
                if (result.value) {
                    var reason = result.value;
                    data.reject_reason = reason;
                    updatePurchaseOrderStatus(data);
                } else {
                    if(typeof result.value !== 'undefined'){
                    warningAlert("{{ __('lang.required_field_inform') }}")
                    }
                }
            })
        });
    </script>
@endpush
