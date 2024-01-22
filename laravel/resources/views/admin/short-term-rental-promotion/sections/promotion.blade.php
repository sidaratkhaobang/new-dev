<div class="block {{ __('block.styles') }}">
    <div class="block-header">
        <div class="block-title" >
            <div class="d-flex" >
                <div style="flex-shrink: 0; flex-basis: 200px;" >
                    <span>
                        <i class=" me-2 icon-document"></i> เลือกโปรโมชัน
                    </span>
                </div>
                <div class="flex-grow-1" >
                    <input type="text" class="form-control" id="promotion_search" name="promotion_search" placeholder="ค้นหา" >
                </div>
            </div>
        </div>
        <div class="block-options ">
            <button type="button" class="btn btn-clear-promotion me-2" >
                ล้างข้อมูล
            </button>
            <button type="button" class="btn btn-primary btn-search-promotion" >
                <i class="icon-search me-2"></i>ค้นหา
            </button>
        </div>
    </div>
    <div class="block-header pt-1 mb-2">
        <div class="block-title" >
            <div class="d-flex" >
                <div style="flex-shrink: 0; flex-basis: 200px;" >
                    &nbsp;
                </div>
                <div class="flex-grow-1" >
                    <input type="text" class="form-control" id="promotion_code_search" name="promotion_code_search" placeholder="กรอกโค้ดโปรโมชัน / คูปอง" >
                </div>
            </div>
        </div>
        <div class="block-options ps-0">
            <button type="button" class="btn btn-primary btn-add-coupon" style="margin-left: 12px;" >
                <i class="fa fa-plus-circle me-2"></i>เพิ่ม
            </button>
        </div>
    </div>
    <div class="block-content pt-0">
        <div id="block-promotion" class="form-group row" >
            <div class="d-flex justify-content-between" >
                <p class="m-0 pt-2" >โปรโมชันที่ใช้ได้</p>

                <div class="d-flex flex-row mb-3" style="cursor: pointer;">
                    <div id="to_left" class="svg-container" data-interval="false"
                         data-bs-target="#carousel-promotions" data-bs-slide="prev">
                        <img src="{{ asset('images/btn_arrow_left.png') }}" >
                    </div>
                    <div id="to_right" class="svg-container ms-3" data-interval="false"
                         data-bs-target="#carousel-promotions" data-bs-slide="next">
                         <img src="{{ asset('images/btn_arrow_right.png') }}" >
                    </div>
                </div>
            </div>
            <div id="carousel-promotions" class="carousel slide" data-ride="carousel" data-interval="0" >
                <div class="carousel-inner"></div>
            </div>
        </div>
    </div>
</div>

