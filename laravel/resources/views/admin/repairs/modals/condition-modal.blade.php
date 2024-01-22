<div class="modal fade" id="modal-condition" tabindex="-1" aria-labelledby="modal-condition" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout modal-dialog-scrollable" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="condition-modal-label">เงื่อนไขบริการ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-wrap db-scroll">
                    <table class="table table-striped table-vcenter">
                        <thead class="bg-body-dark">
                            <tr>
                                <th>เงื่อนไขบริการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($condotion_lt_rental as $index => $d)
                                <tr>
                                    <td>{{ $d->name }} <br>
                                        @if (sizeof($d->sub_quotation_form_checklist) > 0)
                                            @foreach ($d->sub_quotation_form_checklist as $key_checklist => $item_checklist)
                                                &nbsp;&nbsp;-
                                                    {{ $item_checklist->quotation_form_checklist_name }}
                                                    <br>
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
