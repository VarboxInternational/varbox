<a href="{{ $url or '#' }}" class="btn {!! $class or '' !!}" {!! implode(' ', $attributes) !!}>
    <i class="fa {!! $icon or '' !!}"></i>&nbsp; {!! $text or 'Click' !!}
</a>