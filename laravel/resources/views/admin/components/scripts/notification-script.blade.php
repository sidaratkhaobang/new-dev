@push('scripts')
<!-- Page JS Plugins -->
<script src="{{ asset('assets/js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

<!-- Page JS Helpers (BS Notify Plugin) -->
<script>Dashmix.helpersOnLoad(['jq-notify']);</script>
<script>
    @auth
        var user_id = "{{ get_user_id() }}";
        Echo.private(`notification_user.` + user_id)
        .notification((notification) => {
            //console.log(notification);
            /* Dashmix.helpers('jq-notify', {
                from: 'bottom', 
                message: notification.title + ' : ' + notification.description, 
                type: notification.type, 
                icon: notification.icon + ' me-4',
                timer: 5000
            }); */
            document.getElementById("my-notifications").innerHTML += notification.html;
            $("#page-header-notifications-dropdown").removeClass('btn-alt-secondary').addClass('btn-primary');
            let noti_count = $("#noti-count").html();
            noti_count = parseInt(noti_count);
            if(isNaN(noti_count)){
                noti_count = 0;
            }
            noti_count++;
            if(noti_count > 99){
                noti_count = '99+';
            }
            $("#noti-count").html(noti_count);

            let noti_message = notification.title + ' : ' + notification.description;
            notifyMe(noti_message);
        });
    @endauth

    $('input[type=radio][name=noti-radio]').change(function() {
        //console.log(this.value);
        $('#page-header-notifications-dropdown').dropdown('show');
        if (this.value == '1' || this.value == 1) {
            $('.noti-item.noti-read').css("display", "none");
        } else if (this.value == '2' || this.value == 2) {
            $('.noti-item.noti-read').css("display", "list-item");
        }
    });

    function readNotification(id){
        //console.log(id);
        axios.post("{{ route('admin.read-notification') }}", {id:id}).then(response => {
            console.log(response.data);
            if(response.data.success){
                window.location.href = response.data.redirect;
            }
        });
    }

    function notifyMe(noti_message) {
        if (!("Notification" in window)) {
            // Check if the browser supports notifications
            // alert("This browser does not support desktop notification");
        } else if (Notification.permission === "granted") {
            // Check whether notification permissions have already been granted;
            // if so, create a notification
            const notification = new Notification(noti_message);
        } else if (Notification.permission !== "denied") {
            // We need to ask the user for permission
            Notification.requestPermission().then((permission) => {
                // If the user accepts, let's create a notification
                if (permission === "granted") {
                    const notification = new Notification(noti_message);
                }
            });
        }
    }
</script>
@endpush