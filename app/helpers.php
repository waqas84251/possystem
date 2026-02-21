<?php

if (!function_exists('remove_filter_url')) {
    function remove_filter_url($filterToRemove)
    {
        $currentQuery = request()->query();
        unset($currentQuery[$filterToRemove]);
        
        return url()->current() . (!empty($currentQuery) ? '?' . http_build_query($currentQuery) : '');
    }
}