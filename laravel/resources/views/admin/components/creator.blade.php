@push('styles')
    <style>
        .parent-block {
            display: grid;
            grid-template-columns: 70px 100px 180px 80px 200px;
            grid-template-rows: 20px 20px;
            grid-column-gap: 0;
            grid-row-gap: 12px;
            font-size: 14px;
        }

        .parent-block .div1 {
            grid-area: 1 / 1 / 3 / 2;
            align-self: center;
            text-align: left;
        }
    </style>
@endpush
@php
    $creator_name = '';
    $creator_role = '';
    $creator_at = '';
    $creator_branch = '';
    $creator = $d->createdBy ?? null;
    if ($creator) {
        $creator_name = $creator->name;
        $creator_role = $creator->role ? $creator->role->name : '';
        $creator_at = $d->created_at ? get_thai_date_format($d->created_at, 'd/m/Y H:i') : '';
        $creator_branch = $creator->branch ? $creator->branch->name : '';
    } else {
        $user = Auth::user();
        $creator_name = $user->name;
        $creator_role = $user->role->name;
        $creator_at = get_thai_date_format(Carbon::now(), 'd/m/Y H:i');
        $creator_branch = $user->branch->name;
    }
@endphp
<div class="block {{ __('block.styles') }}" style="background-color: #F1F4F9;">
    <div class="block-content p-3">
        <div class="parent-block">
            <div class="div1">
                <img src="{{ asset('images/user/user.png') }}" alt="Profile Image" style=" width:60px; height:60px;">
            </div>
            <div class="div2">
                <span>{{ __('lang.creator.creator') }}</span>
            </div>
            <div class="div3">
                <b>{{ $creator_name }}</b>
            </div>
            <div class="div4">
                <span>{{ __('lang.creator.role') }}</span>
            </div>
            <div class="div5">
                <b>{{ $creator_role }}</b>
            </div>
            <div class="div6">
                <span>{{ __('lang.creator.created_at') }}</span>
            </div>
            <div class="div7">
                <b>{{ $creator_at }}</b>
            </div>
            <div class="div8">
                <span>{{ __('lang.creator.branch') }}</span>
            </div>
            <div class="div9">
                <b>{{ $creator_branch }}</b>
            </div>
        </div>
    </div>
</div>
