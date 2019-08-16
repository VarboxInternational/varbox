@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.pages._form', ['url' => route('admin.pages.store', ['parent' => $parent ?: null])])])
@endsection
