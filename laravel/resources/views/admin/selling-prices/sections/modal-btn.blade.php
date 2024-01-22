@section('block_options_btn')
    <a class="btn btn-sm btn-primary" onclick="openModalAccident()" href="javascript:void(0)">
        <i class="fa fa-car-crash"></i> {{ __('repairs.accident_history') }}
    </a>
    <a class="btn btn-sm btn-primary" onclick="openModalMaintain()" href="javascript:void(0)">
        <i class="fa fa-arrow-rotate-left"></i> {{ __('repairs.maintain_history') }}
    </a>
    <a class="btn btn-sm btn-primary px-3 me-1 my-1" onclick="openAccessoryModal()" href="javascript:void(0)">
        <i class="fa fa-wrench"></i> {{ __('replacement_cars.accessory_detail') }}
    </a>
    @include('admin.selling-prices.modals.accessory')
@endsection
