<?php

namespace Varbox\Composers;

use Illuminate\View\View;

class NotificationsComposer
{
    /**
     * Construct the admin menu.
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        if (!auth()->check()) {
            return;
        }

        $notifications = auth()->user()->unreadNotifications();

        $view->with([
            'notifications' => $notifications->take(10)->get(),
            'count' => $notifications->count(),
        ]);
    }
}
