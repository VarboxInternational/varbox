@section('bottom_scripts')
    <script type="text/javascript">
        var token = '{{ csrf_token() }}';
        var blocksTab = $('div#tab-blocks');
        var blocksContainer = $('div.blocks-container');

        var listBlocks = function () {
            $.ajax({
                type : 'GET',
                url: '{{ route('admin.blocks.get') }}',
                data: {
                    _token: token,
                    blockable_id: blocksContainer.data('blockable-id'),
                    blockable_type: blocksContainer.data('blockable-type'),
                    draft: blocksContainer.data('draft'),
                    revision: blocksContainer.data('revision'),
                    disabled: blocksContainer.data('disabled')
                },
                beforeSend: function () {
                    blocksContainer.hide();
                    blocksTab.find('.loading').fadeIn(300);
                },
                complete: function () {
                    blocksTab.find('.loading').fadeOut(300);

                    setTimeout(function () {
                        blocksContainer.fadeIn(300);
                    }, 300);
                },
                success : function(data) {
                    if (data.status == true) {
                        blocksContainer.html(data.html);

                        initBlockSelect();
                        orderBlocks();
                    } else {
                        blocksTab.hide();
                        init.FlashMessage('error', 'Could not load the blocks! Please try again.');
                    }
                },
                error: function (err) {
                    blocksTab.hide();
                    init.FlashMessage('error', 'Could not load the blocks! Please try again.');
                }
            });
        }, assignBlock = function (_this) {
            var container = _this.closest('.blocks-location-container');
            var table = container.find('.blocks-table');
            var select = container.find('.block-assign-select');

            if (select.val()) {
                $.ajax({
                    type : 'POST',
                    url: '{{ route('admin.blocks.row') }}',
                    data: {
                        _token: token,
                        block_id: select.val()
                    },
                    beforeSend: function () {
                        container.css({opacity: 0.5});
                    },
                    complete: function () {
                        container.css({opacity: 1});
                    },
                    success : function(data) {
                        if (data.status == true) {
                            table.find('tr.no-blocks-assigned').remove();
                            table.find('tbody').append(
                                $('#block-row-template').html()
                                    .replace(/#index#/g, parseInt(getLastIndex()) + 1)
                                    .replace(/#block_id#/g, data.data.id)
                                    .replace(/#block_name#/g, data.data.name)
                                    .replace(/#block_type#/g, data.data.type)
                                    .replace(/#block_url#/g, data.data.url)
                            );

                            $('.blocks-request').append(
                                $('#block-request-template').html()
                                    .replace(/#index#/g, parseInt(getLastIndex()) + 1)
                                    .replace(/#block_id#/g, data.data.id)
                                    .replace(/#block_location#/g, container.data('location'))
                                    .replace(/#block_ord#/g, table.find('tbody > tr').length)
                            );

                            orderBlocks();
                        } else {
                            init.FlashMessage('error', 'Could not assign the block! Please try again.');
                        }
                    },
                    error: function (err) {
                        init.FlashMessage('error', 'Could not assign the block! Please try again.');
                    }
                });
            }
        }, unassignBlock = function (_this) {
            var container = _this.closest('.blocks-location-container');
            var table = _this.closest('table');
            var row = _this.closest('tr');
            var input = $('input.block-input[data-index="' + row.data('index') + '"]');

            container.css({
                opacity: 0.5
            });

            setTimeout(function () {
                var count = table.find('tbody > tr').length;

                input.remove();
                row.remove();

                if (count <= 1) {
                    table.find('tbody').append(
                        $('#no-block-row-template').html()
                    );
                }

                container.css({
                    opacity: 1
                });

                orderBlocks();
            }, 250);
        }, orderBlocks = function () {
            $(".blocks-table").tableDnD({
                onDrop: function(table, row){
                    var rows = table.tBodies[0].rows;

                    $(rows).each(function (index, selector) {
                        $('input[name="blocks[' + $(selector).attr('data-index') + '][' + $(selector).attr('data-block-id') + '][ord]"]').val(index + 1);
                    });
                }
            });
        }, getLastIndex = function () {
            var inputs = $('.blocks-request').find('input.block-input');
            var max = 0;

            inputs.each(function (index, selector) {
                if ($(selector).attr('data-index') > max) {
                    max = $(selector).attr('data-index');
                }
            });

            return max;
        }, initBlockSelect = function () {
            $('.block-assign-select').chosen({
                width: '100%',
                inherit_select_classes: true
            });
        };

        $(function () {
            listBlocks();

            $(document).on('click', 'a.block-assign', function (e) {
                e.preventDefault();

                assignBlock($(this));
            });

            $(document).on('click', 'a.block-unassign:not(.disabled)', function (e) {
                e.preventDefault();

                unassignBlock($(this));
            });
        });
    </script>
@append