@push('scripts')
<script>
    let addVoucherVue = new Vue({
        el: '#voucher-section',
        data: {
            voucher_list: [],
            promotion_list: @if (isset($promotion_list) && !empty(count($promotion_list)))
                @json($promotion_list)
                @else
            []
            @endif ,
            voucher_code: '',
            promotion_code: '',
            promotion: @if (isset($checked_promotions))
                @json($checked_promotions)
                @else
                ''
            @endif ,
            rental: @if (isset($rental))
                @json($rental)
                @else
            []
            @endif ,
            rental_id: @if (isset($rental_id))
                @json($rental_id)
                @else
            []
            @endif ,
            summary: @if (isset($summary))
                @json($summary)
                @else
            []
            @endif ,
            checked_vouchers: @if (isset($checked_vouchers))
                @json($checked_vouchers)
                @else
            []
            @endif ,
            withholding_tax_list: @if (isset($withholding_tax_list))
                @json($withholding_tax_list)
                @else
            []
            @endif ,
            show_msg: false
        },
        watch: {
            promotion: async function (select_promotion) {
                this.show_msg = true;
                data = await this.getSummary();
                if (data.data.success) {
                    this.summary = data.data.data;
                    this.clearMsg();
                }
            },
            checked_vouchers: async function (selected_vouchers) {
                this.show_msg = true;
                data = await this.getSummary();
                if (data.data.success) {
                    this.summary = data.data.data;
                    this.clearMsg();
                }
            },
        },
        methods: {
            display: function () {
                $("#voucher-section").show();
            },
            add: async function () {
                var _this = this;
                var voucher_code = this.voucher_code;
                if (!voucher_code) {
                    return warningAlert("{{ __('lang.required_field_inform') }}");
                }
                var promotion_type = "{{ PromotionTypeEnum::VOUCHER }}";
                var voucher_data = await this.getVoucherDetail(voucher_code, promotion_type);

                if (!voucher_data.data.success) {
                    return warningAlert(voucher_data.data.data);
                }
                $('.voucher-wrap').append(voucher_data.data.html);
                /* var voucher = voucher_data.data.data;

                if (!voucher) {
                    return warningAlert("{{ __('short_term_rentals.voucher_not_found') }}");
                }
                const voucher_exist = this.voucher_list.some(function (el) {
                    return el.id === voucher.id;
                });

                if (voucher_exist) {
                    return warningAlert("{{ __('short_term_rentals.voucher_exist') }}");
                }
                if (voucher) {
                    _this.voucher_list.push(voucher);
                    _this.display();
                    this.voucher_code = '';
                } else {
                    warningAlert("{{ __('lang.required_field_inform') }}");
                } */
            },
            addPromotionCode: async function () {
                var _this = this;
                var promotion_code = this.promotion_code;
                if (!promotion_code) {
                    return warningAlert("{{ __('lang.required_field_inform') }}");
                }
                var promotion_type = "{{ PromotionTypeEnum::PROMOTION }}";
                var voucher_data = await this.getVoucherDetail(promotion_code, promotion_type);

                if (!voucher_data.data.success) {
                    return warningAlert("{{ __('short_term_rentals.promotion_not_found') }}");
                }
                var voucher = voucher_data.data.data;

                if (!voucher) {
                    return warningAlert("{{ __('short_term_rentals.promotion_not_found') }}");
                }
                const voucher_exist = this.promotion_list.some(function (el) {
                    return el.id === voucher.id;
                });

                if (voucher_exist) {
                    return warningAlert("{{ __('short_term_rentals.promotion_exist') }}");
                }
                if (voucher) {
                    _this.promotion_list.push(voucher);
                    _this.display();
                    this.promotion_code = '';
                } else {
                    warningAlert("{{ __('lang.required_field_inform') }}");
                }
            },
            getVoucherDetail: async function (code, promotion_type) {
                try {
                    /* const response = await axios.get("#", { // route('admin.short-term-rental.voucher-detail')
                        params: {
                            voucher_code: code,
                            rental_id: this.rental_id,
                            promotion_type: promotion_type
                        }
                    });
                    return response; */
                } catch (e) {
                    throw e;
                }
            },
            getSummary: async function (tax) {
                try {
                    const response = await axios.get(
                        "{{ route('admin.short-term-rentals.promotion-detail') }}", {
                            params: {
                                promotion_id: this.promotion,
                                promotion_codes: this.checked_vouchers,
                                rental_bill_id: this.rental_bill_id,
                                withholding_tax: tax,
                            }
                        });
                    return response;
                } catch (e) {
                    throw e;
                }
            },
            clearMsg: function () {
                setTimeout(() => {
                    this.summary['msg'] = ''
                }, 3000)
            },
            calculateWithholdingTax: async function (tax) {
                this.show_msg = true;
                data = await this.getSummary(tax);
                if (data.data.success) {
                    this.summary = data.data.data;
                    this.clearMsg();
                }
            },
            checked: function (id) {
                if (this.promotion && (id == this.promotion)) {
                    this.promotion = '';
                }
            },
            removeVoucher(index) {
                ++index
                this.voucher_list = this.voucher_list.slice(index)
            }
        },
        props: ['title'],
    });
    addVoucherVue.display();

    function addVoucher() {
        addVoucherVue.add();
    }

    function addPromotion() {
        addVoucherVue.addPromotionCode();
    }

    /* $('input[name="withholding_tax[]"]').change(function () {
        var val = this.checked ? this.value : 0;
        addVoucherVue.calculateWithholdingTax(val);
    });

    $('#active_tax').change(function () {
        if (!this.checked) {
            addVoucherVue.calculateWithholdingTax(0);
        }
    }); */

    function getPromotionData(){
        var rental_id = $('#rental_id').val();
        var s = $('#promotion_search').val();
        var params = {
            rental_id: rental_id,
            s: s
        };
        axios.post("{{ route('admin.short-term-rental.promotion.promotion-data') }}", params).then(response => {
            if (response.data.success) {
                $('#carousel-promotions').carousel('dispose');
                document.querySelector(".carousel-inner").innerHTML = response.data.html;
                $('#carousel-promotions').carousel({
                    interval: 0
                });

                var promotion_id_selected = $('#promotion_id_selected').val();
                if(promotion_id_selected != ""){
                    $("input[name=promotion_id][value='" + promotion_id_selected + "']").prop("checked", true);
                }
            }
        });
    }

    function getPromotionCoupon(coupon_code){
        var rental_id = $('#rental_id').val();
        var s = $('#promotion_search').val();
        var params = {
            rental_id: rental_id,
            s: s,
            coupon_code: coupon_code
        };
        axios.get("{{ route('admin.short-term-rental.promotion.promotion-coupon') }}", {
            params: params
        }).then(response => {
            if (response.data.success) {
                $('#carousel-promotions').carousel('dispose');
                document.querySelector(".carousel-inner").innerHTML = response.data.html;
                $('#carousel-promotions').carousel({
                    interval: 0
                });

                var promotion_id_selected = $('#promotion_id_selected').val();
                if(promotion_id_selected != ""){
                    $("input[name=promotion_id][value='" + promotion_id_selected + "']").prop("checked", true);
                }
            } else {
                warningAlert(response.data.message);
            }
        });
    }

    function getPromotionVoucher(voucher_code){
        var rental_id = $('#rental_id').val();
        var params = {
            rental_id: rental_id,
            voucher_code: voucher_code
        };
        axios.get("{{ route('admin.short-term-rental.promotion.promotion-voucher') }}", {
            params: params
        }).then(response => {
            if (response.data.success) {
                document.querySelector(".voucher-wrap").innerHTML = response.data.html + document.querySelector(".voucher-wrap").innerHTML;
            } else {
                warningAlert(response.data.message);
            }
        });
    }

    $(document).ready(() => {
        getPromotionData();

        /* $('#promotion_search').on('keyup', delay(function(){
            getPromotionData();
        }, 300)); */

        $('.btn-search-promotion').on('click', function(){
            getPromotionData();
        });

        $('.btn-add-coupon').on('click', function(){
            var coupon_code = $('#promotion_code_search').val();
            if(empty(coupon_code)){
                warningAlert("กรุณากรอกโค้ดโปรโมชัน / คูปอง");
                return false;
            }
            getPromotionCoupon(coupon_code);
        });

        $('.btn-add-voucher').on('click', function(){
            var voucher_code = $('#voucher_code').val();
            if(empty(voucher_code)){
                warningAlert("กรุณากรอกเลข Voucher");
                return false;
            }
            getPromotionVoucher(voucher_code);
        });
    });
</script>
@endpush
