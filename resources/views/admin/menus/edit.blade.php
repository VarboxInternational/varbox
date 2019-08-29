@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.menus._form', ['url' => route('admin.menus.update', ['location' => $location, 'id' => $item->getKey()])])
@endsection
