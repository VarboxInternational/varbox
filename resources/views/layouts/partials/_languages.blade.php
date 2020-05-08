@if(defined('IS_TRANSLATABLE') && IS_TRANSLATABLE === true)
    <div class="languages-dropdown dropdown d-flex mr-4 rounded bg-white">
        <a class="nav-link text-dark font-weight-bold" data-toggle="dropdown">
            {{ strtoupper($language->code) }}
        </a>
        @if($languages->count())
            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                @foreach($languages as $language)
                    <a href="{{ route('admin.languages.change', $language->getKey()) }}}" class="dropdown-item d-flex my-2">
                        {{ $language->name }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endif
