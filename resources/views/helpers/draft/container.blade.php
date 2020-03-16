@if($model->isDrafted())
    @php($showPublishButton = !empty($permission) ? auth()->user()->isSuper() || auth()->user()->hasPermission($permission) : true)

    @section('top')
        <div class="alert alert-info col-lg-12 mb-5">
            <div class="d-inline-block float-left text-left mx-auto" style="margin-top: 2px;">
                <i class="fe fe-info mr-2" aria-hidden="true"></i>
            </div>
            <div class="d-inline-block">
                <h4>This record is currently drafted!</h4>
                <p @if(!$showPublishButton) class="mb-0" @endif>
                    Please note that if you have un-saved changes,
                    you will have to save them before publishing the draft for them to be persisted.
                </p>
                @if($showPublishButton)
                    <div class="btn-list mt-4">
                        {!! form()->open(['url' => route($route, $model->getKey()), 'method' => 'PUT', 'class' => 'float-left d-inline']) !!}
                        {!! form()->button(' <i class="fe fe-check mr-2"></i>Publish Draft', ['type' => 'submit', 'class' => 'confirm-are-you-sure btn btn-blue']) !!}
                        {!! form()->close() !!}
                    </div>
                @endif
            </div>
        </div>
    @append
@endif
