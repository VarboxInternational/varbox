{!! form()->open(['url' => $url, 'method' => 'delete', 'class' => 'd-inline']) !!}
{!! form()->button('<i class="fe fe-trash text-red"></i>', ['type' => 'submit', 'class' => 'button-delete btn icon d-inline bg-white px-0', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Delete'] + ($attributes ? (array)$attributes : [])) !!}
{!! form()->close() !!}