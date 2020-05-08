@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.configs._form', ['url' => route('admin.configs.store')])
@endsection
