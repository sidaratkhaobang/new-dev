<div class="row" >
    @foreach($data as $index => $address)
    <div class="col-12 col-sm-4 col-md-3 col-lg-3" style="font-size: 14px;" >
        <x-forms.radio-block id="customer_billing_address_id_{{ $index }}" name="customer_billing_address_id" value="{{ $address->id }}" selected="{{ null }}" >
            <span class="block-title" >{{ $address->billing_customer_type_text }}</span>
            <p class="m-0" >
                <img class="me-1" src="{{ asset('images/icons/user.png') }}" alt="user">
                <b>{{ $address->name }}</b>
            </p>

            <p class="m-0" >
                <img class="me-1" src="{{ asset('images/icons/usercode.png') }}" alt="user-code">
                <b>{{ $address->tax_no }}</b>
            </p>

            <p class="m-0" >
                <img class="me-1" src="{{ asset('images/icons/mail.png') }}" alt="mail">
                <span>{{ $address->email }}</span>
            </p>

            <p class="m-0" >
                <img class="me-1" src="{{ asset('images/icons/phone.png') }}" alt="phone">
                <span>{{ $address->tel }}</span>
            </p>

            <p class="m-0" >
                <img class="me-1" src="{{ asset('images/icons/location.png') }}" alt="location">
                <span>{{ $address->address }}</span>
            </p>
        </x-forms.radio-block>
    </div>
    @endforeach
</div>