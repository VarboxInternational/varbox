@if(isset($revision) && $revision instanceof \Varbox\Contracts\RevisionModelContract)
    @section('top')
        <div class="alert alert-info col-lg-12 mb-5">
            <div class="d-inline-block float-left text-left mx-auto" style="margin-top: 2px;">
                <i class="fe fe-info mr-2" aria-hidden="true"></i>
            </div>
            <div class="d-inline-block">
                <h4>You are currently viewing a revision of the model!</h4>
                <div class="btn-list mt-4">
                    @permission('revisions-rollback')
                    {!! form()->open(['url' => route('admin.revisions.rollback', $revision->getKey()), 'method' => 'POST', 'class' => 'float-left d-inline']) !!}
                    {!! form()->button(' <i class="fe fe-refresh-ccw mr-2"></i>Rollback Revision', ['type' => 'submit', 'class' => 'confirm-are-you-sure btn btn-blue']) !!}
                    {!! form()->close() !!}
                    @endpermission

                    <a href="{{ session('revision_back_url_' . $revision->getKey()) }}" class="btn btn-secondary ml-4">
                        <i class="fe fe-arrow-left mr-2"></i>Back To Original
                    </a>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script type="text/javascript">
            $(function () {
                disable.form();
            });
        </script>

        @php(\Illuminate\Support\Facades\DB::rollBack())

        @if(in_array(\Varbox\Traits\IsCacheable::class, class_uses($revision)))
            @php($revision->clearQueryCache())
        @endif

        @if(in_array(\Varbox\Traits\IsCacheable::class, class_uses($model)))
            @php($model->clearQueryCache())
        @endif
    @endpush
@else
    @permission('revisions-list')
        <div class="col-md-12">
            <div class="card card-collapsed">
                <div class="card-header" data-toggle="card-collapse" style="cursor: pointer;">
                    <h3 class="card-title">Revisions Info</h3>
                    <div class="card-options">
                        <a href="#" class="card-options-collapse"><i class="fe fe-chevron-up"></i></a>
                    </div>
                </div>
                <div class="revisions-container card-body p-0"
                     data-revisionable-id="{{ $model->id }}"
                     data-revisionable-type="{{ get_class($model) }}"
                ></div>
            </div>
        </div>

        @include('varbox::helpers.revision.partials.scripts')
    @endpermission
@endif
