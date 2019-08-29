@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.languages._form', ['url' => route('admin.languages.store')])
@endsection
