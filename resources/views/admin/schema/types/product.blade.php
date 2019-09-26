<div class="d-flex">
    <a href="{{ route('admin.schema.create', ['type' => $type]) }}" class="stamp stamp-md bg-blue text-white mr-3">
        <i class="fa fa-shopping-cart"></i>
    </a>
    <div>
        <h4 class="m-0">
            <a href="{{ route('admin.schema.create', ['type' => $type]) }}">
                {{ $label ?: 'N/A' }}
            </a>
        </h4>
        <small class="text-muted">
            Any offered product or service. (e.g. a pair of shoes, a concert ticket)
        </small>
    </div>
</div>
