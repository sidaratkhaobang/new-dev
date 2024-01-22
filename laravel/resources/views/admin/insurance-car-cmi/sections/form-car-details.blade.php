<div id="car-accessory" class="block {{ __('block.styles') }}">
    @section('block_options_accessory')
        <div class="block-options">
            <div class="block-options-item">

                <button type="button" class="btn btn-primary"  @click="fetchCarAccessoryData('{{$type}}')" data-bs-toggle="modal" data-bs-target="#modal-accessory">
                    <i class="icon-menu-tools">
                    </i>
                    ข้อมูลอุปกรณ์เสริม
                </button>
            </div>
        </div>
    @endsection
    @include('admin.components.block-header',[
 'text' =>   __('cmi_cars.car_detail')     ,
'block_icon_class' => 'icon-document',
'block_option_id' => '_accessory',
])
    <div class="block-content">
        <div class="car-detail-wrapper">
            <div class="row items-push">
                <div class="col-lg-4 my-4 car-add-border">
                    @include('admin.cmi-components.car-section', [
                        'car' => $car,
                    ])
                </div>
                <div class="col-lg-8">
                    @include('admin.insurance-car-cmi.sections.form-car-leasing-info')
                </div>
            </div>
        </div>
    </div>
        @include('admin.insurance-car-component.modals.modal-car-accessory')
</div>
