@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.countries._form', ['url' => route('admin.countries.update', $item->getKey())])
@endsection
