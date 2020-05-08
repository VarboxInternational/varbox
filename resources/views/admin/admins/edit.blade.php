@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.admins._form', ['url' => route('admin.admins.update', $item->getKey())])
@endsection
