@php($parameters = isset($parameters) ? $parameters : [])

<a class="button-save-continue btn btn-success btn-square text-white ml-4" data-route-name="{{ $route }}" data-route-parameters=@json($parameters){!! isset($attributes) ? implode(' ', $attributes) : '' !!}>
    <i class="fe fe-arrow-right mr-2"></i>Save & Continue
</a>
