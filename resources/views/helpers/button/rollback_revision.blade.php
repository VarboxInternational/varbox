{!! form()->open(['url' => $url, 'method' => 'POST', 'class' => 'left']) !!}
{!! form()->button('<i class="fa fa-undo"></i>&nbsp; Rollback', ['type' => 'submit', 'class' => 'btn-rollback-revision btn green', 'onclick' => 'return confirm("Are you sure you want to rollback this revision?")'] + ($attributes ? (array)$attributes : [])) !!}
{!! form()->close() !!}