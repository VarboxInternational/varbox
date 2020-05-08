<?php

namespace Varbox\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Blade;
use Varbox\Contracts\AnalyticsModelContract;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AnalyticsController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var AnalyticsModelContract
     */
    protected $model;

    /**
     * @param AnalyticsModelContract $model
     */
    public function __construct(AnalyticsModelContract $model)
    {
        $this->model = $model;
    }

    /**
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        meta()->set('title', 'Admin - Analytics');

        $item = $this->model->first();
        $metrics = null;

        $startDate = now()->subMonth();
        $endDate = now();

        if ($request->filled('start_date')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->query('start_date'));
        }

        if ($request->filled('end_date')) {
            $endDate = Carbon::createFromFormat('Y-m-d', $request->query('end_date'));
        }

        if ($startDate > $endDate) {
            flash()->error('Please select a valid time period in which to view Analytics data!');

            return redirect()->route('admin.analytics.show');
        }

        if ($this->model->shouldUseAnalytics()) {
            $metrics = $this->model->formatAnalyticsData(
                $this->model->fetchAnalyticsData($startDate, $endDate)
            );
        }

        return view('varbox::admin.analytics.show')->with([
            'title' => 'Analytics',
            'item' => $item,
            'metrics' => $metrics,
        ]);
    }

    /**
     * @param AnalyticsModelContract $analytics
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, AnalyticsModelContract $analytics = null)
    {
        try {
            if ($analytics && $analytics->exists) {
                $analytics->update($request->all());
            } else {
                $this->model->create($request->all());
            }

            flash()->success('The Analytics code was successfully saved!');
        } catch (Exception $e) {
            flash()->error('Something went wrong! Please try again.', $e);
        }

        return redirect()->route('admin.analytics.show')->withInput($request->all());
    }
}
