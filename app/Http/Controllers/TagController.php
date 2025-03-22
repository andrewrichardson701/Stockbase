<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\View\View;

use App\Models\IndexModel;
use App\Models\GeneralModel;
use App\Models\FunctionsModel;
use App\Models\ResponseHandlingModel;
use App\Models\TagModel;

class TagController extends Controller
{
    //
    static public function index(Request $request)
    {
        $nav_highlight = 'tags'; // for the nav highlighting
        $nav_data = GeneralModel::navData($nav_highlight);
        $response_handling = ResponseHandlingModel::responseHandling($request);
        $previous_url = GeneralModel::previousURL();

        $tags = GeneralModel::getAllWhere('tag', ['deleted' => 0], 'name');

        $tag_data = TagModel::getAllStockFromTags($tags);

        $sites = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('site', 0));
        $areas = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('area', 0));
        $shelves = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('shelf', 0));
 
        return view('tags', ['previous_url' => $previous_url,
                            'nav_data' => $nav_data,
                            'response_handling' => $response_handling,
                            'tag_data' => $tag_data,
                            'tags' => $tags,
                            'sites' => $sites ?? null,
                            'areas' => $areas ?? null,
                            'shelves' => $shelves ?? null,
                            ]);
    }
}
