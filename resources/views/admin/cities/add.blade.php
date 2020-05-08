@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.cities._form', ['url' => route('admin.cities.store')])
@endsection
