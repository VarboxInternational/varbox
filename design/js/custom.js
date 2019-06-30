$(document).ready(function () {
    setups();
    buttons();
    sort();
    generators();
    confirms();
});

function setups() {
    init.Editor();
    init.Select2();
}

function buttons() {
    $(document).on('click', '.button-save-stay, .button-save-new', function (e) {
        e.preventDefault();

        $('form.row.row-cards').append('<input type="hidden" name="save_stay" value="1" />').submit();
    });

    $(document).on('click', '.button-save-continue', function (e) {
        e.preventDefault();

        if ($(this).data('route-parameters')) {
            $.each($(this).data('route-parameters'), function (key, value) {
                $('form.row.row-cards').append('<input type="hidden" name="save_continue_route_parameters[' + key + ']" value="' + value + '" />')
            })
        }

        $('form.row.row-cards')
            .append('<input type="hidden" name="save_continue" value="1" />')
            .append('<input type="hidden" name="save_continue_route" value="' + $(this).data('route-name') + '" />')
            .submit();
    });
}

function sort() {
    var sortField = $('th.sortable');

    //initialize sort headings display
    sortField.each(function () {
        if (query.param('sort') == $(this).data('sort')) {
            if (query.param('dir') == 'asc') {
                $(this).attr('data-dir', 'desc');
                $(this).find('i').addClass('fa-sort-asc');
            } else {
                $(this).attr('data-dir', 'asc');
                $(this).find('i').addClass('fa-sort-desc');
            }
        }

        if (!$(this).attr('data-dir')) {
            $(this).attr('data-dir', 'asc');
        }
    });


    //create sort full url & redirect
    sortField.click(function () {
        var url = window.location.href.replace('#', '').split('?')[0],
            params = [];

        $.each(query.params(), function (index, obj) {
            if (obj.name == 'sort' || obj.name == 'dir') {
                return true;
            }

            params.push(obj);
        });

        params.push({
            name: 'sort',
            value: $(this).data('sort')
        });

        params.push({
            name: 'dir',
            value: $(this).data('dir') ? $(this).data('dir') : 'asc'
        });

        window.location.href = url + '?' + decodeURIComponent($.param(params));
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