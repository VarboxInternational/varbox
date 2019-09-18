@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.redirects._form', ['url' => route('admin.redirects.update', $item->getKey())])
@endsection
