<?php

namespace Varbox\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Varbox\Contracts\ActivityModelContract;
use Varbox\Contracts\ErrorModelContract;
use Varbox\Contracts\UploadModelContract;
use Varbox\Contracts\UserModelContract;

class DashboardController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var UserModelContract
     */
    protected $userModel;

    /**
     * @param UserModelContract $userModel
     */
    public function __construct(UserModelContract $userModel) {
        $this->userModel = $userModel;
    }

    /**
     * @param Request $request
     * @param Authenticatable $user
     * @return \Illuminate\View\View
     */
    public function index(Request $request, Authenticatable $user)
    {
        meta()->set('title', 'Admin - Dashboard');

        return view('varbox::admin.dashboard')->with([
            'hideTitle' => true,
            'usersRegisteredInTheLastMonth' => $this->usersRegisteredInTheLastMonth(),
            'usersRegisteredInTheLastWeek' => $this->usersRegisteredInTheLastWeek(),
            'activeUsers' => $this->activeUsers(),
            'inactiveUsers' => $this->inactiveUsers(),
        ]);
    }

    /**
     * @return int
     */
    protected function usersRegisteredInTheLastMonth()
    {
        return $this->userModel
            ->excludingAdmins()
            ->where('created_at', '>=', now()->subMonth()->startOfDay())
            ->count();
    }

    /**
     * @return int
     */
    protected function usersRegisteredInTheLastWeek()
    {
        return $this->userModel
            ->excludingAdmins()
            ->where('created_at', '>=', now()->subWeek()->startOfDay())
            ->count();
    }

    /**
     * @return int
     */
    protected function activeUsers()
    {
        return $this->userModel
            ->excludingAdmins()
            ->onlyActive()
            ->count();
    }

    /**
     * @return int
     */
    protected function inactiveUsers()
    {
        return $this->userModel
            ->excludingAdmins()
            ->onlyInactive()
            ->count();
    }
}
