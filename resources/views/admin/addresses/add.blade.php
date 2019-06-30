@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.addresses._form', ['url' => route('admin.addresses.store', $user->getKey())])
@endsection