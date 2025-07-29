<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingHelper
{
    public static function get($key, $default = null)
    {
        return Cache::rememberForever("settings.{$key}", function () use ($key, $default) {
            return DB::table('settings')->where('name', $key)->value('value') ?? $default;
        });
    }
}
?>