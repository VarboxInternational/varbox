@permission('blocks-show')
    <div class="col-md-12">
        <div class="card card-collapsed">
            <div class="card-header blocks-list-header" data-toggle="card-collapse" style="cursor: pointer;">
                <h3 class="card-title">Blocks Info</h3>
                <div class="card-options">
                    <a href="#" class="card-options-collapse"><i class="fe fe-chevron-up"></i></a>
                </div>
            </div>
            <div class="alert alert-warning m-5">
                <div class="d-inline-block float-left text-left mx-auto" style="margin-top: 2px;">
                    <i class="fe fe-alert-circle mr-2" aria-hidden="true"></i>
                </div>
                <div class="d-inline-block">
                    <p class="mb-0">
                        In order for the block operations (<strong>add</strong> / <strong>remove</strong> / <strong>order</strong>) to be persisted,
                        you'll have to <strong>save</strong> the record afterwards.
                    </p>
                </div>
            </div>
            <div class="js-BlocksContainer card-body p-0"></div>
        </div>
    </div>

    @include('varbox::helpers.block.templates.row')
    @include('varbox::helpers.block.templates.empty')
    @include('varbox::helpers.block.templates.data')
    @include('varbox::helpers.block.partials.scripts')

    @if($revision)
        @php(\Illuminate\Support\Facades\DB::rollBack())
    @endif
@endpermission
