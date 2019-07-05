@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.configs._form', ['url' => route('admin.configs.store')])
@endsection