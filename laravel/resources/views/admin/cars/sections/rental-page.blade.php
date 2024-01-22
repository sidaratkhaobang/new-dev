<section class="section-page" id="page-3" style="display: none">
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('cmi_cars.page_title'),
            'is_toggle' => true,
        ])
        <div class="block-content pt-0">
            @include('admin.cars.sections.tables.rental-table')
        </div>
    </div>
</section>