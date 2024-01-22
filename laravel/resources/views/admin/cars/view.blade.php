@extends('admin.layouts.layout')
@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                @include('admin.cars.sections.view.car-info')
                @include('admin.cars.sections.store-data')
                @include('admin.cars.sections.view.other-info')
                @include('admin.cars.sections.view.purchasing')
                @include('admin.cars.sections.car-element')
                <div class="row push">
                    <div class="text-end">     
                        <a class="btn btn-secondary" href="{{ route('admin.cars.index') }}" >{{ __('lang.back') }}</a>          
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection