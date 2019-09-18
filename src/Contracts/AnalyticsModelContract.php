<?php

namespace Varbox\Contracts;

use Carbon\Carbon;
use Illuminate\Support\Collection;

interface AnalyticsModelContract
{
    /**
     * @return bool
     */
    public static function shouldUseAnalytics();

    /**
     * @param \Carbon\Carbon $startDate
     * @param \Carbon\Carbon $endDate
     * @return \Illuminate\Support\Collection
     */
    public static function fetchAnalyticsData(Carbon $startDate, Carbon $endDate);

    /**
     * @param \Illuminate\Support\Collection $data
     * @return string
     */
    public static function formatAnalyticsData(Collection $data);
}
