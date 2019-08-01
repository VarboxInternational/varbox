$(document).ready(function () {
    setups();
    sort();
    buttons();
    generators();
    confirms();
});

function setups() {
    init.Editor();
    init.Select2();
}

function sort() {
    var sortField = $('th.sortable');

    sortField.each(function () {
        if (query.param('sort') == $(this).data('sort')) {
            if (query.param('direction') == 'asc') {
                $(this).attr('data-direction', 'desc');
                $(this).find('i').addClass('fa-sort-asc');
            } else {
                $(this).attr('data-direction', 'asc');
                $(this).find('i').addClass('fa-sort-desc');
            }
        }

        if (!$(this).attr('data-direction')) {
            $(this).attr('data-direction', 'asc');
        }
    });


    //create sort full url & redirect
    sortField.click(function () {
        var url = window.location.href.replace('#', '').split('?')[0],
            params = [];

        $.each(query.params(), function (index, obj) {
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
}

function buttons()
{
    $('.button-save-stay, .button-save-new').click(function (e) {
        e.preventDefault();

        $(this).closest('form').append('<input type="hidden" name="save_stay" value="1" />').submit();
    });

    $('.button-save-continue').click(function (e) {
        e.preventDefault();

        var form = $(this).closest('form');

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

    $('.button-save-draft').click(function (e) {
        e.preventDefault();

        var _this = $(this);

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
                    var form = _this.closest('form');

                    form.validate().settings.ignore = "*";
                    form.find('input[name="_method"]').remove();
                    form.attr('action', _this.data('url')).attr('method', 'POST').submit();
                }
            }
        });
    });

    $('.button-duplicate').click(function (e) {
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
                    let form = _this.closest('form');

                    form.validate().settings.ignore = "*";
                    form.find('input[name="_method"]').remove();
                    form.attr('action', _this.data('url')).attr('method', 'POST').submit();
                }
            }
        });
    });
}

function generators() {
    $('.password-generate').pGenerator({
        'bind': 'click',
        'passwordElement': 'input[name="password"]',
        'passwordLength': 10,
        'uppercase': true,
        'lowercase': true,
        'numbers':   true,
        'specialChars': true,
        'onPasswordGenerated': function(generatedPassword) {
            clipboard.copy(generatedPassword);

            $('input[type="password"][name="password_confirmation"]').val(generatedPassword);

            bootbox.alert({
                message: 'Password copied to clipboard: <span class="text-blue font-weight-bold">' + generatedPassword + '</span>',
                backdrop: true
            });
        }
    });
}

function confirms() {
    $('.confirm-are-you-sure').click(function (e) {
        e.preventDefault();

        var _this = $(this);

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
                    _this.closest('form').submit();
                }
            }
        });
    });
}
