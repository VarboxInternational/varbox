<?php

namespace Varbox\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Routing\Controller;
use Varbox\Contracts\UserModelContract;
use Varbox\Models\User;

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
        if ($request->filled('user')) {
            try {
                $usr = app(UserModelContract::class)->findOrFail((int)$request->get('user'));

                if ($user->id != $usr->id) {
                    $user = $usr;
                    $isAnotherUser = true;

                    flash()->warning('
                        <span class="font-weight-bold d-inline">You are now viewing another user\'s notifications!</span><br />
                        Interactive actions are not available.
                    ');
                }
            } catch (ModelNotFoundException $e) {
                flash()->error('
                    <span class="font-weight-bold d-inline">Failed viewing another user\'s notifications!</span><br />
                    You are now seeing your own notifications.
                ', $e);
            }
        }

        $query = $user->notifications();

        if ($request->filled('read')) {
            switch ($request->query('read')) {
                case 1:
                    $query->whereNotNull('read_at');
                    break;
                case 2:
                    $query->whereNull('read_at');
            }
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->query('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->query('end_date'));
        }

        if ($request->filled('sort')) {
            $query->getBaseQuery()->orders = null;
            $query->orderBy($request->get('sort'), $request->get('dir') ?: 'asc');
        }

        return view('varbox::admin.notifications.index')->with([
            'title' => 'Notifications',
            'items' => $query->paginate(config('varbox.varbox-crud.per_page', 10)),
            'users' => User::all(),
            'days' => config('varbox.varbox-notification.old_threshold', 30),
            'isAnotherUser' => isset($isAnotherUser),
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

            flash()->success('All unread notifications have been successfully marked as read!');
        } catch (Exception $e) {
            flash()->error('Something went wrong! Please try again.', $e);
        }

        return redirect()->route('admin.notifications.index');
    }

    /**
     * @param Authenticatable $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteOnlyRead(Authenticatable $user)
    {
        try {
            $user->readNotifications()->delete();

            flash()->success('All read notifications have been successfully deleted!');
        } catch (Exception $e) {
            flash()->error('Something went wrong! Please try again.', $e);
        }

        return redirect()->route('admin.notifications.index');
    }

    /**
     * @param Authenticatable $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteOnlyOld(Authenticatable $user)
    {
        try {
            $days = config('varbox.varbox-notification.old_threshold', 30);
            $date = Carbon::now()->subDays($days)->format('Y-m-d H:i:s');

            if ((int)$days > 0) {
                $user->notifications()->where('created_at', '<', $date)->delete();
            }

            flash()->success('Old notifications were successfully deleted!');
        } catch (Exception $e) {
            flash()->error('Something went wrong! Please try again.', $e);
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

            flash()->success('All notifications were successfully deleted!');
        } catch (Exception $e) {
            flash()->error('Something went wrong! Please try again.', $e);
        }

        return back();
    }
}
