<a data-url="{{ $url }}" class="button-duplicate btn btn-yellow btn-square text-white ml-4" {!! implode(' ', $attributes) !!}>
    <i class="fe fe-copy mr-2"></i>Duplicate
</a>

@pushonce('scripts')
    <script>
        $('.button-duplicate').click(function (e) {
            let _this = $(this);

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
                        let form = _this.closest('form');

                        form.validate().settings.ignore = "*";
                        form.find('input[name="_method"]').remove();
                        form.attr('action', _this.data('url')).attr('method', 'POST').submit();
                    }
                }
            });
        });
    </script>
@endpushonce
