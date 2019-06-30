@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.addresses._form', ['url' => route('admin.addresses.update', [$user->getKey(), $item->getKey()])])
@endsection