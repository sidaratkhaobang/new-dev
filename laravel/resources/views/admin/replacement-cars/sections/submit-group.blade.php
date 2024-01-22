<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <x-forms.submit-group :optionals="[
            'url' => 'admin.replacement-cars.index', 
            'view' => ($mode == MODE_VIEW) ? true : null,
            'manage_permission' => Actions::Manage . '_' . Resources::ReplacementCar
        ]" />
    </div>
</div>