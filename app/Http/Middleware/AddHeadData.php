<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\GeneralModel; // Import GeneralModel
use Illuminate\Http\Request;

class AddHeadData
{
    public function handle(Request $request, Closure $next)
    {
        // Fetch the data using your GeneralModel's headData() method
        $head_data = GeneralModel::headData();

        // Share the data with all views
        view()->share('head_data', $head_data);

        return $next($request);
    }
}