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
     * @var UploadModelContract
     */
    protected $uploadModel;

    /**
     * @var ActivityModelContract
     */
    protected $activityModel;

    /**
     * @var ErrorModelContract
     */
    protected $errorModel;

    /**
     * @param UserModelContract $userModel
     * @param UploadModelContract $uploadModel
     * @param ActivityModelContract $activityModel
     * @param ErrorModelContract $errorModel
     */
    public function __construct(
        UserModelContract $userModel,
        UploadModelContract $uploadModel,
        ActivityModelContract $activityModel,
        ErrorModelContract $errorModel
    ) {
        $this->userModel = $userModel;
        $this->uploadModel = $uploadModel;
        $this->activityModel = $activityModel;
        $this->errorModel = $errorModel;
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
            'usersRegisteredInTheLastMonth' => $this->usersRegisteredInTheLasMonth(),
            'filesUploadedInTheLasMonth' => $this->filesUploadedInTheLasMonth(),
            'activityLoggedInTheLasMonth' => $this->activityLoggedInTheLasMonth(),
            'errorsOccurredInTheLasMonth' => $this->errorsOccurredInTheLasMonth(),
            'latestUsers' => $this->latestUsers(),
            'latestUploads' => $this->latestUploads(),
            'latestActivity' => $this->latestActivity(),
        ]);
    }

    /**
     * @return int
     */
    protected function usersRegisteredInTheLasMonth()
    {
        return $this->userModel
            ->excludingAdmins()
            ->where('created_at', '>=', now()->subMonth()->startOfDay())
            ->count();
    }

    /**
     * @return int
     */
    protected function filesUploadedInTheLasMonth()
    {
        return $this->uploadModel
            ->where('created_at', '>=', now()->subMonth()->startOfDay())
            ->count();
    }

    /**
     * @return int
     */
    protected function activityLoggedInTheLasMonth()
    {
        return $this->activityModel
            ->where('created_at', '>=', now()->subMonth()->startOfDay())
            ->count();
    }

    /**
     * @return int
     */
    protected function errorsOccurredInTheLasMonth()
    {
        return $this->errorModel
            ->where('created_at', '>=', now()->subMonth()->startOfDay())
            ->count();
    }

    /**
     * @return Collection
     */
    protected function latestUsers()
    {
        return $this->userModel
            ->excludingAdmins()
            ->latest()
            ->take(5)
            ->get();
    }

    /**
     * @return Collection
     */
    protected function latestUploads()
    {
        return $this->uploadModel
            ->latest()
            ->take(5)
            ->get();
    }

    /**
     * @return Collection
     */
    protected function latestActivity()
    {
        return $this->activityModel
            ->latest()
            ->take(5)
            ->get();
    }
}
