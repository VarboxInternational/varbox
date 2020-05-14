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
                    <div class="card-value float-right text-white bg-green px-3 rounded">
                        <i class="fe fe-image"></i>
                    </div>
                    <h3 class="mb-1">{{ $filesUploadedInTheLasMonth }} {{ \Illuminate\Support\Str::plural('File', $filesUploadedInTheLasMonth) }}</h3>
                    <div class="text-muted">uploaded last month</div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-value float-right text-white bg-red px-3 rounded">
                        <i class="fa fa-chart-line"></i>
                    </div>
                    <h3 class="mb-1">{{ $activityLoggedInTheLasMonth }} {{ \Illuminate\Support\Str::plural('Activity', $activityLoggedInTheLasMonth) }}</h3>
                    <div class="text-muted">logged in the last month</div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-value float-right text-white bg-yellow px-3 rounded">
                        <i class="fa fa-exclamation-circle"></i>
                    </div>
                    <h3 class="mb-1">{{ $errorsOccurredInTheLasMonth }} {{ \Illuminate\Support\Str::plural('Error', $errorsOccurredInTheLasMonth) }}</h3>
                    <div class="text-muted">occurred in the last month</div>
                </div>
            </div>
        </div>
    </div>
    <div class="row row-cards">
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Latest Users</h2>
                    <div class="card-options">
                        <a href="{{ route('admin.users.index', ['sort' => 'created_at', 'direction' => 'desc']) }}" class="btn btn-blue btn-sm">View All</a>
                    </div>
                </div>
                <table class="table card-table">
                    <tbody>
                        @forelse($latestUsers as $user)
                            <tr>
                                <td>
                                    <div>{{ $user->email ?: 'N/A' }}</div>
                                    <div class="small text-muted">{{ $user->name ?: 'N/A' }}</div>
                                </td>
                                <td class="align-middle text-right">
                                    <span class="badge badge-default">
                                        @if($user->active) active @else inactive @endif
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">No users registered in the last month</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Latest Uploads</h2>
                    <div class="card-options">
                        <a href="{{ route('admin.errors.index', ['sort' => 'updated_at', 'direction' => 'desc']) }}" class="btn btn-green btn-sm">View All</a>
                    </div>
                </div>
                <table class="table card-table">
                    <tbody>
                        @forelse($latestUploads as $upload)
                            <tr>
                                <td class="w-1">
                                    <a href="{{ uploaded($upload->full_path)->url() }}" target="_blank">
                                        <span class="avatar d-block rounded bg-white" @if($upload->isImage()) style="background-image: url({{ uploaded($upload->full_path)->thumbnail() }})" @endif>
                                            <i class="fa @if($upload->isVideo()) fa-file-video @elseif($upload->isAudio()) fa-file-audio @elseif($upload->isFile()) fa-file-alt @endif text-blue" style="vertical-align: middle; font-size: 245%;"></i>
                                        </span>
                                    </a>
                                </td>
                                <td>
                                    <div>{{ $upload->original_name ? \Illuminate\Support\Str::limit($upload->original_name, 30) : 'N/A' }}</div>
                                    <div class="small text-muted">{{ $upload->mime ?: 'N/A' }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">No files uploaded in the last month</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Latest Activity</h2>
                    <div class="card-options">
                        <a href="{{ route('admin.activity.index', ['sort' => 'created_at', 'direction' => 'desc']) }}" class="btn btn-red btn-sm">View All</a>
                    </div>
                </div>
                <table class="table card-table">
                    <tbody>
                        @forelse($latestActivity as $activity)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ optional($activity->user)->email ?: 'A user' }}</strong> {{ $activity->event }} a {{ $activity->entity_type ?: $activity->subject->getMorphClass() }}
                                    </div>
                                    <div class="small text-muted">{{ Carbon\Carbon::parse($activity->created_at)->diffForHumans()}}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">No files uploaded in the last month</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
