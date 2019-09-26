@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        @foreach($types as $type => $label)
            <div class="col-md-4">
                <div class="card p-3">
                    @include('varbox::admin.schema.types.' . $type)
                </div>
            </div>
        @endforeach
    </div>
@endsection
