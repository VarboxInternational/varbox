@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.menus._form', ['url' => route('admin.menus.store', [$location, $parent ?: null])])
@endsection
