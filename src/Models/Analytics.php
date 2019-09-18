<?php

namespace Varbox\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Spatie\Analytics\AnalyticsFacade;
use Spatie\Analytics\Period;
use Varbox\Contracts\AnalyticsModelContract;

class Analytics extends Model implements AnalyticsModelContract
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'analytics';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
    ];

    /**
     * Determine if Google Analytics metrics can be used.
     * This is done by checking the VIEW_ID property from the .env and the service-account-credentials.json file.
     *
     * @return bool
     */
    public static function shouldUseAnalytics()
    {
        if (!config('varbox.analytics.view_id')) {
            return false;
        }

        $credentials = config(
            'varbox.analytics.credentials_json',
            storage_path('app/analytics/service-account-credentials.json')
        );

        if (!File::exists($credentials)) {
            return false;
        }

        return true;
    }

    /**
     * Fetch the Google Analytics data.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return Collection
     */
    public static function fetchAnalyticsData(Carbon $startDate, Carbon $endDate)
    {
        $response = AnalyticsFacade::performQuery(
            Period::create($startDate, $endDate),
            'ga:users,ga:pageviews,ga:sessions,ga:exits',
            ['dimensions' => 'ga:date']
        );

        return collect($response['rows'] ?? [])->map(function (array $row) {
            return [
                'date' => Carbon::createFromFormat('Ymd', $row[0])->format('d M Y'),
                'visitors' => $row[1],
                'page_views' => (int)$row[2],
                'sessions' => (int)$row[3],
                'exits' => (int)$row[3],
            ];
        });
    }

    /**
     * Format the Google Analytics data.
     *
     * @param Collection $data
     * @return string
     */
    public static function formatAnalyticsData(Collection $data)
    {
        $format = [];
        $format['cols'] = [
            [
                'label' => '',
                'type' => 'string'
            ],
            [
                'label' => 'Visitors',
                'type' => 'number'
            ],
            [
                'label' => 'Page Views',
                'type' => 'number'
            ],
            [
                'label' => 'Sessions',
                'type' => 'number'
            ],
            [
                'label' => 'Exits',
                'type' => 'number'
            ],
        ];

        foreach ($data as $index => $col) {
            $format['rows'][$index]['c'] = [
                [
                    'v' => $col['date']
                ],
                [
                    'v' => $col['visitors']
                ],
                [
                    'v' => $col['page_views']
                ],
                [
                    'v' => $col['sessions']
                ],
                [
                    'v' => $col['exits']
                ],
            ];
        }

        return json_encode($format);
    }
}
