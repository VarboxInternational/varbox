@section('bottom_scripts')
    <script type="text/javascript">
        function initTooltip() {
            $('.tooltip').tooltipster({
                theme: 'tooltipster-punk'
            });
        }

        window.__UploaderIndex = '{{ $index }}';

        $(function () {
            var page = 2,
                token = '{{ csrf_token() }}';

            var uploadLoad = function (_this) {
                var popup = _this.next('.upload-new'),
                    tab = popup.find('.modal-tab.active'),
                    list = popup.find('ul.modal-tabs').find('li.active'),
                    container = tab.find('div.modal-items'),
                    keyword = tab.find('input.search').val(),
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
                        initTooltip();
                    }
                });
            }, uploadScroll = function (_this) {
                var tab = _this.find('.modal-tab.active'),
                    list = _this.find('ul.modal-tabs').find('li.active'),
                    container = tab.find('div.modal-items'),
                    keyword = tab.find('input.search').val(),
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
                            initTooltip();
                        }
                    });
                }
            }, uploadSearch = function (_this) {
                var tab = _this.find('.modal-tab.active'),
                    list = _this.find('ul.modal-tabs').find('li.active'),
                    container = tab.find('div.modal-items'),
                    keyword = tab.find('input.search').val(),
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
                            initTooltip();
                        }
                    });
                }, 300);
            }, uploadUpload = function (_this) {
                var list = _this.find('ul.modal-tabs').find('li.active'),
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
                        var message = _this.find('span.upload-message'),
                            progress = _this.find('.progress');

                        _this.find('.loading').fadeOut(300);

                        if (data.result.status === true) {
                            _this.find('#' + data.result.type).find('.modal-items').prepend(data.result.html);
                            _this.find('#' + data.result.type).find('.modal-items > p').remove();

                            _this.find('.modal-tab').find('.modal-items > a').removeClass('active');
                            _this.find('#' + data.result.type).find('.modal-items > a:first-of-type').addClass('active');

                            message.text(data.result.message).removeClass('error').addClass('success');
                            progress.find('.bar').removeClass('error').addClass('success');

                            initTooltip();
                        } else {
                            message.text(data.result.message).removeClass('success').addClass('error');
                            progress.find('.bar').removeClass('success').addClass('error');
                        }

                        setTimeout(function(){
                            progress.find('.bar').css('width', '0px').removeClass('success').removeClass('error');
                            message.text('');
                            progress.slideUp(500);
                        }, 5000);
                    },
                    progressall: function (e, data) {
                        var progress = parseInt(data.loaded / data.total * 100, 10);

                        _this.find('.loading').fadeIn(300);
                        _this.find('.loading').show();
                        _this.find('.progress .bar').css('width', progress + '%');
                    }
                });
            }, uploadSave = function (_this) {
                var tab = _this.find('.modal-tab.active'),
                    container = tab.find('div.modal-items'),
                    model = _this.data('model'),
                    field = _this.data('field'),
                    path = container.find('a.active').data('path'),
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
                        _this.find('.loading').fadeIn(300);
                    },
                    complete: function () {
                        _this.find('.loading').fadeOut(300);
                    },
                    success: function(data) {
                        var input = _this.closest('.field-wrapper').next('.upload-input'),
                            button = _this.prev('.open-upload-new'),
                            message = _this.find('span.upload-message');

                        if (data.status === true) {
                            input.val(data.path);
                            button.html(data.name);
                            $('.popup:visible').hide();
                        } else {
                            message.text(data.message).css('display', 'block').removeClass('success').addClass('error');

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
                $('#open-upload-new-' + index).removeClass('half').addClass('full');
                $('.popup:visible').hide();
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
                            $('#upload-crop-' + index).show();
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
            $(document).on('click', '.upload-new:not(.disabled) ul.modal-tabs > li', function(e) {
                e.preventDefault();

                $(this).closest('ul.modal-tabs').find('li').removeClass('active');
                $(this).addClass('active');

                uploadLoad($(this).closest('.upload-new:not(.disabled)').prev('.open-upload-new'));
            });

            //scroll load
            document.addEventListener('scroll', function (event) {
                if (event.target.classList.contains('uploads')) {
                    uploadScroll($(event.target).closest('.upload-new'));
                }
            }, true);

            //search load
            $(document).on('keyup', '.upload-new:not(.disabled) .modal-tab.active input.search', function(e) {
                e.preventDefault();

                uploadSearch($(this).closest('.upload-new'));
            });

            //upload new
            $(document).on('click', '.upload-new:not(.disabled) label.upload-btn > input[type="file"]', function (e) {
                uploadUpload($(this).closest('.upload-new'));
            });

            //save new
            $(document).on('click', '.upload-new .upload-save', function(e) {
                e.preventDefault();

                uploadSave($(this).closest('.upload-new'));
            });

            //cropper load
            $(document).on('click', '.open-upload-cropper:not(.disabled)', function (e) {
                e.preventDefault();

                uploadCrop($(this));
            });

            //delete current
            $(document).on('click', '.upload-current:not(.disabled) .upload-delete', function(){
                uploadRemove($(this));
            });
        });
    </script>
@append
