@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.menus._form', ['url' => route('admin.menus.update', [$location, $item->getKey()])])
@endsection
