@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.pages._form', ['url' => route('admin.pages.update', $item->getKey())])
@endsection
