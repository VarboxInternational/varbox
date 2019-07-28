@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.emails._form', ['on_revision' => true])
@endsection

{!! revision()->view($revision, $item) !!}
