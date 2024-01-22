<p>การตอบคำถามจะตอบในรูปแบบของการพิมพ์ข้อความ</p>
<div id="app2" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap mb-3">
        <table class="table table-striped">
            <thead class="bg-body-dark">
            <th style="width: 5%">#</th>
            <th style="width: 5%">{{ __('car_inspections.seq_question') }}</th>
            <th style="width: 85%">{{ __('car_inspections.list_question') }}</th>
            <th class="sticky-col "></th>
            </thead>
            <tbody v-if="inputs.length > 0">
            <tr v-for="(input,k) in inputs">
                <td>
                    <div class="form-check d-inline-block">
                        <input class="form-check-input form-check-input-each" type="checkbox" v-model="inputs[k].status_question">
                        <input type="hidden" v-bind:name="'data2['+ k+ '][status_question]'" id="status_question" v-model="inputs[k].status_question">
                        <label class="form-check-label" for="row_{{ $d->id }}"></label>
                    </div>
                </td>
                <td>
                    <input type="number" class="form-control" v-model="inputs[k].seq" min="0">
                    <input type="hidden" v-bind:name="'data2['+ k+ '][seq]'" id="seq" v-model="inputs[k].seq">
                </td>
                <td>
                    <input type="text" class="form-control" v-model="inputs[k].name">
                    <input type="hidden" v-bind:name="'data2['+ k+ '][name]'" id="name" v-model="inputs[k].name">
                </td>
                <td>
                    @if(empty($view))
                        <a class="btn btn-danger btn-auto-width" v-on:click="remove(k)"><i class="fa-solid fa-trash-can" ></i></a>
                    @endif
                </td>
            </tr>
            </tbody>
            <tbody v-else>
            <tr class="table-empty">
                <td class="text-center" colspan="7">" {{ __('lang.no_list') }} "</td>
            </tr>
            </tbody>
        </table>
    </div>
    @if(empty($view))
        <div class="col-md-12 text-end">
            <button type="button" class="btn btn-primary"
                    onclick="add2()">{{ __('lang.add') }}</button>
        </div>
    @endif
</div>
