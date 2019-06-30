@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.states._form', ['url' => route('admin.states.store')])
@endsection