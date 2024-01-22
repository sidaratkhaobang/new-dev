<div class="row items-push mt-4">
	<div class="col-md-3">
        <div class="block block-rounded block-bordered mb-0 block-bordered-custom" href="javascript:void(0)">
            <div class="block-content block-content-full d-flex align-items-center justify-content-between">
					<div>
						<div class="fw-semibold mb-1">Supplier</div>
					</div>
					<div class="ms-3">
						<div class="fw-bolder">
							<span class="h6">{{ ($d->supplier) ? $d->supplier->name : '' }}</span>
						</div>
					</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="block block-rounded block-bordered mb-0 block-bordered-custom" href="javascript:void(0)">
            <div class="block-content block-content-full d-flex align-items-center justify-content-between">
				{{-- <div class="block-content block-content-full d-flex align-items-center justify-content-between"> --}}
					<div>
						<div class="fw-semibold mb-1">จำนวน</div>
					</div>
					<div class="ms-3">
						<div class="fw-bolder">
							<span class="h4">@{{ getNumberWithCommas(summary.amount) }} </span>ชิ้น
						</div>
					</div>
				{{-- </div> --}}
            </div>
        </div>
    </div>
	<div class="col-md-3">
        <div class="block block-rounded block-bordered mb-0 block-bordered-custom" href="javascript:void(0)">
            <div class="block-content block-content-full d-flex align-items-center justify-content-between">
				{{-- <div class="block-content block-content-full d-flex align-items-center justify-content-between"> --}}
					<div>
						<div class="fw-semibold mb-1">ราคาไม่รวม VAT สุทธิ </div>
						{{-- <div class="fs-sm text-muted">5 Orders</div> --}}
					</div>
					<div class="ms-3">
						<div class="fw-bolder">
							<span class="h4">@{{ getNumberWithCommas(summary.subtotal) }} </span>บาท
						</div>
					</div>
				{{-- </div> --}}
            </div>
        </div>
    </div>
	<div class="col-md-3">
        <div class="block block-rounded block-bordered mb-0 block-bordered-custom" href="javascript:void(0)">
            <div class="block-content block-content-full d-flex align-items-center justify-content-between">
				{{-- <div class="block-content block-content-full d-flex align-items-center justify-content-between"> --}}
					<div>
						<div class="fw-semibold mb-1">ราคารวมสุทธิ </div>
						{{-- <div class="fs-sm text-muted">5 Orders</div> --}}
					</div>
					<div class="ms-3">
						<div class="fw-bolder">
							<span class="h4">@{{ getNumberWithCommas(summary.total_after_discount) }} </span>บาท
						</div>
					</div>
				{{-- </div> --}}
            </div>
        </div>
    </div>
</div>
