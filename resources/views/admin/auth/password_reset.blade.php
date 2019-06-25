@extends('varbox::layouts.admin.login')

@section('content')
    <div class="row">
        <div class="col col-login mx-auto">
            <div class="text-center mb-6">
                <img src="{{ asset('/vendor/varbox/images/logo.svg') }}" class="h-6" alt="{{ config('app.name') }}">
            </div>

            {!! form()->open(['url' => route('admin.password.update'), 'method' => 'post', 'class' => 'card']) !!}
            {!! form()->hidden('token', $token) !!}
            <div class="card-body p-6">
                <div class="card-title">Reset Password</div>
                <p class="text-muted">Enter a new password for your corresponding admin account email address.</p>

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

                <div class="form-group">
                    <label class="form-label" for="email-field">Email</label>
                    {!! form()->text('email', $email ?? old('email'), ['class' => 'form-control', 'aria-describedby' => 'emailHelp']) !!}
                </div>

                <div class="form-group">
                    <label class="form-label" for="email-field">Password</label>
                    {!! form()->password('password', ['class' => 'form-control']) !!}
                </div>

                <div class="form-group">
                    <label class="form-label" for="email-field">Confirm Password</label>
                    {!! form()->password('password_confirmation', ['class' => 'form-control']) !!}
                </div>
                <div class="form-footer">
                    {!! form()->submit('Reset Password', ['class' => 'btn btn-primary btn-square btn-block']) !!}
                </div>
            </div>
            {!! form()->close() !!}

            <div class="text-center text-muted">
                Forget it, <a href="{{ route('admin.login') }}">send me back</a> to the sign in page.
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {!! JsValidator::formRequest(config('varbox.varbox-binding.form_requests.password_reset_form_request', Varbox\Requests\PasswordResetRequest::class)) !!}
@endpush
