@extends('varbox::layouts.default')

@section('content')
    <div class="row row-cards">
        <div class="col-md-12 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-value float-right text-white bg-blue px-3 rounded">
                        <i class="fe fe-users"></i>
                    </div>
                    <h3 class="mb-1">{{ $usersRegisteredInTheLastMonth }} {{ \Illuminate\Support\Str::plural('User', $usersRegisteredInTheLastMonth) }}</h3>
                    <div class="text-muted">registered in the last month</div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-value float-right text-white bg-yellow px-3 rounded">
                        <i class="fe fe-users"></i>
                    </div>
                    <h3 class="mb-1">{{ $usersRegisteredInTheLastWeek }} {{ \Illuminate\Support\Str::plural('User', $usersRegisteredInTheLastWeek) }}</h3>
                    <div class="text-muted">registered in the last week</div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-value float-right text-white bg-green px-3 rounded">
                        <i class="fe fe-check"></i>
                    </div>
                    <h3 class="mb-1">{{ $activeUsers }} {{ \Illuminate\Support\Str::plural('User', $activeUsers) }}</h3>
                    <div class="text-muted">who are active</div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-value float-right text-white bg-red px-3 rounded">
                        <i class="fe fe-x"></i>
                    </div>
                    <h3 class="mb-1">{{ $inactiveUsers }} {{ \Illuminate\Support\Str::plural('User', $inactiveUsers) }}</h3>
                    <div class="text-muted">who are inactive</div>
                </div>
            </div>
        </div>
    </div>
@endsection
