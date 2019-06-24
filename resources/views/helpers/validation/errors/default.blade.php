@if($errors->any())
    @foreach($errors->getMessages() as $field => $messages)
        @foreach($messages as $message)
            <p>{!! $message !!}</p>
        @endforeach
    @endforeach
@endif