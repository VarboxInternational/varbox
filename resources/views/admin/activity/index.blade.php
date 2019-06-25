@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    {!! form()->open(['url' => route('admin.activity.clean'), 'method' => 'DELETE']) !!}
                    {!! form()->button('<i class="fe fe-trash mr-2"></i>Delete Old Activity', ['type' => 'submit', 'class' => 'button-delete-old-activity btn btn-yellow btn-square btn-block mb-5', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Older than ' . $days . ' ' . \Illuminate\Support\Str::plural('day', $days)]) !!}
                    {!! form()->close() !!}

                    {!! form()->open(['url' => route('admin.activity.delete'), 'method' => 'DELETE']) !!}
                    {!! form()->button('<i class="fe fe-trash-2 mr-2"></i>Delete All Activity', ['type' => 'submit', 'class' => 'button-delete-all-activity btn btn-red btn-square btn-block']) !!}
                    {!! form()->close() !!}
                </div>
            </div>

            @include('varbox::admin.activity._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.activity._table')

            {!! pagination('admin')->render($items) !!}
        </div>
    </div>
@endsection

@section('bottom_scripts')
    <script>
        $('.button-delete-old-activity, .button-delete-all-activity').click(function (e) {
            e.preventDefault();

            var _this = $(this);

            bootbox.confirm({
                message: "Are you sure you want to delete the records?",
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
    </script>
@append