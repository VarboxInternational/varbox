@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.translations._form', ['url' => route('admin.translations.update', $item->getKey())])
@endsection
