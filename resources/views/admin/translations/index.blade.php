@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('top')
    <div class="alert alert-info col-lg-12 mb-5">
        <div class="d-inline-block float-left text-left mx-auto" style="margin-top: 2px;">
            <i class="fe fe-info mr-2" aria-hidden="true"></i>
        </div>
        <div class="d-inline-block">
            <h4>Important</h4>
            <p class="mb-0">
                Before changing anything, first <strong>import</strong> your translations, to ensure you see the currently used ones.<br />
                After you have changed any translations, <strong>export</strong> them to actually propagate your changes.<br />
            </p>
        </div>
    </div>
@endsection

@section('content')
    <div class="row row-cards">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    @permission('translations-import')
                        {!! form()->open(['url' => route('admin.translations.import'), 'method' => 'POST']) !!}
                        {!! form()->button('<i class="fe fe-download mr-2"></i>&nbsp; Import Translations', ['type' => 'submit', 'class' => 'confirm-are-you-sure btn btn-blue btn-square btn-block text-left']) !!}
                        {!! form()->close() !!}
                    @endpermission
                    @permission('translations-export')
                        {!! form()->open(['url' => route('admin.translations.export'), 'method' => 'POST']) !!}
                        {!! form()->button('<i class="fe fe-upload mr-2"></i>&nbsp; Export Translations', ['type' => 'submit', 'class' => 'confirm-are-you-sure btn btn-green btn-square btn-block mt-5 text-left']) !!}
                        {!! form()->close() !!}
                    @endpermission
                    @permission('translations-delete')
                        {!! form()->open(['url' => route('admin.translations.clear'), 'method' => 'DELETE']) !!}
                        {!! form()->button('<i class="fe fe-trash-2 mr-2"></i>&nbsp; Remove All Translations', ['type' => 'submit', 'class' => 'confirm-are-you-sure btn btn-red btn-square btn-block mt-5 text-left']) !!}
                        {!! form()->close() !!}
                    @endpermission
                </div>
            </div>

            @include('varbox::admin.translations._filter')
        </div>
        <div class="col-lg-9">
            @include('varbox::admin.translations._table')

            {!! $items->links('varbox::pagination', request()->query()) !!}
        </div>
    </div>
@endsection
