<div class="table-wrap db-scroll">
    <table class="table table-striped table-vcenter">
        <thead class="bg-body-dark">
            <tr>
                <th style="width: 1px;" >#</th>
                @if(isset($thead))
                {{ $thead }}
                @endif
                <th style="width: 1px;" class="sticky-col"></th>
            </tr>
        </thead>
        <tbody>
            {{ $slot }}
            @if(sizeof($list) <= 0)
            <tr>
                <td class="text-center" colspan="99">" {{ __('lang.no_list') }} "</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

{!! $list->appends(\Request::except('page'))->render() !!}