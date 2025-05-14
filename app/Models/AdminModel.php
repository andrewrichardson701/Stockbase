<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminModel query()
 * @mixin \Eloquent
 */
class AdminModel extends Model
{
    //
    static public function getActiveSessionLog()
    {
        $instance = new self();
        $instance->setTable('session_log AS sl');

        return $instance->selectRaw('sl.id AS id, sl.user_id AS sl_user_id, 
                                    FROM_UNIXTIME(sl.login_time) AS sl_login_time, IFNULL(FROM_UNIXTIME(sl.logout_time), NULL) AS sl_logout_time, 
                                    COALESCE(INET_NTOA(sl.ipv4), INET6_NTOA(sl.ipv6)) AS sl_ip,
                                    sl.browser AS sl_browser, sl.os AS sl_os, sl.status AS sl_status,
                                    FROM_UNIXTIME(sl.last_activity) AS sl_last_activity,
                                    u.username AS u_username')
                        ->join('users AS u', 'u.id', '=', 'sl.user_id')
                        ->where('sl.logout_time', '=', NULL)
                        ->get()
                        ->toarray();
    }

    static public function imageManagementCount()
    {
        $files = array_values(array_diff(scandir('img/stock'), array('..', '.'))); 
        $count = count($files);

        return $count;
    }

    static public function taggedStockByTagId() 
    {
        $return = [];

        $instance = new self();
        $instance->setTable('stock_tag');

        $results = $instance->get()
                        ->toarray();

        foreach($results as $result) {
            if (!isset($return[$result['tag_id']]['count']) || $return[$result['tag_id']]['count'] == 0) {
                $return[$result['tag_id']]['count'] = 0;
            }
            $return[$result['tag_id']]['count']++;
            $return[$result['tag_id']]['rows'][] = $result;
        }

        return $return;
    }

    static public function taggedStockByStockId() 
    {
        $return = [];

        $instance = new self();
        $instance->setTable('stock_tag');

        $results = $instance->get()
                        ->toarray();

    
        foreach($results as $result) {
            if (!isset($return[$result['stock_id']]['count']) || $return[$result['stock_id']]['count'] !== 0) {
                $return[$result['stock_id']]['count'] = 0;
            }
            $return[$result['stock_id']]['count']++;
            $return[$result['stock_id']]['rows'][] = $result;
        }

        return $return;
    }

    static public function taggedStockById() 
    {
        $return = [];

        $instance = new self();
        $instance->setTable('stock_tag');

        $results = $instance->get()
                        ->toarray();

        foreach($results as $result) {
            if (!isset($return[$result['id']]['count']) || $return[$result['id']]['count'] !== 0) {
                $return[$result['id']]['count'] = 0;
            }
            $return[$result['id']]['count']++;
            $return[$result['id']]['rows'][] = $result;
        }

        return $return;
    }

    static public function attributeLinks($search_table, $asset_field, $select=null, $deleted_count=null)
    {
        $return = [];

        $instance = new self();
        $instance->setTable($search_table);

        $results = $instance->when($select !== null, function ($query) use ($select) {
                                $query->selectRaw($select);
                            })
                        ->get()
                        ->toarray();

        foreach($results as $result) {
            if (!isset($return[$result[$asset_field]]['count']) || $return[$result[$asset_field]]['count'] == 0) {
                $return[$result[$asset_field]]['count'] = 0;
            }
            
            

            if ($deleted_count == 1) {
                if (isset($result['deleted'])) {
                    if (!isset($return[$result[$asset_field]]['deleted_count']) || $return[$result[$asset_field]]['deleted_count'] !== 0) {
                        $return[$result[$asset_field]]['deleted_count'] = 0;
                    }
                    if ($result['deleted'] == 1) {
                        $return[$result[$asset_field]]['deleted_count']++;
                    } else {
                        $return[$result[$asset_field]]['count']++;
                    }
                } else {
                    $return[$result[$asset_field]]['count']++;
                }
            } else {
                $return[$result[$asset_field]]['count']++;
            }

            $return[$result[$asset_field]]['rows'][] = $result;
        }

        return $return;
    }

}