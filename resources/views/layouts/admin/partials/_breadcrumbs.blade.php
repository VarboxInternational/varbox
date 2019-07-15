@if(config('varbox.breadcrumbs.enabled', true) === true)
    <nav aria-label="breadcrumb" class="">
        <ol class="breadcrumb mb-0 px-0">
            @foreach (breadcrumbs()->generate() as $breadcrumb)
                <li class="breadcrumb-item {{ $loop->first ? 'ml-md-auto ml-sm-0' : '' }} {{ $loop->last ? 'active' : '' }}">
                    @if($loop->last)
                        {{ $breadcrumb->title }}
                    @else
                        <a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a>
                        <i class="fa fa-angle-right mx-2" aria-hidden="true" style="color: #868e96"></i>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif