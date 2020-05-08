@extends('varbox::layouts.login')

@section('content')
    <div class="row">
        <div class="col col-login mx-auto">
            {!! form()->open(['url' => request()->url(), 'method' => 'post', 'class' => 'card']) !!}
                <div class="card-body p-6">
                    <div class="card-title">Forgot Password</div>
                    <p class="text-muted">Enter your email address and you'll receive a link to reset your password.</p>

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
                        {!! form()->text('email', null, ['id' => 'email-field', 'class' => 'form-control', 'aria-describedby' => 'emailHelp']) !!}
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
