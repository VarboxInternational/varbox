@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.admins._form', ['url' => route('admin.admins.store')])
@endsection