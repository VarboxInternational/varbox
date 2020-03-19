window.$ = window.jQuery = require('jquery');
window.Dropzone = require('dropzone');
window.Quill = require('quill');
window.Bootbox = require('bootbox');

require('bootstrap');
require('select2');
require('jstree');
require('pgenerator');
require('tablednd/js/jquery.tablednd');
require('jquery-mask-plugin');
require('jcrop-0.9.12');
require('blueimp-file-upload');

import { ImageUpload } from 'quill-image-upload';

$(document).ready(function () {
    App.bootstrap
        .Tooltip()
        .Popover()
        .Cards();

    App.init
        .Editor()
        .Select2();

    App.table
        .Sort()
        .Order();

    App.button
        .SaveAndStay()
        .SaveAndNew()
        .SaveAndContinue()
        .SaveAsDraft()
        .SaveAsDuplicate()
        .Preview();

    App.slug
        .FromToInputs();

    App.generate
        .Passwords();

    App.confirm
        .AreYouSure();
});

window.App = {
    bootstrap: {
        Tooltip: function () {
            $('[data-toggle="tooltip"]').tooltip();

            return this;
        },
        Popover: function () {
            $('[data-toggle="popover"]').popover({
                html: true
            });

            return this;
        },
        Cards: function () {
            $('[data-toggle="card-remove"]').on('click', function(e) {
                $(this).closest('.card').remove();

                e.preventDefault();
                return false;
            });

            $('[data-toggle="card-collapse"]').on('click', function(e) {
                $(this).closest('.card').toggleClass('card-collapsed');

                e.preventDefault();
                return false;
            });

            $('[data-toggle="card-fullscreen"]').on('click', function(e) {
                $(this).closest('.card').toggleClass('card-fullscreen').removeClass('card-collapsed');

                e.preventDefault();
                return false;
            });

            return this;
        },
        Editor: function (elem, options) {
            let Size = Quill.import('attributors/style/size');

            Size.whitelist = [
                false, '12px', '14px', '16px', '18px', '20px', '22px', '24px'
            ];

            Quill.register('modules/imageUpload', ImageUpload);
            Quill.register(Size, true);

            let editorElements, elementType, elementValue, editorDiv, editorPlaceholder, defaultOptions;

            if(elem) {
                editorElements = Array.prototype.slice.call(document.querySelectorAll(elem));
            } else {
                editorElements = Array.prototype.slice.call(document.querySelectorAll('[data-quilljs]'));
            }

            editorElements.forEach(function(el) {
                if(elem && el.hasAttribute("data-quilljs")) {
                    return;
                }

                elementType = el.type;

                if(elementType == 'textarea') {
                    elementValue = el.value;
                    editorDiv = document.createElement('div');
                    editorDiv.innerHTML = elementValue;
                    el.parentNode.insertBefore(editorDiv, el.nextSibling);
                    el.style.display = "none";
                    editorPlaceholder = el.placeholder;
                } else {
                    editorPlaceholder = null;
                    editorDiv = el;
                }

                if(!options) {
                    defaultOptions = {
                        theme: 'snow',
                        placeholder: editorPlaceholder,
                    };
                } else {
                    if(!options.placeholder) {
                        options.placeholder = editorPlaceholder;
                    }

                    defaultOptions = options;
                }

                window.QuillEditor = new Quill(editorDiv, defaultOptions);
                window.QuillEditor.on('text-change', function(delta, oldDelta, source) {
                    el.value = window.QuillEditor.root.innerHTML;
                });
            });
        }
    },
    init: {
        Editor: function () {
            App.bootstrap.Editor('.editor-input', {
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'size': [false, '12px', '14px', '16px', '18px', '20px', '22px', '24px'] }],
                        [{'list': 'ordered'}, { 'list': 'bullet' }],
                        [{'header': [1, 2, 3, 4, 5, 6, false] }],
                        [{'color': [] }, { 'background': [] }],
                        [{'align': [] }],
                        ['link', 'image', 'video']
                    ],
                    imageUpload: {
                        url: '/wysiwyg/upload-image',
                        method: 'POST',
                        name: 'wysiwyg_image',
                        csrf: {
                            token: '_token',
                            hash: $('meta[name="csrf-token"]').attr('content')
                        },
                        callbackKO: serverError => {
                            Bootbox.alert(serverError.body.replace (/(^")|("$)/g, ''));
                        }
                    }
                },
                theme: 'snow',
            });

            return this;
        },
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

            return this;
        },
        Select2: function () {
            $('.select-input').select2({
                theme: "bootstrap",
                width: "100%",
                allowClear: true,
                placeholder: ''
            });

            return this;
        },
        Bootbox: function (selector) {
            $(selector).find('.confirm-are-you-sure').on('click', function (e) {
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
                            _this.closest('form').submit();
                        }
                    }
                });
            });

            return this;
        },
        InputMask: function () {
            $('input[data-mask]').each(function (i, input) {
                $(input).mask($(input).attr('data-mask'), {
                    placeholder: $(input).attr('placeholder'),
                    clearIfNotMatch: true
                });
            });

            return this;
        },
        Tooltip: function () {
            $('[data-toggle="tooltip"]').tooltip();

            return this;
        }
    },
    query: {
        Params: function () {
            let vars = [],
                hash,
                hashes = window.location.search ?
                    window.location.href.slice(window.location.href.indexOf('?') + 1).split('&') :
                    null;

            if (!hashes) {
                return [];
            }

            for (let i = 0; i < hashes.length; i++) {
                hash = hashes[i].split('=');

                vars.push({
                    name: hash[0],
                    value: hash[1]
                });
            }

            return vars;
        },
        Param: function (name) {
            name = name.replace(/[\[\]]/g, "\\$&");

            let regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(window.location.href);

            if (!results) {
                return null;
            }

            if (!results[2]) {
                return '';
            }

            return decodeURIComponent(results[2].replace(/\+/g, " "));
        }
    },
    form: {
        Disable: function () {
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

                //disable quill editors
                window.QuillEditor.disable();
            }, 500);
        }
    },
    table: {
        Sort: function () {
            let field = $('th.sortable');

            field.each(function () {
                if (App.query.Param('sort') == $(this).data('sort')) {
                    if (App.query.Param('direction') == 'asc') {
                        $(this).attr('data-direction', 'desc');
                        $(this).find('i').addClass('fa-sort-up');
                    } else {
                        $(this).attr('data-direction', 'asc');
                        $(this).find('i').addClass('fa-sort-down');
                    }
                }

                if (!$(this).attr('data-direction')) {
                    $(this).attr('data-direction', 'asc');
                }
            });

            //create sort full url & redirect
            field.click(function () {
                let url = window.location.href.replace('#', '').split('?')[0],
                    params = [];

                $.each(App.query.Params(), function (index, obj) {
                    if (obj.name == 'sort' || obj.name == 'direction') {
                        return true;
                    }

                    params.push(obj);
                });

                params.push({
                    name: 'sort',
                    value: $(this).data('sort')
                });

                params.push({
                    name: 'direction',
                    value: $(this).data('direction') ? $(this).data('direction') : 'asc'
                });

                window.location.href = url + '?' + decodeURIComponent($.param(params));
            });

            return this;
        },
        Order: function () {
            $('table[data-orderable="true"]').tableDnD({
                onDrop: function(table, row){
                    let rows = table.tBodies[0].rows,
                        items = {};

                    for (let i = 0; i < rows.length; i++) {
                        items[i + 1] = rows[i].id;
                    }

                    $.ajax({
                        type: 'PATCH',
                        url: $(table).data('order-url'),
                        data: {
                            _method: 'PATCH',
                            _token: $(table).data('order-token'),
                            model: $(table).data('order-model'),
                            items: items
                        }
                    });
                }
            });

            return this;
        }
    },
    button: {
        SaveAndStay: function () {
            $('.button-save-stay').click(function (e) {
                e.preventDefault();

                $(this).closest('form')
                    .append('<input type="hidden" name="save_stay" value="1" />')
                    .submit();
            });

            return this;
        },
        SaveAndNew: function () {
            $('.button-save-new').click(function (e) {
                e.preventDefault();

                $(this).closest('form')
                    .append('<input type="hidden" name="save_stay" value="1" />')
                    .submit();
            });

            return this;
        },
        SaveAndContinue: function () {
            $('.button-save-continue').click(function (e) {
                e.preventDefault();

                let form = $(this).closest('form');

                if ($(this).data('route-parameters')) {
                    $.each($(this).data('route-parameters'), function (key, value) {
                        form.append('<input type="hidden" name="save_continue_route_parameters[' + key + ']" value="' + value + '" />')
                    })
                }

                form
                    .append('<input type="hidden" name="save_continue" value="1" />')
                    .append('<input type="hidden" name="save_continue_route" value="' + $(this).data('route-name') + '" />')
                    .submit();
            });

            return this;
        },
        SaveAsDraft: function () {
            $('.button-save-draft').click(function (e) {
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
                            let form = _this.closest('form');

                            //form.validate().settings.ignore = "*";
                            form.find('input[name="_method"]').remove();
                            form.attr('action', _this.data('url')).attr('method', 'POST').submit();
                        }
                    }
                });
            });

            return this;
        },
        SaveAsDuplicate: function () {
            $('.button-duplicate').click(function (e) {
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
                            let form = _this.closest('form');

                            //form.validate().settings.ignore = "*";
                            form.find('input[name="_method"]').remove();
                            form.attr('action', _this.data('url')).attr('method', 'POST').submit();
                        }
                    }
                });
            });

            return this;
        },
        Preview: function () {
            $('.button-preview').click(function () {
                let form = $(this).closest('form'),
                    action = $(form).attr('action'),
                    input = form.find('input[name="_method"]'),
                    method = input.val();

                input.remove();
                //form.validate().settings.ignore = "*";
                form.attr('action', $(this).data('url')).attr('method', 'POST').attr('target', '_blank').submit();

                setTimeout(function () {
                    $(form).attr('action', action).removeAttr('target')
                        .append('<input type="hidden" name="_method" value="' + method + '" />');
                }, 1000);
            });

            return this;
        }
    },
    slug: {
        FromToInputs: function () {
            let from = $('.js-SlugFrom'),
                to = $('.js-SlugTo');

            if (from.length && to.length) {
                from.bind('keyup blur', function() {
                    to.val(
                        $(this).val().toString().toLowerCase()
                            .replace(/\s+/g, '-')
                            .replace(/[^\w\-]+/g, '')
                            .replace(/\-\-+/g, '-')
                            .replace(/^-+/, '')
                            .replace(/-+$/, '')
                    );
                });
            }

            return this;
        }
    },
    generate: {
        Passwords: function() {
            $('.password-generate').pGenerator({
                'bind': 'click',
                'passwordElement': 'input[name="password"]',
                'passwordLength': 10,
                'uppercase': true,
                'lowercase': true,
                'numbers':   true,
                'specialChars': true,
                'onPasswordGenerated': function(generatedPassword) {
                    App.clipboard.Copy(generatedPassword);

                    $('input[type="password"][name="password_confirmation"]').val(generatedPassword);

                    Bootbox.alert({
                        message: 'Password copied to clipboard: <span class="text-blue font-weight-bold">' + generatedPassword + '</span>',
                        backdrop: true
                    });
                }
            });

            return this;
        }
    },
    confirm: {
        AreYouSure: function () {
            $('.confirm-are-you-sure').click(function (e) {
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
                            _this.closest('form').submit();
                        }
                    }
                });
            });

            return this;
        }
    },
    clipboard: {
        Copy: function (text) {
            let target, focus, targetId;

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

            let succeed;

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
    }
};
