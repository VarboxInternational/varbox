var init = {
    Uploader: function (exists, container, oldIndex, newIndex) {
        window.__UploaderIndex = 1 + Math.floor(Math.random() * 999999);

        container.find('.js-UploadNewOpenBtn').each(function (i, _container) {
            $(_container).attr('id', $(_container).attr('id').replace(/[0-9]+/g, window.__UploaderIndex));
            $(_container).attr('data-target', $(_container).attr('data-target').replace(/[0-9]+/g, window.__UploaderIndex));
        });

        container.find('.js-UploadNewModal').each(function (i, _container) {
            $(_container).attr('id', $(_container).attr('id').replace(/[0-9]+/g, window.__UploaderIndex));
            $(_container).attr('data-index', $(_container).attr('data-index').replace(/[0-9]+/g, window.__UploaderIndex));
        });

        container.find('.js-UploadNewModal .js-UploadTab').each(function (i, _container) {
            $(_container).attr('id', $(_container).attr('id').replace(/[0-9]+/g, window.__UploaderIndex));
        });

        container.find('.js-UploadNewModal .js-UploadTabBtn').each(function (i, _container) {
            $(_container).attr('href', $(_container).attr('href').replace(/[0-9]+/g, window.__UploaderIndex));
        });

        container.find('.js-UploadInput').each(function (i, _container) {
            $(_container).attr('class', $(_container).attr('class').replace(/[0-9]+/g, window.__UploaderIndex));
        });

        if (exists) {
            container.find('.js-UploadNewBtn').each(function (i, _container) {
                $(_container).removeClass('w-100').addClass('w-50 border-right-0');
            });

            container.find('.js-UploadNewModal, .js-UploadCurrentModal').each(function (index, _container) {
                if ($(_container).attr('data-field')) {
                    $(_container).attr('data-field', $(_container).attr('data-field').replace(oldIndex, newIndex));
                }

                if ($(_container).attr('data-index')) {
                    $(_container).attr('data-index', $(_container).attr('data-index').replace(/[0-9]+/g, window.__UploaderIndex));
                }
            });

            container.find('.js-UploadNewModal').each(function (i, _container) {
                $(_container).attr('data-field', $(_container).attr('data-field').replace(oldIndex, newIndex));
            });

            container.find('.js-UploadOpenCropper').each(function (i, _container) {
                $(_container).attr('class', $(_container).attr('class').replace(oldIndex, newIndex));
            });

            container.find('.js-UploadInput').each(function (index, _container) {
                if ($(_container).attr('name')) {
                    $(_container).attr('name', $(_container).attr('name').replace(oldIndex, newIndex));
                }
            });
        } else {
            container.find('.js-UploadNewBtn').each(function (i, _container) {
                $(_container).removeClass('w-50 border-right-0').addClass('w-100');
            });

            container.find('.js-UploadCurrentOpenBtn, .js-UploadCurrentModal').each(function (i, _container) {
                $(_container).remove();
            });

            container.find('.js-UploadNewModal').each(function (i, _container) {
                $(_container).attr('data-field', $(_container).attr('data-field').replace(/[0-9]+/g, newIndex));
            });

            container.find('.js-UploadInput').each(function (i, _container) {
                $(_container).attr('name', $(_container).attr('name').replace(/[0-9]+/g, newIndex)).val('');
            });
        }
    },
    Editor: function () {
        var editor = new FroalaEditor('textarea.editor-input', {
            fileUploadMethod: 'POST',
            fileUploadURL: '/froala/upload/file',
            fileUploadParam: 'froala_file',
            fileUploadParams: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            imageUploadMethod: 'POST',
            imageUploadURL: '/froala/upload/image',
            imageUploadParam: 'froala_image',
            imageUploadParams: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            videoUploadMethod: 'POST',
            videoUploadURL: '/froala/upload/video',
            videoUploadParam: 'froala_video',
            videoUploadParams: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            events: {
                'file.error': function (error, response) {
                    var errorMessage;

                    if (response) {
                        errorMessage = response;
                    } else if (error.message) {
                        errorMessage = error.message;
                    }

                    if (errorMessage) {
                        editor.popups.get('file.insert')
                            .find('.fr-file-progress-bar-layer')
                            .find('h3')
                            .text(errorMessage);
                    }
                },
                'image.error': function (error, response) {
                    var errorMessage;

                    if (response) {
                        errorMessage = response;
                    } else if (error.message) {
                        errorMessage = error.message;
                    }

                    if (errorMessage) {
                        editor.popups.get('image.insert')
                            .find('.fr-image-progress-bar-layer')
                            .find('h3')
                            .text(errorMessage);
                    }
                },
                'video.error': function (error, response) {
                    var errorMessage;

                    if (response) {
                        errorMessage = response;
                    } else if (error.message) {
                        errorMessage = error.message;
                    }

                    if (errorMessage) {
                        editor.popups.get('video.insert')
                            .find('.fr-video-progress-bar-layer')
                            .find('h3')
                            .text(errorMessage);
                    }
                }
            },
        });
    },
    Select2: function () {
        $('.select-input').select2({
            theme: "bootstrap",
            width: "100%",
            allowClear: true,
            placeholder: ''
        });
    },
    InputMask: function () {
        $('input[data-mask]').each(function (i, input) {
            $(input).mask($(input).attr('data-mask'), {
                placeholder: $(input).attr('placeholder'),
                clearIfNotMatch: true
            });
        });
    }
};

var query = {
    params: function () {
        var vars = [], hash;
        var hashes = window.location.search ?
            window.location.href.slice(window.location.href.indexOf('?') + 1).split('&') :
            null;

        if (!hashes) {
            return [];
        }

        for (var i = 0; i < hashes.length; i++) {
            hash = hashes[i].split('=');

            vars.push({
                name: hash[0],
                value: hash[1]
            });
        }

        return vars;
    },
    param: function (name) {
        name = name.replace(/[\[\]]/g, "\\$&");

        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(window.location.href);

        if (!results) {
            return null;
        }

        if (!results[2]) {
            return '';
        }

        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }
};

var clipboard = {
    copy: function (text) {
        var target, focus, targetId;

        targetId = "_hiddenCopyText_";
        target = document.getElementById(targetId);

        if (!target) {
            target = document.createElement("textarea");
            target.style.position = "absolute";
            target.style.left = "-9999px";
            target.style.top = "0";
            target.id = targetId;

            document.body.appendChild(target);
        }

        target.textContent = text;
        focus = document.activeElement;

        target.focus();
        target.setSelectionRange(0, target.value.length);

        var succeed;

        try {
            succeed = document.execCommand("copy");
        } catch (e) {
            succeed = false;
        }

        if (focus && typeof focus.focus === "function") {
            focus.focus();
        }

        target.textContent = "";

        return succeed;
    }
};

var disable = {
    form: function () {
        setTimeout(function () {
            //disable normal inputs
            $('form.frm input').attr('disabled', true);
            $('form.frm textarea').attr('disabled', true);
            $('form.frm select').attr('disabled', true);

            //disable uploaders
            $('form.frm .js-UploadNewOpenBtn').addClass('disabled');
            $('form.frm .js-UploadCurrentModal').find('.js-UploadOpenCropper').addClass('disabled');
            $('form.frm .js-UploadCurrentModal').find('.js-UploadDeleteBtn').remove();
            $('form.frm .js-UploadCurrentModal').next('.js-UploadCropContainer').remove();
            $('form.frm .js-UploadNewBtn').addClass('disabled');
            $('form.frm .js-UploadNewModal').remove();

            //disable multiple items buttons
            $('.js-MultipleItemButtons').remove();

            //disable select2
            $('form.frm select.select-input').prop('disabled', true);

            //disable froala editors
            var editor = new FroalaEditor('textarea.editor-input');

            editor.edit.off();
            editor.edit.disableDesign();
        }, 500);
    }
};
