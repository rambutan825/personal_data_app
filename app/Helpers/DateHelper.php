<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Return a date according to the timezone of the user, in a
     * short format like "Oct 29, 1981".
     *
     * @param  Carbon  $date
     * @param  string  $timezone
     * @return string
     */
    public static function formatDate(Carbon $date, string $timezone = null): string
    {
        if ($timezone) {
            $date->setTimezone($timezone);
        }

        return $date->isoFormat(trans('format.date'));
    }

    /**
     * Return a date and the time according to the timezone of the user, in a
     * short format like "Oct 29, 1981 19:32".
     *
     * @param  Carbon  $date
     * @param  string  $timezone
     * @return string
     */
    public static function formatShortDateWithTime(Carbon $date, string $timezone = null): string
    {
        if ($timezone) {
            $date->setTimezone($timezone);
        }

        return $date->isoFormat(trans('format.short_date_year_time'));
    }

    /**
     * Return the day and the month in a format like "July 29th".
     *
     * @param  Carbon  $date
     * @return string
     */
    public static function formatMonthAndDay(Carbon $date): string
    {
        return $date->isoFormat(trans('format.long_month_day'));
    }

    /**
     * Return the short month and the year in a format like "Jul 2020".
     *
     * @param  Carbon  $date
     * @return string
     */
    public static function formatMonthAndYear(Carbon $date): string
    {
        return $date->isoFormat(trans('format.short_month_day'));
    }

    /**
     * Return the day and the month in a format like "Jul 29".
     *
     * @param  Carbon  $date
     * @return string
     */
    public static function formatShortMonthAndDay(Carbon $date): string
    {
        return $date->isoFormat(trans('format.short_date'));
    }

    /**
     * Return the day and the month in a format like "Monday (July 29th)".
     *
     * @param  Carbon  $date
     * @param  string  $timezone
     * @return string
     */
    public static function formatDayAndMonthInParenthesis(Carbon $date, string $timezone = null): string
    {
        if ($timezone) {
            $date->setTimezone($timezone);
        }

        return $date->isoFormat(trans('format.day_month_parenthesis'));
    }

    /**
     * Return the complete date like "Monday, July 29th 2020".
     *
     * @param  Carbon  $date
     * @return string
     */
    public static function formatFullDate(Carbon $date): string
    {
        return $date->isoFormat(trans('format.full_date'));
    }
}
