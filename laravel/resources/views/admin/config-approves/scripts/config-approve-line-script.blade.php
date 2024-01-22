@push('scripts')
    <script>
        var lastConfigTR = 1;
        function addConfigLine2() {
            clearForm('#modal-config');
            $('.row-user').hide();
            $('.row-department').hide();
            $('.row-section').hide();
            $('.row-role').hide();
            $('#config_type').val('add');
            $('#modal-config').modal('show');
        }

        $("input[name='is_person']").on('change', function(){
            var val = !!(parseInt($("input[name='is_person']:checked").val()));
            if(val){
                $('.row-user').show();
                $('.row-department').hide();
                $('.row-section').hide();
                $('.row-role').hide();
            } else {
                $('.row-user').hide();
                $('.row-department').show();
                $('.row-section').show();
                $('.row-role').show();
            }
        });

        $('.btn-save-config').on('click', function(){
            var formData = new FormData(document.querySelector('#form-modal-config'));
            var config_type = $('#config_type').val();
            axios.post("{{ route('admin.config-approves.add-row') }}", formData).then(response => {
                if (response.data.success) {
                    console.log(response.data.data);
                    if(config_type == 'add'){
                        $('.table-config tbody').append(response.data.html);
                    } else if(config_type == 'edit'){
                        lastConfigTR.replaceWith(response.data.html);
                    }
                    $('#modal-config').modal('hide');
                } else {
                    errorAlert(error.response.data.message);
                }
            }).catch(error => {
                errorAlert(error.response.data.message);
            });
        });

        $(document).on('click', '.btn-row-edit', function(){
            clearForm('#modal-config');
            var tr = $(this).parents('tr');
            lastConfigTR = tr;
            var seq = tr.find("input:hidden[name^=seq]").val();
            var department_id = tr.find("input:hidden[name^=department_id]").val();
            var section_id = tr.find("input:hidden[name^=section_id]").val();
            var role_id = tr.find("input:hidden[name^=role_id]").val();
            var user_id = tr.find("input:hidden[name^=user_id]").val();

            var is_all_department = tr.find("input:hidden[name^=is_all_department]").val();
            var is_all_section = tr.find("input:hidden[name^=is_all_section]").val();
            var is_all_role = tr.find("input:hidden[name^=is_all_role]").val();
            var is_super_user = tr.find("input:hidden[name^=is_super_user]").val();

            // set modal
            var md = $('#modal-config');
            md.find('#m_seq').val(seq);
            if(user_id != "" && user_id != null){
                md.find("input[name^=is_person][value=1]").prop('checked', true).trigger('change');
                var user_name = tr.find(".user_name").text();
                md.find("#m_user_id").append((new Option(user_name, user_id, true, true))).trigger('change');
                md.find("input[name^=m_is_super_user][value='"+is_super_user+"']").prop('checked', true).trigger('change');
            } else {
                md.find("input[name^=is_person][value=0]").prop('checked', true).trigger('change');
                // department
                var department_name = tr.find(".department_name").text();
                md.find("#m_department_id").append((new Option(department_name, department_id, true, true))).trigger('change');
                md.find("input[name^=m_is_all_department][value='"+is_all_department+"']").prop('checked', true).trigger('change');
                // section
                var section_name = tr.find(".section_name").text();
                md.find("#m_section_id").append((new Option(section_name, section_id, true, true))).trigger('change');
                md.find("input[name^=m_is_all_section][value='"+is_all_section+"']").prop('checked', true).trigger('change');
                // role
                var role_name = tr.find(".role_name").text();
                md.find("#m_role_id").append((new Option(role_name, role_id, true, true))).trigger('change');
                md.find("input[name^=m_is_all_role][value='"+is_all_role+"']").prop('checked', true).trigger('change');
            }
            $('#config_type').val('edit');
            $('#modal-config').modal('show');
        });

        $(document).on('click', '.btn-row-delete', function(){
            var tr = $(this).parents('tr');
            var line_id = tr.find("input:hidden[name^=line_id]").val();
            if(line_id != "" && line_id != null){
                appendHidden("#save-form", "del_ids[]", line_id);
            }
            tr.remove();
        });

        $(document).ready(() => {
            $('.row-user').hide();
            $('.row-department').hide();
            $('.row-section').hide();
            $('.row-role').hide();
        });
    </script>
@endpush
