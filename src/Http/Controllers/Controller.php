<?php

namespace Kainotomo\PHMoney\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Kainotomo\PHMoney\Models\Setting;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Get start date from request
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Support\Carbon
     */
    protected function getStartDate(Request $request)
    {
        $setting = Setting::where(
            [
                'type' => "AccountingPeriod"
            ]
        )->firstOrFail();
        $date_start = $request->date_start ?? $setting->params['date_start'];
        $date_start = is_string($date_start) ? json_decode($date_start, true) : $date_start;
        if ($date_start['date_type'] == 'filter_date') {
            $date_start = (new Carbon($date_start['filter_date']))->startOfDay();
        } else {
            switch ($date_start['list_date']['id']) {
                case 'today':
                    $date_start = now()->startOfDay();
                    break;

                case 'start_of_this_month':
                    $date_start = now()->startOfMonth();
                    break;

                case 'start_of_previous_month':
                    $date_start = now()->subMonth()->startOfMonth();
                    break;

                case 'start_of_current_quarter':
                    $date_start = now()->startOfQuarter();
                    break;

                case 'start_of_previous_quarter':
                    $date_start = now()->subQuarter()->startOfQuarter();
                    break;

                case 'start_of_this_year':
                    $date_start = now()->startOfYear();
                    break;

                case 'start_of_previous_year':
                    $date_start = now()->subYear()->startOfYear();
                    break;

                default:
                    $date_start = now()->startOfYear();
                    break;
            }
        }
        return $date_start;
    }

    /**
     * Get end date from request
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Support\Carbon
     */
    protected function getEndDate(Request $request)
    {
        $setting = Setting::where(
            [
                'type' => "AccountingPeriod"
            ]
        )->firstOrFail();
        $date_end = $request->date_end ?? $setting->params['date_end'];
        $date_end = is_string($date_end) ? json_decode($date_end, true) : $date_end;
        if ($date_end['date_type'] == 'filter_date') {
            $date_end = (new Carbon($date_end['filter_date']))->endOfDay();
        } else {
            switch ($date_end['list_date']['id']) {
                case 'today':
                    $date_end = now()->endOfDay();
                    break;

                case 'end_of_this_month':
                    $date_end = now()->endOfMonth();
                    break;

                case 'end_of_previous_month':
                    $date_end = now()->subMonth()->endOfMonth();
                    break;

                case 'end_of_current_quarter':
                    $date_end = now()->endOfQuarter();
                    break;

                case 'end_of_previous_quarter':
                    $date_end = now()->subQuarter()->endOfQuarter();
                    break;

                case 'end_of_this_year':
                    $date_end = now()->endOfYear();
                    break;

                case 'end_of_previous_year':
                    $date_end = now()->subYear()->endOfYear();
                    break;

                default:
                    break;
            }
        }

        return $date_end;
    }

}
