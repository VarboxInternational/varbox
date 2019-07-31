<a data-url="{{ $url }}" class="button-save-draft btn btn-danger btn-square text-white ml-4" {!! implode(' ', $attributes) !!}>
    <i class="fe fe-paperclip mr-2"></i>Save As Draft
</a>

@pushonce('scripts')
    <script>
        $('.button-save-draft').click(function (e) {
            e.preventDefault();

            var _this = $(this);

            bootbox.confirm({
                message: "Are you sure?",
                buttons: {
                    cancel: {
                        label: 'No',
                        className: 'btn-secondary btn-default btn-square px-5 mr-auto'
                    },
                    confirm: {
                        label: 'Yes',
                        className: 'btn-primary btn-square px-5'
                    }
                },
                callback: function (result) {
                    if (result === true) {
                        var form = _this.closest('form');

                        form.validate().settings.ignore = "*";
                        form.find('input[name="_method"]').remove();
                        form.attr('action', _this.data('url')).attr('method', 'POST').submit();
                    }
                }
            });
        });
    </script>
@endpushonce
