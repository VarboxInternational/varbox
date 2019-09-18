{!! form_admin()->model($item, ['url' => route('admin.analytics.update', $item), 'method' => 'PUT']) !!}
    <div class="card">
        <div class="card-status bg-green"></div>
        <div class="card-header">
            <h3 class="card-title">Analytics Code</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    {!! form_admin()->textarea('code', false) !!}
                    {!! form_admin()->button('<i class="fe fe-check mr-2"></i>Save', ['type' => 'submit', 'class' => 'btn btn-green btn-square']) !!}
                </div>
            </div>
        </div>
    </div>
{!! form_admin()->close() !!}
