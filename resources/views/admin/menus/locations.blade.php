@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        @forelse($locations as $location => $name)
            <div class="col-md-6">
                <div class="card p-3">
                    <div class="d-flex align-items-center">
                        <span class="stamp stamp-md bg-blue mr-3">
                            <i class="fe fe-navigation-2"></i>
                        </span>
                        <div>
                            <h4 class="m-0">
                                <a href="{{ route('admin.menus.index', $location) }}">
                                    {{ $name ?: 'N/A' }} <small class="text-gray">Location</small>
                                </a>
                            </h4>
                            <small class="text-muted">
                                click to view the menu items from this location
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card p-4">
                    <span class="text-muted">There are no menu locations</span>
                </div>
            </div>
        @endforelse
@endsection
