@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.roles._form', ['url' => route('admin.roles.update', $item->getKey())])
@endsection
