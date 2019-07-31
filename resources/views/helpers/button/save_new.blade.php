<a class="button-save-new btn btn-warning btn-square text-white ml-4" {!! implode(' ', $attributes) !!}>
    <i class="fe fe-plus mr-2"></i>Save & New
</a>

@pushonce('scripts')
    <script>
        $('.button-save-new').click(function (e) {
            e.preventDefault();

            $(this).closest('form').append('<input type="hidden" name="save_stay" value="1" />').submit();
        });
    </script>
@endpushonce
