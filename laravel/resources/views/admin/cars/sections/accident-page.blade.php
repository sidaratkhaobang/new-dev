<section class="section-page" id="page-5" style="display: none">
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('cars.accident_history'),
            'is_toggle' => true,
        ])
        <div class="block-content pt-0">
            @include('admin.cars.sections.tables.accident-table')
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('cars.repair_history'),
            'is_toggle' => true,
        ])
        <div class="block-content pt-0">
            @include('admin.cars.sections.tables.repair-table')
        </div>
    </div>
</section>