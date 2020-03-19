@push('scripts')
    <script type="text/javascript">
        let blocksContainerSelector = '.js-BlocksContainer',
            locationContainerSelector = '.js-BlocksLocationContainer',
            tableSelector = '.js-BlocksTable',
            tableTemplateSelector = '#js-BlocksTableTemplate',
            requestSelector = '.js-BlocksRequest',
            requestTemplateSelector = '#js-BlocksRequestTemplate',
            emptyRowSelector = '.js-BlocksEmpty',
            emptyRowTemplateSelector = '#js-BlocksEmptyTemplate';

        let listBlocks = function () {
            $.ajax({
                type : 'GET',
                url: '{{ route('admin.blocks.get') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    blockable_id: @json($model->getKey()),
                    blockable_type: @json($model->getMorphClass()),
                    revision: @json($revision ? $revision->getKey() : null),
                    disabled: @json($disabled)
                },
                success : function(data) {
                    if (data.status == true) {
                        $(blocksContainerSelector).html(data.html);

                        orderBlocks();

                        App.init.Select2().Tooltip();
                    }
                },
                error: function (err) {
                    $(blocksContainerSelector).html(
                        '<p class="p-5 text-red">Could not load the blocks!</p>'
                    );
                }
            });
        }, assignBlock = function (_this) {
            let container = _this.closest(locationContainerSelector),
                table = container.find(tableSelector),
                select = container.find('select');

            if (select.val()) {
                $.ajax({
                    type : 'POST',
                    url: '{{ route('admin.blocks.row') }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        block_id: select.val()
                    },
                    beforeSend: function () {
                        container.css({
                            opacity: 0.5
                        });
                    },
                    success : function(data) {
                        if (data.status == true) {
                            setTimeout(function () {
                                table.find(emptyRowSelector).remove();
                                table.find('tbody').append(
                                    $(tableTemplateSelector).html()
                                        .replace(/#index#/g, parseInt(getLastBlockIndex()) + 1)
                                        .replace(/#block_id#/g, data.data.id)
                                        .replace(/#block_name#/g, data.data.name)
                                        .replace(/#block_type#/g, data.data.type)
                                        .replace(/#block_url#/g, data.data.url)
                                );

                                $(requestSelector).append(
                                    $(requestTemplateSelector).html()
                                        .replace(/#index#/g, parseInt(getLastBlockIndex()) + 1)
                                        .replace(/#block_id#/g, data.data.id)
                                        .replace(/#block_location#/g, container.data('location'))
                                        .replace(/#block_ord#/g, table.find('tbody > tr').length)
                                );

                                orderBlocks();

                                App.init.Tooltip();

                                container.css({
                                    opacity: 1
                                });
                            }, 200);
                        }
                    }
                });
            }
        }, unassignBlock = function (_this) {
            let container = _this.closest(locationContainerSelector),
                table = _this.closest('table'),
                row = _this.closest('tr'),
                input = $(requestSelector).find('input[data-index="' + row.data('index') + '"]');

            container.css({
                opacity: 0.5
            });

            setTimeout(function () {
                var count = table.find('tbody > tr').length;

                $('[data-toggle="tooltip"]').tooltip('hide');

                input.remove();
                row.remove();

                if (count <= 1) {
                    table.find('tbody').append(
                        $(emptyRowTemplateSelector).html()
                    );
                }

                App.init.Tooltip();

                orderBlocks();

                container.css({
                    opacity: 1
                });
            }, 200);
        }, orderBlocks = function () {
            $(tableSelector).tableDnD({
                onDrop: function(table, row){
                    let rows = table.tBodies[0].rows;

                    $(rows).each(function (index, selector) {
                        let rowIndex = $(selector).attr('data-index'),
                            rowId = $(selector).attr('data-block-id');

                        $('input[name="blocks[' + rowIndex + '][' + rowId + '][ord]"]').val(index + 1);
                    });
                }
            });
        }, getLastBlockIndex = function () {
            let inputs = $(requestSelector).find('input'),
                max = 0;

            inputs.each(function (index, selector) {
                if ($(selector).attr('data-index') > max) {
                    max = $(selector).attr('data-index');
                }
            });

            return max;
        };

        $(function () {
            listBlocks();

            $(document).on('click', '.button-assign-block', function (e) {
                e.preventDefault();

                assignBlock($(this));
            });

            $(document).on('click', '.button-unassign-block:not(.disabled)', function (e) {
                e.preventDefault();

                unassignBlock($(this));
            });
        });
    </script>
@endpush
