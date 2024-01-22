<section class="section-page" id="page-2" style="display: none">
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('เอกสารสัญญาเช่า'),
            'is_toggle' => true,
        ])
        <div class="block-content pt-0">
            @include('admin.cars.sections.tables.insurance-table')
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('vmi_cars.page_title'),
            'is_toggle' => true,
        ])
        <div class="block-content pt-0">
            @include('admin.cars.sections.tables.vmi-table')
        </div>
    </div>
</section>