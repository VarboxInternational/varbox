@push('scripts')
    <script type="text/javascript">
        let revisionsContainer = $('.revisions-container');

        let listRevisions = function () {
            $.ajax({
                type : 'GET',
                url: '{{ route('admin.revisions.index') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    revisionable_id: revisionsContainer.data('revisionable-id'),
                    revisionable_type: revisionsContainer.data('revisionable-type'),
                    route: '{{ $route }}',
                    parameters: @json($parameters)
                },
                success : function(data) {
                    if (data.status) {
                        revisionsContainer.html(data.html);
                        App.init.Tooltip();
                    }
                },
                error: function (err) {
                    revisionsContainer.html(
                        '<p class="p-5 text-red">Could not load the revisions!</p>'
                    );
                }
            });
        }, rollbackRevision = function (_this) {
            $.ajax({
                type : 'POST',
                url: _this.attr('href'),
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success : function(data) {
                    if (data.status) {
                        window.location.reload();
                    }
                },
                error: function (err) {
                    revisionsContainer.html(
                        '<p class="p-5 text-red">Could not rollback the revision!</p>'
                    );
                }
            });
        }, deleteRevision = function (_this) {
            $.ajax({
                type : 'DELETE',
                url: _this.attr('href'),
                data: {
                    _token: '{{ csrf_token() }}',
                    revisionable_id: revisionsContainer.data('revisionable-id'),
                    revisionable_type: revisionsContainer.data('revisionable-type'),
                    route: '{{ $route }}',
                    parameters: @json($parameters)
                },
                beforeSend: function () {
                    revisionsContainer.css({
                        opacity: 0.5
                    });
                },
                success : function(data) {
                    if (data.status) {
                        setTimeout(function () {
                            revisionsContainer.html(data.html);
                            App.init.Tooltip();

                            revisionsContainer.css({
                                opacity: 1
                            });
                        }, 200);
                    }
                },
                error: function (err) {
                    setTimeout(function () {
                        revisionsContainer.html(
                            '<p class="p-5 text-red">Could not delete the revision!</p>'
                        );

                        revisionsContainer.css({
                            opacity: 1
                        });
                    }, 200);
                }
            });
        };

        $(function () {
            listRevisions();

            $(document).on('click', '.button-rollback-revision', function (e) {
                e.preventDefault();

                let _this = $(this);

                Bootbox.confirm({
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
                            rollbackRevision(_this);
                        }
                    }
                });
            });

            $(document).on('click', '.button-delete-revision', function (e) {
                e.preventDefault();

                let _this = $(this);

                Bootbox.confirm({
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
                            deleteRevision(_this);
                        }
                    }
                });
            });
        });
    </script>
@endpush
