<div class="card">
    <div class="card-header" data-toggle="card-collapse" style="cursor: pointer;">
        <h3 class="card-title">Tree View</h3>
        <div class="card-options">
            <a href="#" class="card-options-collapse"><i class="fe fe-chevron-up"></i></a>
        </div>
    </div>
    <div class="card-body px-4 js-TreeRecords"></div>
    <div class="card-footer">
        <span class="text-muted">Something wrong in tree?</span><br />
        {!! form()->open(['url' => route('admin.menus.tree.fix'), 'method' => 'PUT']) !!}
        {!! form()->submit('Fix it now!', ['class' => 'confirm-are-you-sure btn btn-link p-0 border-0']) !!}
        {!! form()->close() !!}
    </div>
</div>

@push('scripts')
    <script type="text/javascript">
        let recordsTree = $(".js-TreeRecords");

        let treeContainerSelector = '.js-TreeContainer',
            treeTableSelector = '.js-TreeTable';

        let treeCreateUrl = '{{ route('admin.menus.create', ['location' => $location]) }}',
            treeLoadUrl = '{{ route('admin.menus.tree.load', ['location' => $location]) }}',
            treeListUrl = '{{ route('admin.menus.tree.list', ['location' => $location]) }}',
            treeSortUrl = '{{ route('admin.menus.tree.sort') }}';

        $(function () {
            load(recordsTree);
            list(recordsTree);
            move(recordsTree);
        });

        let load = function (tree) {
            setTimeout(function () {
                tree.jstree({
                    "core" : {
                        'themes': {
                            'name': 'proton',
                            'responsive': true
                        },
                        "check_callback" : function(operation, node, node_parent, node_position, more) {
                            if (operation === "move_node") {
                                return (node.id != "id" && node_parent.id != "#");
                            }

                            return true;
                        },
                        'data': {
                            'url' : function(node) {
                                return treeLoadUrl + (parseInt(node.id) ? "/" + node.id : '');
                            }
                        }
                    },
                    "state" : { "key" : "state_key" },
                    "plugins" : ["dnd", "state", "types"],
                });
            }, 500);
        }, list = function (tree) {
            tree.on('select_node.jstree', function (e, data) {
                var node = data.instance.get_node(data.selected);
                var request = {};

                $.each(query.params(), function (index, obj) {
                    request[obj.name] = obj.value.split('+').join(' ');
                });

                $(treeTableSelector).css({opacity: 0.5});
                $('.button-add').attr('href', treeCreateUrl);

                $.ajax({
                    url: treeListUrl + "/" + (parseInt(node.id) ? node.id : ''),
                    type: 'GET',
                    data: request,
                    success: function(data){
                        $('.button-add').attr('href', $('.button-add').attr('href') + '/' + (parseInt(node.id) ? node.id : ''));
                        $(treeContainerSelector).html(data);
                        $(treeTableSelector).css({opacity: 1});

                        sort();

                        init.Tooltip();
                        init.Bootbox(treeContainerSelector);
                    }
                });
            });
        }, move = function (tree) {
            tree.on('move_node.jstree', function (e, data) {
                var _tree = tree.jstree().get_json();
                var _node = data.node;
                var _data = {
                    node: parseInt(_node.id) ? _node.id : '',
                    children: _node.children,
                    parent: parseInt(data.parent) ? data.parent : '',
                    old_parent: parseInt(data.old_parent) ? data.old_parent : ''
                };

                $.ajax({
                    url: treeSortUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}',
                        tree: _tree
                    }
                });
            });
        };
    </script>
@endpush
