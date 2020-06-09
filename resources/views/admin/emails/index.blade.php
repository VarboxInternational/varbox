@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    @permission('emails-add')
                        @include('varbox::buttons.add', ['url' => route('admin.emails.create')])
                    @endpermission
                    @permission('emails-export')
                        @include('varbox::buttons.csv', ['url' => route('admin.emails.csv', request()->query())])
                    @endpermission
                </div>
            </div>

            @include('varbox::admin.emails._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.emails._table')

            {!! $items->links('varbox::pagination', request()->query()) !!}
        </div>
    </div>
@endsection
