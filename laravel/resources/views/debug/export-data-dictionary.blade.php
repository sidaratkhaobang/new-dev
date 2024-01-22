@extends('admin.layouts.layout')
@section('page_title', 'Data Doctionary')

@section('content')
@foreach($tables as $table)
<div class="block block-rounded">
    <div class="block-header">
        <h3 class="block-title">{{ $table->Tables_in_newcarfly }}</h3>
    </div>
    <div class="block-content" >
        <table class="table table-bordered" >
            <thead>
                <tr>
                    <th style="width: 30%;" >column_name</th>
                    <th style="width: 30%;" >column_type</th>
                    <th style="width: 15%;" >is_nullable</th>
                    <th style="width: 15%;" >extra</th>
                    <th style="width: 15%;" >column_comment</th>
                </tr>
            </thead>
            <tbody>
                @php
                $filtered = $fields->filter(function($item) use ($table) {
                    return (strcmp($item->table_name, $table->Tables_in_newcarfly) == 0);
                });
                @endphp
                @foreach($filtered as $field)
                <tr>
                    <td>{{ $field->column_name }}</td>
                    <td>{{ $field->column_type }}</td>
                    <td>{{ $field->is_nullable }}</td>
                    <td>{{ $field->extra }}</td>
                    <td>{{ $field->column_comment }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <br><br>
</div>
@endforeach
@endsection
