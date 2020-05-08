@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.states._form', ['url' => route('admin.states.update', $item->getKey())])
@endsection
