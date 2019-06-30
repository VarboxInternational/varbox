@if($message)
    <div class="alert alert-{{ $type }}  alert-dismissible fade show col-lg-12 mb-5">
        <button type="button" class="close" data-dismiss="alert"></button>
        <div class="d-inline-block float-left text-left mx-auto" style="margin-top: 2px;">
        @switch($type)
            @case('success')
                <i class="fe fe-check-circle mr-2" aria-hidden="true"></i>
            @break
            @case('danger')
                <i class="fe fe-x-circle mr-2" aria-hidden="true"></i>
            @break
            @case('warning')
                <i class="fe fe-alert-circle mr-2" aria-hidden="true"></i>
            @break
            @case('info')
                <i class="fe fe-info mr-2" aria-hidden="true"></i>
            @break
        @endswitch
        </div>
        <div class="d-inline-block">{!! $message !!}</div>
    </div>

    @php(session()->forget("flash_{$type}"))
@endif