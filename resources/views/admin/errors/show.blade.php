@extends('varbox::layouts.admin.default')

@section('title', $title)

@section('content')
    <div class="frm row row-cards">
        <div class="col-md-6">
            <div class="card">
                <div class="card-status bg-blue"></div>
                <div class="card-header">
                    <h3 class="card-title">Basic Info</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            {!! form_admin()->text('type', 'Type', $item->type ?: 'N/A', ['disabled' => 'disabled']) !!}
                            {!! form_admin()->text('url', 'Url', $item->url ?: 'N/A', ['disabled' => 'disabled']) !!}
                        </div>
                        <div class="col-md-6">
                            {!! form_admin()->text('code', 'Code', $item->code ?? 'N/A', ['disabled' => 'disabled']) !!}
                        </div>
                        <div class="col-md-6">
                            {!! form_admin()->text('occurrences', 'Occurrences', $item->occurrences ?: 'N/A', ['disabled' => 'disabled']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-status bg-green"></div>
                <div class="card-header">
                    <h3 class="card-title">Message Info</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            {!! form_admin()->textarea('message', 'Message', $item->message ?: 'N/A', ['disabled' => 'disabled', 'style' => 'height: 200px;']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if($item->file || $item->line || $item->trace)
        <div class="col-md-12">
            <div class="card">
                <div class="card-status bg-red"></div>
                <div class="card-header">
                    <h3 class="card-title">Trace Info</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            @if($item->file || $item->line)
                                Error occured in <strong>{{ $item->file ?: 'N/A' }}</strong> on line <strong>{{ $item->line ?: 'N/A' }}</strong>
                            @endif
                            <hr>
                            @if($item->trace)
                                <strong class="text-red">Trace:</strong><br /><br />
                                {!!  str_replace("\n", "<br /><br />", $item->trace)  !!}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex text-left">
                        {!! button()->goBack(route('admin.errors.index')) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection