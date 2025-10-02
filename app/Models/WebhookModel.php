<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralModel;
use Illuminate\Support\Facades\DB;

class WebhookModel extends Model
{
    //
    public static function toggleWebhook($enabled)
    {
        $user = GeneralModel::getUser();
        if ($user['permissions']['root'] !== 1 && $user['permissions']['admin'] !== 1) {
            return redirect()->to(route('admin', ['section' => 'webhook-settings']) . '#webhook-settings')->with('error', 'Permission denied.');
        }

        if (in_array($enabled, ['on', 'off'])) {
            if ($enabled == 'on') {
                $enabled = 1;
            } else {
                $enabled = 0;
            }

            if (is_numeric($enabled)) {
                $current_data = DB::table('config')
                        ->select('webhook_enabled')
                        ->where('id', 1)
                        ->first();

                if ($current_data) {
                    $previous_value = $current_data->webhook_enabled;

                    $state = $enabled == 1 ? 'enabled' : 'disabled';

                    $update = DB::table('config')->where('id', 1)->update(['webhook_enabled' => (int)$enabled, 'updated_at' => now()]);

                    if ($update) {
                        // changelog
                        $changelog_info = [
                            'user' => GeneralModel::getUser(),
                            'table' => 'config',
                            'record_id' => 1,
                            'action' => 'Update record',
                            'field' => 'webhook_enabled',
                            'previous_value' => $previous_value,
                            'new_value' => (int)$enabled
                        ];

                        GeneralModel::updateChangelog($changelog_info);
                        return redirect()->to(route('admin', ['section' => 'webhook-settings']) . '#webhook-settings')->with('success', 'Webhook '.$state.'!');
                    } else {
                        return redirect()->to(route('admin', ['section' => 'webhook-settings']) . '#webhook-settings')->with('error', 'No changes made. Unable to toggle Webhook');
                    }
                    
                } else {
                    return redirect()->to(route('admin', ['section' => 'webhook-settings']) . '#webhook-settings')->with('error', 'Unable to get current config.');
                }
            } else {
                return redirect()->to(route('admin', ['section' => 'webhook-settings']) . '#webhook-settings')->with('error', 'Invalid value.');
            }
        } else {
            return redirect()->to(route('admin', ['section' => 'webhook-settings']) . '#webhook-settings')->with('error', 'Invalid value.');
        }
    }

    public static function sendWebhook($message = null, $embeds = []) 
    {
        // Base payload
        $payload = [];

        $config = GeneralModel::config();

        if ($config['webhook_url']) {

            if ($message) {
                $payload["content"] = $message; // plain text message
            }

            if ($config['webhook_friendly_name']) {
                $payload["username"] = $config['webhook_friendly_name']; // custom name
            }

            if ($config['webhook_avatar_url']) {
                $payload["avatar_url"] = $config['webhook_avatar_url']; // custom avatar
            }

            if (!empty($embeds)) {
                $payload["embeds"] = $embeds; // embed objects
            }

            $jsonData = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            // Initialize cURL
            $ch = \curl_init($config['webhook_url']);

            \curl_setopt($ch, CURLOPT_POST, true);
            \curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            \curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json"
            ]);

            // Execute and capture response
            $response = \curl_exec($ch);

            if (\curl_errno($ch)) {
                $error = \curl_error($ch);
                \curl_close($ch);
                return "cURL Error: $error";
            }

            \curl_close($ch);
            return $response ?: "Message sent!";

        }
    }

    static public function buildWebhookEmbeds($title = null, $description = null, $color = null, $fields = [], $footer = [])
    {
        $embeds = [
            'title' => $title,
            'description' => $description,
            'color' => $color,
            'fields' => $fields,
            'footer' => $footer,
            'timestamp' => date("c")
        ];

        return $embeds;
    }

    static public function webhookTest($data)
    {
        $payload = [];

        if (isset($data['webhook_url'])) {

            $payload["content"] = 'Test message!'; // plain text message

            if (isset($data['webhook_prefix_message'])) {
                $payload['content'] = $data['webhook_prefix_message'].' '.$payload["content"];
            }

            if (isset($data['webhook_friendly_name'])) {
                $payload["username"] = $data['webhook_friendly_name']; // custom name
            }

            if (isset($data['webhook_avatar_url'])) {
                $payload["avatar_url"] = $data['webhook_avatar_url']; // custom avatar
            }


            $jsonData = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            // Initialize cURL
            $ch = \curl_init($data['webhook_url']);

            \curl_setopt($ch, CURLOPT_POST, true);
            \curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            \curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json"
            ]);

            // Execute and capture response
            $response = \curl_exec($ch);

            if (\curl_errno($ch)) {
                $error = \curl_error($ch);
                \curl_close($ch);
                return "cURL Error: $error";
            }

            \curl_close($ch);
            return $response ?: "Message sent!";

        } else {
            return null;
        }
    }
}
