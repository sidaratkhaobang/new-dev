@extends('admin.layouts.layout')
@section('page_title',$page_title)
@section('content')
   @include('admin.insurance.sections.form-details')
   <x-forms.submit-group
       :optionals="['url' => 'admin.insurances-companies.index', 'view' => empty($view) ? null : $view,'manage_permission' => Actions::Manage . '_' . Resources::InsuranceCompanies]"/>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.insurances-companies.store'),
])

@push('scripts')
    <script>
        $status = '{{ isset($view) }}';
        $edit = '{{isset($edit)}}';
        if ($status) {
            $('#insurance_id').prop('disabled',true)
            $('#insurance_th').prop('disabled',true)
            $('#insurance_en').prop('disabled',true)
            $('#website').prop('disabled',true)
            $('#insurance_phone').prop('disabled',true)
            $('#insurance_email').prop('disabled',true)
            $('#insurance_fax').prop('disabled',true)
            $('#address').prop('disabled',true)
            $('#coordinator_name').prop('disabled',true)
            $('#coordinator_email').prop('disabled',true)
            $('#coordinator_phone').prop('disabled',true)
            $('#remark').prop('disabled',true)
            $('#status').prop('disabled',true)
        }
        if($edit){
            $('#insurance_id').prop('disabled',true)
        }
    </script>
@endpush
