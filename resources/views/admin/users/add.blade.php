@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.users._form', ['url' => route('admin.users.store')])
@endsection
