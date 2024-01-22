<div class="row mb-4">
    <div class="col-sm-3">
        <x-forms.select-option id="requester_type_recipient" :list="$request_type_contact_list" :value="$d->requester_type_contact" :label="__('change_registrations.requester_type_contact')"
            :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3 customer_recipient">
        <x-forms.input-new-line id="name_recipient" :value="$d->name_contact" :label="__('change_registrations.name_contact')" :optionals="['required' => true]"/>
    </div>
    <div class="col-sm-3 tls_recipient">
        <x-forms.select-option id="recipient_user_id" :list="[]" :value="$d->recipient_user_id" :optionals="['required' => true, 'ajax' => true, 'default_option_label' => $user_name_recipient]"
            :label="__('change_registrations.tls_name')" />
    </div>
    <div class="col-sm-3 tls_recipient">
        <x-forms.input-new-line id="department_tls_recipient" :value="$d->tel_recipient" :optionals="['input_class' => 'department_tls', 'oninput' => true, 'maxlength' => 10]" :label="__('change_registrations.department_tls')" />
    </div>
    <div class="col-sm-3 tls_recipient">
        <x-forms.input-new-line id="role_tls_recipient" :value="$d->role_tls" :optionals="['input_class' => 'role_tls']" :label="__('change_registrations.role_tls')" />
    </div>

    <div class="col-sm-3 customer_recipient">
        <x-forms.input-new-line id="tel_recipient" :value="$d->tel_recipient" :label="__('change_registrations.tel_contact')" :optionals="['required' => true, 'oninput' => true, 'maxlength' => 10]"/>
    </div>
    <div class="col-sm-3 customer_recipient">
        <x-forms.input-new-line id="email_recipient" :value="$d->email_recipient" :label="__('change_registrations.email_contact')" :optionals="['required' => true]"/>
    </div>

</div>
<div class="row mb-4 tls_recipient">
    <div class="col-sm-3">
        <x-forms.input-new-line id="tel_recipient_tls" :value="$d->tel_recipient" :label="__('change_registrations.tel_contact')" :optionals="['required' => true, 'oninput' => true, 'maxlength' => 10]"/>
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="email_recipient_tls" :value="$d->email_contact" :label="__('change_registrations.email_contact')" :optionals="['required' => true]"/>
    </div>
</div>
<div class="row mb-4 customer_recipient">
    <div class="col-sm-12">
        <x-forms.text-area-new-line id="address_recipient" :value="$d->address_recipient" :label="__('change_registrations.address_contact')" :optionals="['placeholder' => __('lang.input.placeholder'), 'row' => 2,'required' => true]" />

    </div>
</div>

@push('scripts')
    <script>
      function getUserDetailRecipient(UserId) {
            axios.get("{{ route('admin.util.select2-change-registration.get-user-detail') }}", {
                params: {
                    user_id: UserId,
                }
            }).then(response => {
                if (response.data.success) {
                    if (response.data.data) {
                        $("#department_tls_recipient").val(response.data.data.department);
                        $("#role_tls_recipient").val(response.data.data.role);
                    } else {
                        $("#department_tls_recipient").val();
                        $("#role_tls_recipient").val();
                    }
                }
            });
        }

        $("#recipient_user_id").on('change', function() {
            var userId = $(this).val();
            getUserDetailRecipient(userId);
        });

        $(document).ready(function() {
            var userId = $("#recipient_user_id").val();
            if (userId) {
                getUserDetailRecipient(userId);
            }
        });

        $("#requester_type_recipient").on('change', function() {
            clearRecipient();
            var type = $(this).val();
            if (type == '{{ ChangeRegistrationRequestTypeContactEnum::CUSTOMER }}') {
                $('.customer_recipient').show();
                $('.tls_recipient').hide();
            } else if (type == '{{ ChangeRegistrationRequestTypeContactEnum::TLS }}') {
                $('.tls_recipient').show();
                $('.customer_recipient').hide();
            } else {
                $('.customer_recipient').hide();
                $('.tls_recipient').hide();
            }
        });

        $(document).ready(function() {
            $('.tls_recipient').hide();
            var type = $("#requester_type_recipient").val();
            if (type == '{{ ChangeRegistrationRequestTypeContactEnum::CUSTOMER }}') {
                $('.customer_recipient').show();
                $('.tls_recipient').hide();
            } else if (type == '{{ ChangeRegistrationRequestTypeContactEnum::TLS }}') {
                $('.tls_recipient').show();
                $('.customer_recipient').hide();
            } else {
                $('.customer_recipient').hide();
                $('.tltls_recipients').hide();
            }
        });
    </script>
@endpush

