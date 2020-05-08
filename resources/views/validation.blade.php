@if($errors->any())
    <div class="alert alert-danger  alert-dismissible col-lg-12">
        <button type="button" class="close" data-dismiss="alert"></button>

        @foreach($errors->getMessages() as $field => $messages)
            @foreach($messages as $message)
                {!! $message !!}<br />
            @endforeach
        @endforeach
    </div>
@endif
