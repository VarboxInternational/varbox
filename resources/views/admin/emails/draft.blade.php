@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    @include('varbox::admin.emails._form', ['url' => route('admin.emails.update', $item->getKey())])
@endsection

{{--{!! revision()->view($revision, $item) !!}--}}


@section('top')
    <div class="alert alert-info col-lg-12 mb-5">
        <div class="d-inline-block float-left text-left mx-auto" style="margin-top: 2px;">
            <i class="fe fe-info mr-2" aria-hidden="true"></i>
        </div>
        <div class="d-inline-block">
            <h4>This record is currently drafted!</h4>
            <div class="btn-list mt-4">
                @permission('drafts-publish')
                {{--{!! form()->open(['url' => route('admin.revisions.rollback', $revision->getKey()), 'method' => 'POST', 'class' => 'float-left d-inline']) !!}--}}
                <button type="submit" class="button-publish-draft btn btn-blue">
                    <i class="fe fe-check mr-2"></i>Publish Draft
                </button>
                {{--{!! form()->close() !!}--}}
                @endpermission
            </div>
        </div>
    </div>
@endsection