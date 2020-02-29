$(document).ready(function () {
    tabler();
    setups();
    sort();
    order();
    buttons();
    sluggify();
    generators();
    confirms();
});

function tabler() {
    const CARD = '.card';

    $('[data-toggle="tooltip"]').tooltip();

    $('[data-toggle="popover"]').popover({
        html: true
    });

    $('[data-toggle="card-remove"]').on('click', function(e) {
        let $card = $(this).closest(CARD);

        $card.remove();

        e.preventDefault();
        return false;
    });

    $('[data-toggle="card-collapse"]').on('click', function(e) {
        let $card = $(this).closest(CARD);

        $card.toggleClass('card-collapsed');

        e.preventDefault();
        return false;
    });

    $('[data-toggle="card-fullscreen"]').on('click', function(e) {
        let $card = $(this).closest(CARD);

        $card.toggleClass('card-fullscreen').removeClass('card-collapsed');

        e.preventDefault();
        return false;
    });
}

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

function order() {
    $('table[data-orderable="true"]').tableDnD({
        onDrop: function(table, row){
            var rows = table.tBodies[0].rows,
                items = {};

            for (var i = 0; i < rows.length; i++) {
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

    $('.button-preview').click(function () {
        var form = $(this).closest('form'),
            action = $(form).attr('action'),
            input = form.find('input[name="_method"]'),
            method = input.val();

        input.remove();
        form.validate().settings.ignore = "*";
        form.attr('action', $(this).data('url')).attr('method', 'POST').attr('target', '_blank').submit();

        setTimeout(function () {
            $(form).attr('action', action).removeAttr('target')
                .append('<input type="hidden" name="_method" value="' + method + '" />');
        }, 1000);
    });
}

function sluggify()
{
    var from = $('.js-SlugFrom');
    var to = $('.js-SlugTo');

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
