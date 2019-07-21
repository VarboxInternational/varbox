@push('scripts')
    <script type="text/javascript">
        window.__UploaderIndex = '{{ $index }}';

        $(function () {
            var page = 2,
                token = '{{ csrf_token() }}';

            var uploadLoad = function (_this) {
                var popup = _this.next('.upload-new'),
                    tab = popup.find('.tab-content').find('.tab-pane.active'),
                    list = popup.find('.nav-tabs').find('.nav-link.active'),
                    container = tab.find('.modal-items'),
                    keyword = tab.find('input[type="search"]').val(),
                    type = list.data('type'),
                    accept = list.data('accept'),
                    url = '{{ route('admin.uploads.get') }}';

                $.ajax({
                    type: 'GET',
                    url: url + '/' + type,
                    dataType: 'json',
                    data: {
                        _token : token,
                        accept: accept,
                        keyword: keyword
                    },
                    beforeSend:function(){
                        popup.show();
                        container.hide();
                    },
                    complete:function(){
                        container.slideDown(300);
                    },
                    success: function(data) {
                        page = 2;
                        container.html(data.html);
                        $('[data-toggle="tooltip"]').tooltip();
                    }
                });
            }, uploadSwitch = function (_this) {
                var popup = _this.closest('.upload-new'),
                    type = _this.data('type'),
                    accept = _this.data('accept'),
                    index = popup.data('index'),
                    tab = popup.find('.tab-content').find('.tab-pane#' + type + '-' + index),
                    container = tab.find('.modal-items'),
                    keyword = tab.find('input[type="search"]').val(),
                    url = '{{ route('admin.uploads.get') }}';

                $.ajax({
                    type: 'GET',
                    url: url + '/' + type,
                    dataType: 'json',
                    data: {
                        _token : token,
                        accept: accept,
                        keyword: keyword
                    },
                    beforeSend:function(){
                        popup.show();
                        container.hide();
                    },
                    complete:function(){
                        container.slideDown(300);
                    },
                    success: function(data) {
                        page = 2;
                        container.html(data.html);
                        $('[data-toggle="tooltip"]').tooltip();
                    }
                });
            }, uploadScroll = function (_this) {
                var tab = _this.find('.tab-content').find('.tab-pane.active'),
                    list = _this.find('.nav-tabs').find('.nav-link.active'),
                    container = tab.find('.modal-items'),
                    keyword = tab.find('input[type="search"]').val(),
                    type = list.data('type'),
                    accept = list.data('accept'),
                    url = '{{ route('admin.uploads.get') }}';

                if(container.scrollTop() + container.innerHeight() >= container[0].scrollHeight) {
                    $.ajax({
                        type : 'GET',
                        url: url + '/' + type + '?page=' + page,
                        data: {
                            _token: token,
                            accept: accept,
                            keyword: keyword
                        },
                        success : function(data) {
                            page += 1;
                            container.append(data.html);
                            $('[data-toggle="tooltip"]').tooltip();
                        }
                    });
                }
            }, uploadSearch = function (_this) {
                var tab = _this.find('.tab-content').find('.tab-pane.active'),
                    list = _this.find('.nav-tabs').find('.nav-link.active'),
                    container = tab.find('.modal-items'),
                    keyword = tab.find('input[type="search"]').val(),
                    type = list.data('type'),
                    accept = list.data('accept'),
                    url = '{{ route('admin.uploads.get') }}',
                    timer;

                clearInterval(timer);

                timer = setTimeout(function(){
                    $.ajax({
                        type: 'GET',
                        url: url + '/' + type,
                        dataType: 'json',
                        data: {
                            _token : token,
                            accept: accept,
                            keyword : keyword
                        },
                        success: function(data) {
                            page = 2;
                            container.html(data.html);
                            $('[data-toggle="tooltip"]').tooltip();
                        }
                    });
                }, 300);
            }, uploadUpload = function (_this) {
                var index = _this.data('index'),
                    list = _this.find('.nav-tabs').find('.nav-link.active'),
                    accept = list.data('accept');

                _this.fileupload({
                    url: '{{ route('admin.uploads.upload') }}',
                    dataType: 'json',
                    formData: {
                        _token : token,
                        model: _this.data('model'),
                        field: _this.data('field'),
                        accept: accept
                    },
                    done: function (e, data) {
                        var message = _this.find('.upload-message');

                        _this.find('.modal-items').css('opacity', '1');
                        _this.find('.upload-btn').removeClass('btn-loading');

                        if (data.result.status === true) {
                            _this.find('#' + data.result.type + '-' + index).find('.modal-items').prepend(data.result.html);
                            _this.find('#' + data.result.type + '-' + index).find('.modal-items > p').remove();

                            _this.find('.tab-pane').find('.modal-items .btn-upload-select').removeClass('selected');
                            _this.find('#' + data.result.type + '-' + index).find('.modal-items .btn-upload-select').first().addClass('selected');

                            if (_this.find('#' + data.result.type + '-' + index).find('.modal-items .btn-upload-select').first().parent().parent().is('tr')) {
                                $('.upload-items-table tr').removeClass('hovered');
                                _this.find('#' + data.result.type + '-' + index).find('.modal-items .btn-upload-select').first().parent().parent().addClass('hovered');
                            }

                            message.text(data.result.message).removeClass('text-red').addClass('text-green');

                            $('[data-toggle="tooltip"]').tooltip();
                        } else {
                            message.text(data.result.message).removeClass('text-green').addClass('text-red');
                        }

                        setTimeout(function(){
                            message.text('');
                        }, 5000);
                    },
                    progressall: function (e, data) {
                        _this.find('.modal-items').css('opacity', '0.5');
                        _this.find('.upload-btn').addClass('btn-loading');
                    }
                });
            }, uploadSave = function (_this) {
                var tab = _this.find('.tab-content').find('.tab-pane.active'),
                    container = tab.find('.modal-items'),
                    model = _this.data('model'),
                    field = _this.data('field'),
                    path = container.find('.btn-upload-select.selected').data('path'),
                    url = "{{ route('admin.uploads.set') }}";

                $.ajax({
                    type: 'POST',
                    url: url,
                    dataType: 'json',
                    data: {
                        _token : token,
                        path: path,
                        model: model,
                        field: field
                    },
                    beforeSend: function () {
                        _this.find('.modal-items').css('opacity', '0.5');
                        _this.find('.btn-upload-save').addClass('btn-loading');
                    },
                    complete: function () {
                        _this.find('.modal-items').css('opacity', '1');
                        _this.find('.btn-upload-save').removeClass('btn-loading');
                    },
                    success: function(data) {``
                        var input = _this.closest('.form-group').find('.upload-input'),
                            button = _this.prev('.open-upload-new'),
                            message = _this.find('.upload-message');

                        if (data.status === true) {
                            input.val(data.path);
                            button.html(data.name);
                            $(_this).modal('hide');
                        } else {
                            message.text(data.message).removeClass('text-green').addClass('text-red');

                            setTimeout(function(){
                                message.text('');
                            }, 5000);
                        }
                    }
                });
            }, uploadRemove = function (_this) {
                var index = _this.closest('.upload-current').data('index');

                $('#upload-input-' + index).val('');
                $('#open-upload-current-' + index).remove();
                $('#open-upload-new-' + index).removeClass('w-50').removeClass('border-right-0').addClass('w-100');
                $('.upload-current').modal('hide');
            }, uploadCrop = function (_this) {
                var popup = _this.closest('.upload-current'),
                    url = _this.data('url'),
                    path = _this.data('path'),
                    style = _this.data('style'),
                    model = popup.data('model'),
                    field = popup.data('field'),
                    index = popup.data('index');

                $.ajax({
                    type: 'GET',
                    url: '{{ route('admin.uploads.crop') }}',
                    dataType: 'json',
                    data: {
                        _token : token,
                        index: index,
                        model: model,
                        field: field,
                        url: url,
                        path: path,
                        style: style
                    },
                    success: function(data) {
                        if (data.status === true) {
                            $('#upload-crop-container-' + index).html(data.html);
                            $('#upload-crop-' + index).modal('show');
                        }
                    }
                });
            };

            //initial load
            $(document).on('click', '.open-upload-new:not(.disabled)', function (e) {
                e.preventDefault();

                uploadLoad($(this));
            });

            //click load
            $(document).on('click', '.upload-new:not(.disabled) .nav-tabs .btn-upload-switch', function(e) {
                uploadSwitch($(this));
            });

            //scroll load
            document.addEventListener('scroll', function (event) {
                if (event.target.classList && event.target.classList.contains('modal-items')) {
                    uploadScroll($(event.target).closest('.upload-new'));
                }
            }, true);

            //search load
            $(document).on('keyup', '.upload-new:not(.disabled) .tab-pane.active input[type="search"]', function(e) {
                e.preventDefault();

                uploadSearch($(this).closest('.upload-new'));
            });

            //upload new
            $(document).on('click', '.upload-new:not(.disabled) .upload-btn > input[type="file"]', function (e) {
                uploadUpload($(this).closest('.upload-new'));
            });

            //save new
            $(document).on('click', '.upload-new:not(.disabled) .btn-upload-save', function(e) {
                e.preventDefault();

                uploadSave($(this).closest('.upload-new'));
            });

            //cropper load
            $(document).on('click', '.upload-current:not(.disabled) .open-upload-cropper:not(.disabled)', function (e) {
                e.preventDefault();

                uploadCrop($(this));
            });

            //delete current
            $(document).on('click', '.upload-current:not(.disabled) .btn-upload-delete', function(){
                uploadRemove($(this));
            });

            // select file
            $(document).on('click', '.upload-new:not(.disabled) .btn-upload-select', function(e){
                e.preventDefault();

                $('.btn-upload-select').removeClass('selected');
                $(this).addClass('selected');

                if ($(this).parent().parent().is('tr')) {
                    $('.upload-items-table tr').removeClass('hovered');
                    $(this).parent().parent().addClass('hovered');
                }
            });
        });
    </script>
@endpush
