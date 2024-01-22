<tr>
    <td>{{ intval($seq) }}</td>
    <td class="department_name" >
        {{ $department_name }}
    </td>
    <td class="text-center" >
        @if($is_all_department)
        <i class="far fa-circle-check" aria-hidden="true" style="color: green; "></i>
        @endif
    </td>
    <td class="section_name" >
        {{ $section_name }}
    </td>
    <td class="text-center" >
        @if($is_all_section)
        <i class="far fa-circle-check" aria-hidden="true" style="color: green; "></i>
        @endif
    </td>
    <td class="role_name" >
        {{ $role_name }}
    </td>
    <td class="text-center" >
        @if($is_all_role)
        <i class="far fa-circle-check" aria-hidden="true" style="color: green; "></i>
        @endif
    </td>
    <td class="user_name" >
        {{ $user_name }}
    </td>
    <td class="text-center" >
        @if($is_super_user)
        <i class="far fa-circle-check" aria-hidden="true" style="color: green; "></i>
        @endif
    </td>
    @if(!isset($view))
    <td>
        <div class="dropdown dropleft">
            <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-ellipsis-vertical"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark" style="">
                <a class="dropdown-item btn-row-edit" href="javascript:;"><i class="far fa-edit me-1"></i>
                    แก้ไข
                </a>
                <a class="dropdown-item btn-row-delete" href="javascript:;"><i class="fa-solid fa-trash-can me-1"></i>
                    ลบ
                </a>
            </div>
        </div>
    </td>
    @endif

    <input type="hidden" name="line_id[]" value="{{ $line_id }}" >
    <input type="hidden" name="seq[]" value="{{ intval($seq) }}" >

    <input type="hidden" name="department_id[]" value="{{ $department_id }}" >
    <input type="hidden" name="section_id[]" value="{{ $section_id }}" >
    <input type="hidden" name="role_id[]" value="{{ $role_id }}" >
    <input type="hidden" name="user_id[]" value="{{ $user_id }}" >

    <input type="hidden" name="is_all_department[]" value="{{ intval($is_all_department) }}" >
    <input type="hidden" name="is_all_section[]" value="{{ intval($is_all_section) }}" >
    <input type="hidden" name="is_all_role[]" value="{{ intval($is_all_role) }}" >
    <input type="hidden" name="is_super_user[]" value="{{ intval($is_super_user) }}" >
</tr>