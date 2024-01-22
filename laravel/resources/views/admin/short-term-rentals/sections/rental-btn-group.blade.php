<div class="row items-push mb-4">
    <div class="col-sm-6">
        <div class="btn-group" role="group" aria-label="Horizontal Outline Primary">
            @if (!isset($view))
                <a type="button" href="{{ route('admin.short-term-rental.alter.edit', ['rental_id' => $rental_id]) }}"
                    class="btn btn-outline-primary {{ strcmp($page, RentalStateEnum::INFO_EDIT) === 0 ? 'active' : '' }}">รายละเอียดการจอง</a>
                <a type="button"
                    href="{{ route('admin.short-term-rental.alter.edit-driver', ['rental_id' => $rental_id]) }}"
                    class="btn btn-outline-primary {{ strcmp($page, RentalStateEnum::ASSET_EDIT) === 0 ? 'active' : '' }}">รายละเอียดแพ็กเกจ</a>
                <a type="button"
                    href="{{ route('admin.short-term-rental.alter.edit-bill', ['rental_id' => $rental_id]) }}"
                    class="btn btn-outline-primary {{ strcmp($page, RentalStateEnum::SUMMARY_EDIT) === 0 ? 'active' : '' }}">สรุปข้อมูล</a>
            @else
                <a type="button" href="{{ route('admin.short-term-rentals.show', ['short_term_rental' => $rental_id]) }}"
                    class="btn btn-outline-primary {{ strcmp($page, RentalStateEnum::INFO_EDIT) === 0 ? 'active' : '' }}">รายละเอียดการจอง</a>
                <a type="button"
                    href="{{ route('admin.short-term-rental.alter.view-asset', ['rental_id' => $rental_id]) }}"
                    class="btn btn-outline-primary {{ strcmp($page, RentalStateEnum::ASSET_EDIT) === 0 ? 'active' : '' }}">รายละเอียดแพ็กเกจ</a>
                <a type="button"
                    href="{{ route('admin.short-term-rental.alter.view-bill', ['rental_id' => $rental_id]) }}"
                    class="btn btn-outline-primary {{ strcmp($page, RentalStateEnum::SUMMARY_EDIT) === 0 ? 'active' : '' }}">สรุปข้อมูล</a>
            @endif
        </div>
    </div>
</div>
