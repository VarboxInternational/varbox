@push('scripts')
    <script type="text/javascript">
        var _CSRFToken = '{{ csrf_token() }}';
        var revisionsContainer = $('.revisions-container');

        var listRevisions = function () {
            $.ajax({
                type : 'GET',
                url: '{{ route('admin.revisions.index') }}',
                data: {
                    _token: _CSRFToken,
                    revisionable_id: revisionsContainer.data('revisionable-id'),
                    revisionable_type: revisionsContainer.data('revisionable-type'),
                    route: '{{ $routeName }}',
                    parameters: @json($routeParameters)
                },
                success : function(data) {
                    if (data.status) {
                        revisionsContainer.html(data.html);

                        $('[data-toggle="tooltip"]').tooltip();
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
                    _token: _CSRFToken
                },
                success : function(data) {
                    if (data.status) {
                        location.reload();
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
                    _token: _CSRFToken,
                    revisionable_id: revisionsContainer.data('revisionable-id'),
                    revisionable_type: revisionsContainer.data('revisionable-type'),
                    route: '{{ $routeName }}',
                    parameters: @json($routeParameters)
                },
                success : function(data) {
                    if (data.status) {
                        revisionsContainer.html(data.html);

                        $('[data-toggle="tooltip"]').tooltip();
                    }
                },
                error: function (err) {
                    revisionsContainer.html(
                        '<p class="p-5 text-red">Could not delete the revision!</p>'
                    );
                }
            });
        };

        $(function () {
            listRevisions();

            $(document).on('click', '.button-rollback-revision', function (e) {
                e.preventDefault();

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
                            rollbackRevision(_this);
                        }
                    }
                });
            });

            $(document).on('click', '.button-delete-revision', function (e) {
                e.preventDefault();

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
                            deleteRevision(_this);
                        }
                    }
                });
            });
        });
    </script>
@endpush