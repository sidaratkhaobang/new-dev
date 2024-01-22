@component('mail::message')
# แจ้งเตือน : {{ $title }}

{{ $description }}

@component('mail::button', ['url' => $url])
ตรวจสอบ
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
