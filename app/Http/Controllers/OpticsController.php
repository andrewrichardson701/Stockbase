<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

use Illuminate\View\View;

use App\Models\GeneralModel;
use App\Models\OpticsModel;
use App\Models\ResponseHandlingModel;
use App\Models\TransactionModel;

class OpticsController extends Controller
{
    //
    static public function index(Request $request, $stock_id, $modify_type = null): View|RedirectResponse  
    {
        $nav_highlight = 'assets'; // for the nav highlighting

        $nav_data = GeneralModel::navData($nav_highlight);

        $request = $request->all(); // turn request into an array
        $response_handling = ResponseHandlingModel::responseHandling($request);

        $site = $request['site'] ?? 0;
        $search = $request['search'] ?? null;
        $add_form = $request['add_form'] ?? null;
        $deleted = $request['deleted'] ?? 0;
        $optic_type = $request['type'] ?? 0;
        $optic_speed = $request['speed'] ?? 0;
        $optic_mode = $request['mode'] ?? 0;
        $optic_connector = $request['connector'] ?? 0;
        $optic_distance = $request['distance'] ?? 0;

        $form_model = $request['form_model'] ?? null;
        $form_spectrum = $request['form_spectrum'] ?? null;
        $form_vendor = $request['form_vendor'] ?? 0;
        $form_type = $request['form_type'] ?? 0;
        $form_speed = $request['form_speed'] ?? 0;
        $form_connector  = $request['form_connector'] ?? 0;
        $form_distance = $request['form_distance'] ?? 0;
        $form_mode = $request['form_mode'] ?? 0;
        $form_site = $request['form_site'] ?? 0;

        $sort = $request['sort'] ?? 'type';
        $deleted = $request['deleted'] ?? 0;
        $rows = $request['rows'] ?? 20;
        $page = $request['page'] ?? 1;

        $optics_data = OpticsModel::getOptics($request, $sort, $deleted, $rows, $page);

        $params = ['asset_type' => 'optics', 
                    'page' => $page,
                    'site' => $site,
                    'search' => $search, 
                    'add_form' => $add_form,
                    'deleted' => $deleted,
                    'sort' => $sort,
                    'rows' => $rows,
                    'request' => $request,
                    'optic_type' => $optic_type,
                    'optic_speed' => $optic_speed,
                    'optic_mode' => $optic_mode,
                    'optic_connector' => $optic_connector,
                    'optic_distance' => $optic_distance,

                    'form_model' => $form_model,
                    'form_spectrum' => $form_spectrum,
                    'form_vendor' => $form_vendor,
                    'form_type' => $form_type,
                    'form_speed' => $form_speed,
                    'form_connector' => $form_connector,
                    'form_distance' => $form_distance,
                    'form_mode' => $form_mode,
                    'form_site' => $form_site,
                ];

        $sites = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('site', 0));
        $manufacturers = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('manufacturer'));

        $optic_types = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('optic_type', 0));
        $optic_speeds = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('optic_speed', 0));
        $optic_connectors = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('optic_connector', 0));
        $optic_distances = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('optic_distance', 0));
        $optic_vendors = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinct('optic_vendor', 0));
        $optic_modes = ['rows' => ['MM' => ['id' => 'MM', 'name' => 'MM', 'full_name' => 'Multi Mode'],
                                    'SM' => ['id' => 'SM', 'name' => 'SM', 'full_name' => 'Single Mode'],
                                    'Copper' => ['id' => 'Copper', 'name' => 'Copper', 'full_name' => 'Copper'],
                                    'N/A' => ['id' => 'N/A', 'name' => 'N/A', 'full_name' => 'Not Applicable']
                                    ],
                        'count' => 4,
                        'deleted_rows' => 0,
                        ];
        $optic_models = GeneralModel::formatArrayOnIdAndCount(GeneralModel::allDistinctField('model', 'optic_item', 0));


        return view('optics', ['params' => $params,
                                'nav_data' => $nav_data,
                                'response_handling' => $response_handling,
                                'sites' => $sites ?? null,
                                'optic_types' => $optic_types,
                                'optic_speeds' => $optic_speeds,
                                'optic_modes' => $optic_modes,
                                'optic_connectors' => $optic_connectors,
                                'optic_distances' => $optic_distances,
                                'optic_vendors' => $optic_vendors,
                                'optic_models' => $optic_models,
                                'optics_data' => $optics_data,
                                ]);
    }
}
