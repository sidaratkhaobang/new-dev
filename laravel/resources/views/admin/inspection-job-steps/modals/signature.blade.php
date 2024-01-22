<div class="modal fade" id="modal-signature" aria-labelledby="modal-import-cars" aria-hidden="false" tabindex="-1" role="dialog" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="car-accessory-modal-label">ลายเซ็นลูกค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p style="margin-top:90px; margin-left:330px; position:fixed; color: #A3A3A3;">เซ็นชื่อตรงนี้</p>
                <div class="wrapper">
                    <canvas id="signature-pad" class="signature-pad" width=760 height=200
                        style="border:1px solid #A3A3A3; border-style: dashed;"></canvas>
                </div>
                <div class="mt-2" style="cursor: pointer; color:red">
                    <i class="fa fa-eraser" aria-hidden="true"></i>&nbsp;<a id="clear1">ล้างข้อมูล</a>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-end">
                    <button class="btn btn-primary" id="save">บันทึก</button>

                </div>
            </div>
        </div>
    </div>
</div>