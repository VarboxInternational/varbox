{!! form()->open(['url' => $url, 'method' => 'DELETE']) !!}
{!! form()->hidden('_class', get_class($model)) !!}
{!! form()->hidden('_id', $model->id) !!}
{!! form()->button('<i class="fa fa-times"></i>&nbsp; Delete', ['type' => 'submit', 'class' => 'btn-delete-record btn red no-margin-top no-margin-bottom no-margin-right', 'onclick' => 'return confirm("Are you sure you want to delete this draft?")']) !!}
{!! form()->close() !!}