<section class="section-page" id="page-4" style="display: none">
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('cars.equipment_history'),
            'is_toggle' => true,
        ])
        <div class="block-content pt-0">
            @include('admin.cars.sections.tables.equipment-table')
        </div>
    </div>
</section>