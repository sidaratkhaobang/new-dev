<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <div class="justify-content-between mb-4">
            <div class="row push mb-4">
                <div class="col-sm-5">
                    <div class="w-100 block-loss-ratio-danger d-flex justify-content-center align-items-center">
                        <span>ฝ่ายผิด</span>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-3 text-center">
                            <p class="text-center">
                                L/R
                            </p>
                            <p class="text-center total-number-font mb-0">
                                42.9%
                            </p>
                        </div>
                        <div class="col-sm-3">
                            <p class="text-center">
                                ค่าเสียหาย
                            </p>
                            <p class="text-center total-number-font mb-0">
                                {{number_format($totalLossFlase) ?? '0'}}
                            </p>
                        </div>
                        <div class="col-sm-3">
                            <p class="text-center">
                                จำนวนเคลม
                            </p>
                            <p class="text-center total-number-font mb-0">
                                {{number_format($totalCarFalseCase) ?? '0'}}
                            </p>
                        </div>
                        <div class="col-sm-3">
                            <p class="text-center">
                                จำนวนรถที่เกิดอุบัติเหตุ
                            </p>
                            <p class="text-center total-number-font mb-0">
                               {{number_format($totalAccidentFalseCar) ?? '0'}}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2 d-flex align-items-center ps-0 pe-0">
                    <div class="block-bg-total-car d-grid w-100 h-100 align-items-center">
                        <span class="text-center d-block" style="font-size: 16px;">
                            {{__('insurance_loss_ratios.total_car')}}
                        </span>
                        <span class="total-car-font-size text-center d-block">
                            {{ number_format($totalCarAccident) ?? '0' }}
                        </span>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="w-100 block-loss-ratio-success d-flex justify-content-center align-items-center">
                        <span>ฝ่ายถูก</span>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-4 text-center">
                            <p class="text-center">
                                จำนวนรถที่เกิดอุบัติเหตุ
                            </p>
                            <p class="text-center total-number-font mb-0">
                                {{number_format($totalAccidentTrueCar) ?? '0'}}
                            </p>
                        </div>
                        <div class="col-sm-4">
                            <p class="text-center">
                                จำนวนเคลม
                            </p>
                            <p class="text-center total-number-font mb-0">
                                {{number_format($totalCarTrueCase) ?? '0'}}
                            </p>
                        </div>
                        <div class="col-sm-4">
                            <p class="text-center">
                              ค่าเสียหาย
                            </p>
                            <p class="text-center total-number-font mb-0">
                                {{number_format($totalLossTrue) ?? '0'}}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
