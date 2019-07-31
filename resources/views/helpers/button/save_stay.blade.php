<a class="button-save-stay btn btn-success btn-square text-white ml-4" {!! implode(' ', $attributes) !!}>
    <i class="fe fe-map-pin mr-2"></i>Save & Stay
</a>

@pushonce('scripts')
    <script>
        $('.button-save-stay').click(function (e) {
            e.preventDefault();

            $(this).closest('form').append('<input type="hidden" name="save_stay" value="1" />').submit();
        });
    </script>
@endpushonce
