@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.auth.permissions._form', ['url' => route('admin.permissions.store')])
@endsection