@extends('varbox::layouts.default')

@section('title', $title)

@section('top')
    <div class="alert alert-info col-lg-12 mb-5">
        <div class="d-inline-block float-left text-left mx-auto" style="margin-top: 2px;">
            <i class="fe fe-info mr-2" aria-hidden="true"></i>
        </div>
        <div class="d-inline-block">
            <h4>You are currently adding an address for user: <strong>{{ $user->email }}</strong></h4>
            <div class="btn-list mt-4">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary ">
                    <i class="fe fe-arrow-left mr-2"></i>Back To User
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @include('varbox::admin.addresses._form', ['url' => route('admin.addresses.store', $user->getKey())])
@endsection
