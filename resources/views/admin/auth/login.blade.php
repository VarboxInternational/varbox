
@extends('varbox::layouts.admin.login')

@section('content')
    <div class="row">
        <div class="col col-login mx-auto">
            {!! form()->open(['url' => request()->url(), 'method' => 'post', 'class' => 'card']) !!}
                <div class="card-body p-6">
                    <div class="card-title">Administrator Login</div>
                    <p class="text-muted">Enter your credentials and start administrating your application.</p>

                    @if($errors->any())
                        <div class="col-lg-12 mb-4 pl-0 pr-0">
                            @foreach($errors->getMessages() as $field => $messages)
                                @foreach($messages as $message)
                                    <span class="error text-danger small">{!! $message !!}</span>
                                    <br />
                                @endforeach
                            @endforeach
                        </div>
                    @endif

                    @if(session()->has('message'))
                        <div class="col-lg-12 mb-4 pl-0 pr-0">
                            <span class="success text-success small">{{ session('message') }}</span>
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        {!! form()->text('email', null, ['class' => 'form-control', 'aria-describedby' => 'emailHelp']) !!}
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            Password
                        </label>
                        {!! form()->password('password', ['class' => 'form-control']) !!}
                        <a href="{{ route('admin.password.forgot') }}" class="small">I forgot my password</a>
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-blue btn-square btn-block">Sign In</button>
                    </div>
                </div>
            {!! form()->close() !!}

        </div>
    </div>
@endsection

@push('scripts')
    {{--{!! JsValidator::formRequest(config('varbox.bindings.form_requests.login_form_request', \Varbox\Requests\LoginRequest::class)) !!}--}}
@endpush
