<div class="row mb-4">
    <div class="col-sm-3">
        <x-forms.select-option id="requester_type_contact" :list="$request_type_contact_list" :value="$d->requester_type_contact" :label="__('change_registrations.requester_type_contact')"
            :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3 customer">
        <x-forms.input-new-line id="name_contact" :value="$d->name_contact" :label="__('change_registrations.name_contact')" :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3 tls">
        <x-forms.select-option id="contact_user_id" :list="[]" :value="$d->contact_user_id" :optionals="['required' => true, 'ajax' => true, 'default_option_label' => $user_name_contact]"
            :label="__('change_registrations.tls_name')" />
    </div>
    <div class="col-sm-3 tls">
        <x-forms.input-new-line id="department_tls_contact" :value="$d->department_tls" :optionals="['input_class' => 'department_tls']" :label="__('change_registrations.department_tls')" />
    </div>
    <div class="col-sm-3 tls">
        <x-forms.input-new-line id="role_tls_contact" :value="$d->role_tls" :optionals="['input_class' => 'role_tls']" :label="__('change_registrations.role_tls')" />
    </div>

    <div class="col-sm-3 customer">
        <x-forms.input-new-line id="tel_contact" :value="$d->tel_contact" :label="__('change_registrations.tel_contact')" :optionals="['required' => true, 'oninput' => true, 'maxlength' => 10]"/>
    </div>
    <div class="col-sm-3 customer">
        <x-forms.input-new-line id="email_contact" :value="$d->email_contact" :label="__('change_registrations.email_contact')" :optionals="['required' => true]"/>
    </div>

</div>
<div class="row mb-4 tls">
    <div class="col-sm-3">
        <x-forms.input-new-line id="tel_contact_tls" :value="$d->tel_contact" :label="__('change_registrations.tel_contact')" :optionals="['required' => true, 'oninput' => true, 'maxlength' => 10]"/>
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="email_contact_tls" :value="$d->email_contact" :label="__('change_registrations.email_contact')" :optionals="['required' => true]"/>
    </div>
</div>
<div class="row mb-4 customer">
    <div class="col-sm-12">
        <x-forms.text-area-new-line id="address_contact" :value="$d->address_contact" :label="__('change_registrations.address_contact')" :optionals="['placeholder' => __('lang.input.placeholder'), 'row' => 2,'required' => true]" />

    </div>
</div>

@push('scripts')
    <script>

        function getUserDetail(UserId) {
            axios.get("{{ route('admin.util.select2-change-registration.get-user-detail') }}", {
                params: {
                    user_id: UserId,
                }
            }).then(response => {
                console.log(response.data)
                if (response.data.success) {
                    console.log(response.data.data.department)
                    if (response.data.data) {
                        $("#department_tls_contact").val(response.data.data.department);
                        $("#role_tls_contact").val(response.data.data.role);
                    } else {
                        $("#department_tls_contact").val();
                        $("#role_tls_contact").val();
                    }
                }
            });
        }

        $("#contact_user_id").on('change', function() {
            var userId = $(this).val();
            getUserDetail(userId);
        });

        $(document).ready(function() {
            var userId = $("#contact_user_id").val();
            if (userId) {
                getUserDetail(userId);
            }
        });


        $("#requester_type_contact").on('change', function() {
            clearContact();
            var type = $(this).val();
            if (type == '{{ ChangeRegistrationRequestTypeContactEnum::CUSTOMER }}') {
                $('.customer').show();
                $('.tls').hide();
            } else if (type == '{{ ChangeRegistrationRequestTypeContactEnum::TLS }}') {
                $('.tls').show();
                $('.customer').hide();
            } else {
                $('.customer').hide();
                $('.tls').hide();
            }
        });

        $(document).ready(function() {
            $('.tls').hide();
            var type = $("#requester_type_contact").val();
            if (type == '{{ ChangeRegistrationRequestTypeContactEnum::CUSTOMER }}') {
                $('.customer').show();
                $('.tls').hide();
            } else if (type == '{{ ChangeRegistrationRequestTypeContactEnum::TLS }}') {
                $('.tls').show();
                $('.customer').hide();
            } else {
                $('.customer').hide();
                $('.tls').hide();
            }
        });
    </script>
@endpush
