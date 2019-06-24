{!! form()->open(['url' => $url, 'method' => 'PUT']) !!}
{!! form()->button('<i class="fa fa-undo"></i>&nbsp; Restore', ['type' => 'submit', 'class' => 'btn-restore-record btn green no-margin-top no-margin-bottom no-margin-left', 'onclick' => 'return confirm("Are you sure you want to restore this record?")'] + ($attributes ? (array)$attributes : [])) !!}
{!! form()->close() !!}