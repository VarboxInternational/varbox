{!! form()->open(['url' => $url, 'method' => 'PUT', 'class' => 'd-inline']) !!}
{!! form()->button('<i class="fe fe-repeat text-green"></i>', ['type' => 'submit', 'class' => 'button-restore confirm-are-you-sure btn icon d-inline bg-white px-0', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Restore'] + ($attributes ? (array)$attributes : [])) !!}
{!! form()->close() !!}