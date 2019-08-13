<div class="notifications-dropdown dropdown d-flex">
    <a class="nav-link icon" data-toggle="dropdown">
        <i class="fe fe-bell text-white"></i>
        <span class="nav @if($count > 0) nav-unread @endif"></span>
    </a>
    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
        @forelse($notifications as $notification)
            <a href="{{ route('admin.notifications.action', $notification->id) }}" class="dropdown-item d-flex my-2">
                <div>
                    {{ Str::limit(strip_tags($notification->data['subject'] ?? 'N/A'), 50) }}
                    <div class="small text-muted">
                        {{ Carbon\Carbon::parse($notification->created_at)->diffForHumans()}}
                    </div>
                </div>
            </a>
        @empty
            <span class="dropdown-item-text d-flex" style="color: #6e7687;">
                No new notifications
            </span>
        @endforelse
        <div class="dropdown-divider"></div>
        <a href="{{ route('admin.notifications.index') }}" class="dropdown-item text-center text-muted-dark">
            See All Notifications
        </a>
    </div>
</div>
