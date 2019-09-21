<?php

namespace Varbox\Composers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\View\View;

class NotificationsComposer
{
    /**
     * @var Authenticatable
     */
    protected $user;

    /**
     * @param Authenticatable $user
     */
    public function __construct(Authenticatable $user)
    {
        $this->user = $user;
    }

    /**
     * Construct the admin menu.
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        $notifications = $this->user->unreadNotifications()->latest();

        $view->with([
            'notifications' => $notifications->take(10)->get(),
            'count' => $notifications->count(),
        ]);
    }
}
