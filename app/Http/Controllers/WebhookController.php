<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GeneralModel;
use App\Models\WebhookModel;
use Illuminate\Support\Facades\DB;

class WebhookController extends Controller
{
    //
    public static function webhookTest(Request $request)
    {
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'webhook_type' => 'string|required',
                'webhook_friendly_name' => 'string|required',
                'webhook_url' => 'string|required',
                'webhook_avatar_url' => 'string|required',
                'webhook_display_name' => 'string|required',
                'webhook_prefix_message' => 'string|nullable',
            ]);
            $data = [
                'webhook_type' => $request['webhook_type'],
                'webhook_url' => $request['webhook_url'],
                'webhook_friendly_name' => $request['webhook_friendly_name'],
                'webhook_avatar_url' => $request['webhook_avatar_url'],
                'webhook_display_name' => $request['webhook_display_name'],
                'webhook_prefix_message' => $request['webhook_prefix_message']
            ];
            return json_encode(WebhookModel::webhookTest($data));
        } else {
            return json_encode('Error: CSRF token missmatch.');
        }
        
    }
}