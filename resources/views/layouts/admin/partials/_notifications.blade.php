@if(\Varbox::moduleEnabled('audit'))
    <div class="dropdown d-none d-md-flex">
        <a class="nav-link icon" data-toggle="dropdown">
            <i class="fe fe-bell text-white"></i>
            <span class="nav {{--nav-unread--}}"></span>
        </a>
        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
            <a href="#" class="dropdown-item d-flex">
                <span class="avatar avatar-blue mr-3 align-self-center">AC</span>
                <div>
                    <strong>Nathan</strong> pushed new commit: Fix page load performance issue.
                    <div class="small text-muted">10 minutes ago</div>
                </div>
            </a>
            <a href="#" class="dropdown-item d-flex">
                <span class="avatar avatar-azure mr-3 align-self-center">AB</span>
                <div>
                    <strong>Alice</strong> started new task: Tabler UI design.
                    <div class="small text-muted">1 hour ago</div>
                </div>
            </a>
            <a href="#" class="dropdown-item d-flex">
                <span class="avatar mr-3 align-self-center"></span>
                <div>
                    <strong>Rose</strong> deployed new version of NodeJS REST Api V3
                    <div class="small text-muted">2 hours ago</div>
                </div>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item text-center text-muted-dark">See all notifications</a>
        </div>
    </div>
@endif