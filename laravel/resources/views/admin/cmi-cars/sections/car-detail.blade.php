<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
            'text' =>  __('cmi_cars.car_detail'),

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
                    @include('admin.cmi-cars.sections.leasing-info')
                </div>
            </div>
        </div>
    </div>
</div>
