@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.countries._form', ['url' => route('admin.countries.store')])
@endsection