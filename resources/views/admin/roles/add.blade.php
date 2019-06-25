@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.roles._form', ['url' => route('admin.roles.store')])
@endsection