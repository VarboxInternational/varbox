<ul class="nav nav-tabs border-0 flex-column flex-lg-row px-3">
    @foreach($menu->roots() as $item)
        @php($children = $menu->children($item))
        <li class="nav-item px-0 d-sm-flex">
            <a href="{!! $item->url() ?: 'javascript:void(0)' !!}" class="nav-link px-3 w-100 {!! $item->active() ? 'active' : '' !!}" {!! $children->count() ? 'data-toggle="dropdown"' : '' !!}>
                <i class="fa {!! $item->data('icon') !!}"></i>&nbsp; {{ $item->name() }}
            </a>
            @if($children->count())
                <div class="dropdown-menu dropdown-menu-arrow">
                    @foreach($children as $child)
                        <a href="{!! $child->url() ?: '#' !!}" class="dropdown-item ">
                            {{ $child->name() }}
                        </a>
                    @endforeach
                </div>
            @endif
        </li>
    @endforeach
</ul>