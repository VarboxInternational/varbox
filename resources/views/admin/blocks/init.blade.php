@extends('varbox::layouts.default')

@section('title', $title)

@section('content')
    {!! form_admin()->open(['class' => 'frm row row-cards']) !!}
    <div class="col-12">
        <div class="card">
            <div class="card-status bg-blue"></div>
            <div class="card-header">
                <h3 class="card-title">Basic Info</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        {!! form_admin()->select('type', 'Type', ['' => 'Please select'] + $types, null, ['required']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="js-BlockImage col-12 text-center">
        <div class="card">
            <div class="card-status bg-green"></div>
            <div class="card-header">
                <h3 class="card-title">Preview Info</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 text-center">
                        <img class="rounded mx-auto d-bock" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex text-left">
                    @include('varbox::buttons.cancel', ['url' => route('admin.blocks.index')])

                    <a class="js-BlockContinueButton btn btn-primary btn-square text-white ml-4">
                        <i class="fe fe-arrow-right mr-2"></i>Continue
                    </a>
                </div>
            </div>
        </div>
    </div>
    {!! form()->close() !!}
@endsection

@push('scripts')
    <script type="text/javascript">
        $(function () {
            selectType();

            select.change(function () {
                selectType();
            });
        });

        let images = @json($images),
            select = $('select[name="type"]'),
            container = $('.js-BlockImage'),
            button = $('.js-BlockContinueButton');

        let selectType = function () {
            if (select.val()) {
                container.show();
                container.find('img').attr('src', '{{ asset('/') }}' + images[select.val()]).show();
                button.attr('href', '{{ route('admin.blocks.create') }}' + '/' + select.val());
            } else {
                container.hide();
                container.find('img').attr('src', '').hide();
                button.attr('href', '#');
            }
        };
    </script>
@endpush
