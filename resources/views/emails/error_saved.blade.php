<p>An error has occurred!</p>
@if($error->url || $error->file || $error->line)
    <hr>
    <p>
        @if($error->url)
            URL: <strong>{{ $error->url }}</strong><br />
        @endif
        @if($error->file)
            File: <strong>{{ $error->file }}</strong><br />
        @endif
        @if($error->line)
            Line: <strong>{{ $error->line }}</strong><br /><br />
        @endif
        @if($error->type)
            Type: <strong>{{ $error->type }}</strong><br />
        @endif
        @if($error->code)
            Code: <strong>{{ $error->code }}</strong>
        @endif
    </p>
@endif
@if($error->trace)
    <hr>
    <p><strong>Trace</strong></p>
    <p>
        {!! nl2br($error->trace) !!}
    </p>
@endif