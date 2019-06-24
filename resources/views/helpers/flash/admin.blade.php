@if($message)
    <div class="alert alert-{{ $type }}  alert-dismissible fade show col-lg-12 mb-5">
        <button type="button" class="close" data-dismiss="alert"></button>
        @switch($type)
            @case('success')
                <i class="fe fe-check-circle mr-2" aria-hidden="true"></i>
            @break
            @case('danger')
                <i class="fe fe-x-circle mr-2" aria-hidden="true"></i>
            @break
            @case('warning')
                <i class="fe fe-alert-circle mr-2 bg-" aria-hidden="true"></i>
            @break
        @endswitch

        {!! $message !!}
    </div>
@endif