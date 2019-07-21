@push('scripts')
    <script type="text/javascript">
        window.__UploaderIndex = '{{ $index }}';

        $(function () {
            var uploadNewOpenButtonSelector = '.js-UploadNewOpenBtn',
                uploadNewModalSelector = '.js-UploadNewModal',
                uploadNewFileButtonSelector = '.js-UploadNewFileBtn',
                uploadNewSaveButtonSelector = '.js-UploadNewSaveBtn',
                uploadNewTabSelector = '.js-UploadTab',
                uploadNewTabButtonSelector = '.js-UploadTabBtn',
                uploadNewTabContainerSelector = '#tab-UploadTab',
                uploadNewMessage = '.js-UploadNewMessage',
                uploadInputSelector = '.js-UploadInput',
                uploadFilesContainerSelector = '.js-UploadFilesContainer',
                uploadFilesTableSelector = '.js-UploadFilesTable',
                uploadSelectButtonSelector = '.js-UploadSelectBtn';

            var page = 2,
                token = '{{ csrf_token() }}';

            var uploadLoad = function (_this) {
                var popup = _this.next(uploadNewModalSelector),
                    tab = popup.find(uploadNewTabSelector + '.active'),
                    list = popup.find(uploadNewTabButtonSelector + '.active'),
                    container = tab.find(uploadFilesContainerSelector),
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
                var popup = _this.closest(uploadNewModalSelector),
                    type = _this.data('type'),
                    accept = _this.data('accept'),
                    index = popup.data('index'),
                    tab = popup.find('.tab-content').find(uploadNewTabContainerSelector + '-' + type + '-' + index),
                    container = tab.find(uploadFilesContainerSelector),
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
                var tab = _this.find(uploadNewTabSelector + '.active'),
                    list = _this.find(uploadNewTabButtonSelector + '.active'),
                    container = tab.find(uploadFilesContainerSelector),
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
                var tab = _this.find(uploadNewTabSelector + '.active'),
                    list = _this.find(uploadNewTabButtonSelector + '.active'),
                    container = tab.find(uploadFilesContainerSelector),
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
                    list = _this.find(uploadNewTabButtonSelector + '.active'),
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
                        var message = _this.find(uploadNewMessage);

                        _this.find(uploadFilesContainerSelector).css('opacity', '1');
                        _this.find(uploadNewFileButtonSelector).removeClass('btn-loading');

                        if (data.result.status === true) {
                            _this.find(uploadNewTabContainerSelector + '-' + data.result.type + '-' + index).find(uploadFilesContainerSelector).prepend(data.result.html);
                            _this.find(uploadNewTabContainerSelector + '-' + data.result.type + '-' + index).find(uploadFilesContainerSelector + ' > p').remove();

                            _this.find(uploadNewTabSelector).find(uploadFilesContainerSelector + ' ' + uploadSelectButtonSelector).removeClass('selected');
                            _this.find(uploadNewTabContainerSelector + '-' + data.result.type + '-' + index).find(uploadFilesContainerSelector + ' ' + uploadSelectButtonSelector).first().addClass('selected');

                            if (_this.find(uploadNewTabContainerSelector + '-' + data.result.type + '-' + index).find(uploadFilesContainerSelector + ' ' + uploadSelectButtonSelector).first().parent().parent().is('tr')) {
                                $(uploadFilesTableSelector + ' tr').removeClass('hovered');
                                _this.find(uploadNewTabContainerSelector + '-' + data.result.type + '-' + index).find(uploadFilesContainerSelector + ' ' + uploadSelectButtonSelector).first().parent().parent().addClass('hovered');
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
                        _this.find(uploadFilesContainerSelector).css('opacity', '0.5');
                        _this.find(uploadNewFileButtonSelector).addClass('btn-loading');
                    }
                });
            }, uploadSave = function (_this) {
                var tab = _this.find(uploadNewTabSelector + '.active'),
                    container = tab.find(uploadFilesContainerSelector),
                    model = _this.data('model'),
                    field = _this.data('field'),
                    path = container.find(uploadSelectButtonSelector + '.selected').data('path'),
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
                        _this.find(uploadFilesContainerSelector).css('opacity', '0.5');
                        _this.find(uploadNewSaveButtonSelector).addClass('btn-loading');
                    },
                    complete: function () {
                        _this.find(uploadFilesContainerSelector).css('opacity', '1');
                        _this.find(uploadNewSaveButtonSelector).removeClass('btn-loading');
                    },
                    success: function(data) {
                        var input = _this.closest('.form-group').find(uploadInputSelector),
                            button = _this.prev(uploadNewOpenButtonSelector),
                            message = _this.find(uploadNewMessage);

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

                $(uploadInputSelector + '-' + index).val('');
                $('#open-upload-current-' + index).remove();
                $(uploadNewOpenButtonSelector + '-' + index).removeClass('w-50').removeClass('border-right-0').addClass('w-100');
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
            $(document).on('click', uploadNewOpenButtonSelector + ':not(.disabled)', function (e) {
                e.preventDefault();

                uploadLoad($(this));
            });

            //click load
            $(document).on('click', uploadNewModalSelector + ' ' + uploadNewTabButtonSelector, function(e) {
                uploadSwitch($(this));
            });

            //scroll load
            document.addEventListener('scroll', function (event) {
                if (event.target.classList && event.target.classList.contains('js-UploadFilesContainer')) {
                    uploadScroll($(event.target).closest(uploadNewModalSelector));
                }
            }, true);

            //search load
            $(document).on('keyup', uploadNewModalSelector + ' ' + uploadNewTabSelector + '.active input[type="search"]', function(e) {
                e.preventDefault();

                uploadSearch($(this).closest(uploadNewModalSelector));
            });

            //upload new
            $(document).on('click', uploadNewModalSelector + ' ' + uploadNewFileButtonSelector + ' > input[type="file"]', function (e) {
                uploadUpload($(this).closest(uploadNewModalSelector));
            });

            //save new
            $(document).on('click', uploadNewModalSelector + ' ' + uploadNewSaveButtonSelector, function(e) {
                e.preventDefault();

                uploadSave($(this).closest(uploadNewModalSelector));
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
            $(document).on('click', uploadNewModalSelector + ' ' + uploadSelectButtonSelector, function(e){
                e.preventDefault();

                $(uploadSelectButtonSelector).removeClass('selected');
                $(this).addClass('selected');

                if ($(this).parent().parent().is('tr')) {
                    $(uploadFilesTableSelector + ' tr').removeClass('hovered');
                    $(this).parent().parent().addClass('hovered');
                }
            });
        });
    </script>
@endpush
