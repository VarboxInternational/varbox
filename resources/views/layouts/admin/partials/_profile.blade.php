<div class="dropdown">
    <a href="#" class="nav-link pr-0 leading-none" data-toggle="dropdown">
        <span class="avatar bg-white text-dark"><i class="fe fe-user"></i></span>
        <span class="ml-2 d-none d-lg-block">
            <span class="text-white font-weight-bold">{{ auth()->user()->full_name }}</span>
            <small class="text-muted d-block mt-1">Administrator</small>
        </span>
    </a>
    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
        <a href="{{ route('admin.admins.edit', auth()->id()) }}" class="dropdown-item" href="#">
            <i class="dropdown-icon fe fe-user mr-2"></i>My Profile
        </a>
        <div class="dropdown-divider"></div>
        {!! form()->open(['url' => route('admin.logout'), 'method' => 'post']) !!}
        {!! form()->button('<i class="dropdown-icon fe fe-log-out mr-2"></i>Sign out', ['type' => 'submit', 'class' => 'dropdown-item', 'style' => 'cursor: pointer;']) !!}
        {!! form()->close() !!}
    </div>
</div>