@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.permissions._form', ['url' => route('admin.permissions.update', $item->getKey())])
@endsection
