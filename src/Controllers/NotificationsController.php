<?php

namespace Varbox\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Routing\Controller;
use Varbox\Exceptions\NotificationException;

class NotificationsController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param Request $request
     * @param Authenticatable $user
     * @return \Illuminate\View\View
     */
    public function index(Request $request, Authenticatable $user)
    {
        $query = $user->notifications();

        if ($request->filled('read')) {
            switch ($request->query('read')) {
                case 1:
                    $query->whereNull('read_at');
                    break;
                case 2:
                    $query->whereNotNull('read_at');
            }
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->query('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->query('end_date'));
        }

        return view('varbox::admin.notifications.index')->with([
            'items' => $query->paginate(config('varbox.varbox-crud.per_page', 10))
        ]);
    }

    /**
     * @param DatabaseNotification $notification
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(DatabaseNotification $notification)
    {
        try {
            $notification->delete();

            flash()->success('The record was successfully deleted!');
        } catch (Exception $e) {
            flash()->error('Something went wrong! Please try again.', $e);
        }

        return back();
    }

    /**
     * @param DatabaseNotification $notification
     * @return \Illuminate\Http\RedirectResponse
     */
    public function actionNotification(DatabaseNotification $notification)
    {
        try {
            $notification->markAsRead();

            return isset($notification->data['url']) ? redirect($notification->data['url']) : back();
        } catch (Exception $e) {
            flash()->error('Something went wrong! Please try again.', $e);

            return back();
        }
    }

    /**
     * @param DatabaseNotification $notification
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsRead(DatabaseNotification $notification)
    {
        try {
            $notification->markAsRead();

            flash()->success('The notification has been successfully marked as read!');
        } catch (Exception $e) {
            flash()->error('Something went wrong! Please try again.', $e);
        }

        return back();
    }

    /**
     * @param Authenticatable $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAllAsRead(Authenticatable $user)
    {
        try {
            $user->unreadNotifications->each(function ($notification) {
                $notification->markAsRead();
            });

            flash()->success('All your unread notifications have been successfully marked as read!');
        } catch (Exception $e) {
            flash()->error('Something went wrong! Please try again.', $e);
        }

        return back();
    }

    /**
     * @param Authenticatable $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteOnlyRead(Authenticatable $user)
    {
        try {
            $user->readNotifications()->delete();

            flash()->success('All your already read notifications have been successfully deleted!');
        } catch (Exception $e) {
            flash()->error('Something went wrong! Please try again.', $e);
        }

        return back();
    }

    /**
     * @param Authenticatable $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteOnlyOld(Authenticatable $user)
    {
        try {
            $days = config('varbox.notification.delete_records_older_than', 30);

            if ((int)$days > 0) {
                try {
                    $user->notifications()->where(
                        'created_at', '<', Carbon::now()->subDays($days)->format('Y-m-d H:i:s')
                    )->delete();
                } catch (Exception $e) {
                    throw NotificationException::cleanupFailed();
                }
            }

            flash()->success('The records were successfully cleaned up!');
        } catch (Exception $e) {
            flash()->error('Could not cleanup the records! Please try again.', $e);
        }

        return back();
    }

    /**
     * @param Authenticatable $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAll(Authenticatable $user)
    {
        try {
            $user->notifications()->delete();

            flash()->success('All your notifications have been successfully deleted!');
        } catch (Exception $e) {
            flash()->error('Something went wrong! Please try again.', $e);
        }

        return back();
    }
}
