<a href="{{ $url ?? '#' }}" class="btn {!! $class ?? '' !!}" {!! implode(' ', $attributes) !!}>
    <i class="fa {!! $icon ?? '' !!}"></i>&nbsp; {!! $text ?? 'Click' !!}
</a>