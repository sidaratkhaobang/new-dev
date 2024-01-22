@extends('admin.layouts.layout')
@section('page_title', 'Element Sample')

@section('content')
    <div class="block block-rounded">
        <div class="block-header">
            <h3 class="block-title"> ตัวอย่าง Element</h3>
        </div>
        <div class="block-content">
            {{-- input --}}
            <h2 class="content-heading pt-0">Input</h2>
            <div class="row push">
                <div class="col">
                    <x-forms.input id="name" :value=null :label="__('name')"
                        :optionals="['required' => true, 'placeholder' => 'กรุณาระบุข้อมูล']" />
                </div>
            </div>

            @auth
            {{-- text area --}}
            <h2 class="content-heading pt-0">Text Area</h2>
            <x-forms.text-area id="description" :value="null" :label="__('รายละเอียด')" />
            @endauth
            {{-- radio --}}
            <h2 class="content-heading pt-0">Radio</h2>
            <x-forms.radio id="is_period" :value="null" :label="__('สถานะ')" 
                :list="[
                    ['name' =>  __('active'), 'value' => 1], 
                    ['name' =>  __('inactive'), 'value' => 2],
                ]" :optionals="['required' => true]" />
        </div>
    </div>
@endsection