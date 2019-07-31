<a class="button-save-continue btn btn-success btn-square text-white ml-4" data-route-name="{{ $routeName }}" data-route-parameters=@json($routeParameters) {!! implode(' ', $attributes) !!}>
    <i class="fe fe-arrow-right mr-2"></i>Save & Continue
</a>

@pushonce('scripts')
    <script>
        $('.button-save-continue').click(function (e) {
            e.preventDefault();

            var form = $(this).closest('form');

            if ($(this).data('route-parameters')) {
                $.each($(this).data('route-parameters'), function (key, value) {
                    form.append('<input type="hidden" name="save_continue_route_parameters[' + key + ']" value="' + value + '" />')
                })
            }

            form
                .append('<input type="hidden" name="save_continue" value="1" />')
                .append('<input type="hidden" name="save_continue_route" value="' + $(this).data('route-name') + '" />')
                .submit();
        });
    </script>
@endpushonce
