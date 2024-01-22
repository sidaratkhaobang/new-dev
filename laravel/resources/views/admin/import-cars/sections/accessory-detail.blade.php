<div class="mb-5">
  <div class="table-wrap">
      <table class="table table-striped">
          <thead class="bg-body-dark">
              <th>#</th>
              <th>{{ __('import_cars.accessory') }}</th>
              <th>{{ __('import_cars.accessory_class') }}</th>
              <th>{{ __('import_cars.accessory_count') }}</th>
          </thead>
          <tbody v-if="accessory_list.length > 0">
                            <tr v-for="(item, index) in accessory_list">
                                <td style="width: 1%">@{{ index + 1 }}</td>
                                <td style="width: 40%">@{{ item.accessory_name }}</td>
                                <td style="width: 40%">@{{ item.version }}</td>
                                <td style="width: 19%">@{{ item.pr_line_acc_amount }}</td>
                            </tr>
          </tbody>
          <tbody v-else>
            <tr class="table-empty">
                <td class="text-center" colspan="5">“
                    {{ __('lang.no_list') . __('purchase_orders.purchase_requisition_car_detail') }} “</td>
            </tr>
          </tbody>
      </table>
  </div>
</div>