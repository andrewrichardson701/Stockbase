<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

use Illuminate\View\View;

// use App\Models\IndexModel;
use App\Models\GeneralModel;
use App\Models\StockModel;
use App\Models\FavouritesModel;
use App\Models\ResponseHandlingModel;

class FavouritesController extends Controller
{
    //
    static public function index(Request $request): View
    {
        $nav_highlight = 'favourites'; // for the nav highlighting

        $nav_data = GeneralModel::navData($nav_highlight);
        $request = $request->all(); // turn request into an array
        $response_handling = ResponseHandlingModel::responseHandling($request);

        $favourites = FavouritesModel::getFavouriteData(GeneralModel::getUser()['id']);

        return view('favourites', ['nav_data' => $nav_data,
                                'response_handling' => $response_handling,
                                'favourites' => $favourites,
                                ]);
    }
}
