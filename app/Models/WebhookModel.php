<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralModel;
use App\Models\SmtpModel;
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

    static public function getTemplateInfo($template_id)
    {
        $template = DB::table('webhook_templates')->where('id', '=', $template_id)->first();

        if ($template) {
            return $template;
        } else {
            return false;
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

            if ($config['webhook_prefix_message']) {
                $payload["content"] = $config['webhook_prefix_message'].' '.$payload["content"];
            }

            if ($config['webhook_friendly_name']) {
                $payload["username"] = $config['webhook_friendly_name']; // custom name
            }

            if ($config['webhook_avatar_url']) {
                $payload["avatar_url"] = $config['webhook_avatar_url']; // custom avatar
            }

            if (!empty($embeds)) {
                $embeds[0]['timestamp'] = date('c');
                $payload["embeds"] = $embeds; // embed objects
            }
            
            $jsonData = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $jsonData = str_replace('##CURRENT_TIMESTAMP_ISO8601##', date('c'), $jsonData);
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
            
        } else {
            return "No webhook url.";
        }
    }

    static public function convertJsonEmbeds($json)
    {
        $decoded = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            die("Invalid JSON: " . json_last_error_msg());
        }
        return $decoded;
    }

    static public function sanitizeEmbedString(string $input): string 
    {
        // Replace multiple spaces/newlines with a single space
        $input = preg_replace("/\s+/", " ", $input);

        // remove \n 
        $input = str_replace('\n', '', $input);

        return trim($input);
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
            return 'No webhook url specified.';
        }
    }

    public static function notificationWebhook($notification_id, $template_id, $data)
    {
        $config = GeneralModel::configCompare();
        $user = GeneralModel::getUser();

        if ($config['webhook_enabled'] == 1) { // make sure webhook is enabled
            $notification_data = DB::table('webhook_notifications')->find($notification_id);
            
            if ($notification_data && $notification_data->enabled == 1) {
            
                if ($template_id == 0) {
                    $template_id = $notification_data->template_id;
                }

                $template_info = WebhookModel::getTemplateInfo($template_id);

                if ($template_info !== false) {
                    // get the embeds
                    $embeds_raw = $template_info->body;
                    // covnert variables
                    $converted_embeds = SmtpModel::convertVariables($embeds_raw, $data);
                    // sanitize the embeds
                    $sanitized_embeds = WebhookModel::sanitizeEmbedString($converted_embeds);
                    // json the embeds
                    $embeds = WebhookModel::convertJsonEmbeds($sanitized_embeds);
                    // send the webhook
                    WebhookModel::sendWebhook(SmtpModel::convertVariables($template_info->subject, $data), $embeds);
                } else {
                    return 'Unable to find template';
                }
            } else {
                return 'disabled';
            }
        } else {
            return 'disabled';
        }
    }
}
