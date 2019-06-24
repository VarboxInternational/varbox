{!! form()->open(['url' => $url, 'method' => 'POST']) !!}
{!! form()->hidden('_class', get_class($model)) !!}
{!! form()->hidden('_id', $model->id) !!}
{!! form()->button('<i class="fa fa-check-square-o"></i>&nbsp; Publish', ['type' => 'submit', 'class' => 'btn-publish-limbo-draft btn blue no-margin-top no-margin-bottom no-margin-left double-margin-right', 'onclick' => 'return confirm("Are you sure you want to publish this draft?")'] + ($attributes ? (array)$attributes : [])) !!}
{!! form()->close() !!}