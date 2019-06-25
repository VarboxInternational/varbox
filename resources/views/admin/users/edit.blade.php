@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.users._form', ['url' => route('admin.users.update', $item->getKey())])
@endsection