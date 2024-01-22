@push('scripts')
    <script>
        function gen2c2pPaymentLink(id) {
            var id = id;
            axios.get("{{ route('admin.short-term-rentals.gen-2c2p-link') }}", {
                params: {
                    id: id
                }
            }).then(response => {
                if (response.data.success) {
                    appendLink(response.data.data);
                    copyAlert('สำเร็จ !')
                    createNotification('success', 'สำเร็จ !');
                    $(".btn-regenerate-link").hide();
                } else {
                    errorAlert('มีข้อผิดพลาด !')
                    createNotification('danger', 'มีข้อผิดพลาด !');
                }
            });
        }

        function appendLink(new_url) {
            $("#2c2p-payment-link").attr("href", new_url);
            // $("#2c2p-payment-link").text(new_url);
            $("#link").val(new_url);
        }

        function createNotification(css_class, text) {
            const toasts = document.getElementById('toasts');
            const notif = document.createElement('span');
            notif.classList.add('text-' + css_class);
            notif.innerText = text;
            toasts.appendChild(notif);

            setTimeout(() => {
                notif.remove();
            }, 1500);
        }
    </script>
@endpush
