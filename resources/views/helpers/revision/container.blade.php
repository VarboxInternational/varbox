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
