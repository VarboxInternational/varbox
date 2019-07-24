@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.emails._form', ['url' => route('admin.emails.store')])
@endsection