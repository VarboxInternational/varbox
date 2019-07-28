@section('top')
    <div class="alert alert-info col-lg-12 mb-5">
        <div class="d-inline-block float-left text-left mx-auto" style="margin-top: 2px;">
            <i class="fe fe-info mr-2" aria-hidden="true"></i>
        </div>
        <div class="d-inline-block">
            <h4>You are currently viewing a revision of the model!</h4>
            <p>
                "Rollback Revision" will populate the original record with the details below.<br />
                "View Original" will redirect you to the latest version of this record.
            </p>
            <div class="btn-list">
                @permission('revisions-rollback')
                {!! form()->open(['url' => route('admin.revisions.rollback', $revision->getKey()), 'method' => 'POST', 'class' => 'float-left d-inline']) !!}
                <button type="submit" class="button-rollback-revision btn btn-blue">
                    <i class="fe fe-refresh-ccw mr-2"></i>Rollback Revision
                </button>
                {!! form()->close() !!}
                @endpermission

                <a href="{{ session('revision_back_url_' . $revision->getKey()) }}" class="btn btn-secondary ml-4">
                    <i class="fe fe-eye mr-2"></i>View Original
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(function () {
            disable.form();

            $('.button-rollback-revision').click(function (e) {
                e.preventDefault();

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
                            _this.closest('form').submit();
                        }
                    }
                });
            });
        });
    </script>
@endpush

@php
    \Illuminate\Support\Facades\DB::rollBack();

    if (@array_key_exists(\Varbox\Traits\IsCacheable::class, class_uses($revision))) {
        $revision->clearQueryCache();
    }

    if (@array_key_exists(\Varbox\Traits\IsCacheable::class, class_uses($model))) {
        $model->clearQueryCache();
    }
@endphp
