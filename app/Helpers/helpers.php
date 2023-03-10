<?php
use Carbon\Carbon;

if (! function_exists('getCurrentDateTime')) {
    function getCurrentDateTime()
    {
        return Carbon::now()->format('Y-m-d H:i:s');
    }
}
