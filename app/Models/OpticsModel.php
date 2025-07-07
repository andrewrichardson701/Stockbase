<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralModel;
use Illuminate\Support\Facades\DB;

/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpticsModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpticsModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpticsModel query()
 * @mixin \Eloquent
 */
class OpticsModel extends Model
{
    //
    static public function generateOpticWhereArray($array) 
    {
        
        $return = [];
        $optic_keys = ['type', 'speed', 'mode', 'connector', 'distance'];

        if (!empty($array)) {
            foreach($array as $key => $row) {
                if ($key == "site") {
                    $return[] = ['where' => "site.id = ?", 'value' => $array[$key]];
                } elseif ($key == "search") {
                    $value = $array[$key];
                    $return[] = ['where' => "(optic_item.serial_number LIKE ? 
                                                OR optic_item.model LIKE ? 
                                                OR optic_item.spectrum LIKE ?
                                                OR optic_vendor.name LIKE ?
                                                OR optic_type.name LIKE ? 
                                                OR optic_connector.name LIKE ?
                                                OR optic_distance.name LIKE ?
                                                OR optic_item.mode LIKE ? 
                                                OR optic_speed.name LIKE ?)", 
                                                'value' => ["%$value%", "%$value%", "%$value%", "%$value%", "%$value%", "%$value%", "%$value%", "%$value%", "%$value%",]];
                } elseif (in_array($key, $optic_keys)) {
                    if ($key == "mode") {
                        $return[] = ['where' => "optic_item.mode = ?", 'value' => $array[$key]];
                    } else {
                        $return[] = ['where' => "optic_$key.id = ?", 'value' => $array[$key]];
                    }
                }
            }
        } 
        
        return $return;

    }

    static public function getOptics($where_array = [], $orderby = 'type', $deleted = 0, $limit, $page)
    {
        if ($page == 0) { $page = 1; }

        $wheres = OpticsModel::generateOpticWhereArray($where_array) ;
        $order = OpticsModel::getOpitcsOrderBy($orderby);

        $totalCount = count(OpticsModel::getOpticsList($wheres, $order, $deleted, 0, 0));

        if ($limit == 0) { $limit = $totalCount; }

        $offset = $page*$limit-$limit > 0 ? $page*$limit-$limit : 0;

        $optics = GeneralModel::formatArrayOnIdAndCount(OpticsModel::getOpticsList($wheres, $order, $deleted, $limit, $offset));
        
        foreach ($optics['rows'] as $key => $optic){
            $comment_data = GeneralModel::formatArrayOnIdAndCount(OpticsModel::getOpticsComments($optic['id']));
            $optics['rows'][$key]['comment_data'] = $comment_data;
        }

        $optics['total_count'] = $totalCount;
        $optics['pages'] = (int)ceil($totalCount / $limit);
        $optics['page'] = $page;
        $optics['results_per_page'] = $limit;
        $optics['offset'] = $offset;

        return $optics;
    }

    static public function getOpitcsOrderBy($orderby) 
    {
        $order = "optic_type.id, optic_vendor.name, optic_distance.name, optic_connector.id, optic_item.model, optic_item.serial_number";
        switch ($orderby) {
            case 'type':
                $order = "optic_type.name, optic_vendor.name, optic_distance.name, optic_connector.id, optic_item.model, optic_item.serial_number";
                break;
            case 'connector':
                $order = "optic_connector.name, optic_type.name, optic_vendor.name, optic_item.model, optic_item.serial_number";
                break;
            case 'distance':
                $order = "optic_distance.name, optic_type.name, optic_connector.id, optic_vendor.name, optic_item.model, optic_item.serial_number";
                break;
            case 'model':
                $order = "optic_item.model, optic_type.name, optic_vendor.name, optic_distance.name, optic_connector.id, optic_item.serial_number";
                break;
            case 'speed':
                $order = "optic_speed.id, optic_type.name, optic_vendor.name, optic_distance.name, optic_connector.id, optic_item.model, optic_item.serial_number";
                break;
            case 'mode':
                $order = "optic_item.mode, optic_type.name, optic_vendor.name, optic_distance.name, optic_connector.id, optic_item.model, optic_item.serial_number";
                break;
            case 'serial':
                $order = "optic_item.serial_number, optic_type.name, optic_vendor.name, optic_distance.name, optic_connector.id, optic_item.model";
                break;
            case 'vendor':
                $order = "optic_vendor.name, optic_type.name, optic_distance.name, optic_connector.id, optic_item.model, optic_item.serial_number";
                break;
            default:
                $order = "optic_type.id, optic_vendor.name, optic_distance.name, optic_connector.id, optic_item.model, optic_item.serial_number";
                break;
        }

        return $order;
    }

    static public function getOpticsComments($optic_id)
    {
        $return = [];

        $instance = new self();
        $instance->setTable('optic_comment');

        $data = $instance->select(['optic_comment.id AS id',
                                    'optic_comment.item_id AS item_id',
                                    'optic_comment.comment AS comment',
                                    'optic_comment.user_id AS user_id',
                                    'optic_comment.timestamp AS timestamp',
                                    'optic_comment.deleted AS deleted',
                                    'users.username AS username'])
                            ->join('users', 'users.id', '=', 'optic_comment.user_id')
                            ->where('optic_comment.item_id', '=', $optic_id)
                            ->where('optic_comment.deleted', '=', 0)
                            ->orderBy('timestamp', 'desc')
                            ->get()
                            ->toArray();

        return $data;
    }

    static public function getOpticsList($where_array = [], $orderby = 'type', $deleted = 0, $limit, $offset)
    {
        $instance = new self();
        $instance->setTable('optic_item');

        $query = $instance->select(
                        ['optic_item.id AS id',
                        'optic_item.model AS model',
                        'optic_item.serial_number AS serial_number',
                        'optic_item.mode AS mode',
                        'optic_item.spectrum AS spectrum',
                        'optic_item.quantity AS quantity',
                        'optic_item.deleted AS deleted',
                        'optic_vendor.id AS vendor_id',
                        'optic_vendor.name AS vendor_name',
                        'optic_type.id AS type_id',
                        'optic_type.name AS type_name',
                        'optic_connector.id AS connector_id',
                        'optic_connector.name AS connector_name',
                        'optic_distance.id AS distance_id',
                        'optic_distance.name AS distance_name',
                        'optic_speed.id AS speed_id',
                        'optic_speed.name AS speed_name',
                        'site.id AS site_id',
                        'site.name AS site_name'])
                    ->join('optic_vendor', 'optic_item.vendor_id', '=', 'optic_vendor.id')
                    ->join('optic_type', 'optic_item.type_id', '=', 'optic_type.id')
                    ->join('optic_connector', 'optic_item.connector_id', '=', 'optic_connector.id')
                    ->join('optic_speed', 'optic_item.speed_id', '=', 'optic_speed.id')
                    ->join('optic_distance', 'optic_item.distance_id', '=', 'optic_distance.id')
                    ->join('site', 'optic_item.site_id', '=', 'site.id');

        if (!empty($where_array)) {
            foreach ($where_array as $where) {
                $query->whereRaw($where['where'], $where['value']);
            }
        }

        $query->where('optic_item.deleted', '=', $deleted);

        $query->orderByRaw($orderby);

        if ($limit != 0) {
            $query->limit($limit);
        }
        
        if ($offset != 0) {
            $query->offset($offset);
        }

        $rows = $query->get()->toArray();

        return $rows;

    }

    static public function addComment($request)
    {
        $optic_id = $request['id'];
        $comment = $request['comment'];
        $user = GeneralModel::getUser();

        // check optic exists 
        $find = DB::table('optic_item')->where('id', $optic_id)->first();

        if ($find) {
            // add comment
            $values = ['item_id' => $optic_id, 'comment' => $comment, 'user_id' => $user['id'], 'timestamp' => now(), 'created_at' => now(), 'updated_at' => now()];
            $insert = DB::table('optic_comment')->insertGetId($values);

            if ($insert) {
                // changelog
                $changelog_info = [
                    'user' => $user,
                    'table' => 'optic_comment',
                    'record_id' => $insert,
                    'action' => 'New record',
                    'field' => 'comment',
                    'previous_value' => '',
                    'new_value' => $comment
                ];
                
                GeneralModel::updateChangelog($changelog_info);
                $transaction = [
                    'table_name' => 'optic_comment',
                    'item_id' => $insert,
                    'type' => 'add',
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'username' => $user['username'],
                    'site_id' => 0,
                    'reason' => 'Comment Added',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                TransactionModel::addOpticTransaction($transaction);
                return redirect()->to(route('optics'))->with('success', 'Comment added: "'.$comment.'" with id: '.$insert.' for optic id: '.$optic_id.'.');
            } else {
                return redirect()->to(route('optics'))->with('error', 'Unable to insert database entry.');
            }
        } else {
            return redirect()->to(route('optics', ['error' => 'Optic not found for id: '.$optic_id]));
        }
    }

    static public function deleteComment($request)
    {
        $comment_id = $request['id'];
        $optic_id = $request['optic_id'];

        $user = GeneralModel::getUser();

        // check optic exists 
        $find = DB::table('optic_comment')->where('id', $comment_id)->first();

        if ($find) {
            // delete comment
            $update = DB::table('optic_comment')->where('id', $comment_id)->update(['deleted' => 1, 'updated_at' => now()]);

            if ($update) {
                // changelog
                $changelog_info = [
                    'user' => $user,
                    'table' => 'optic_comment',
                    'record_id' => $comment_id,
                    'action' => 'Comment Deleted',
                    'field' => 'deleted',
                    'previous_value' => $find->deleted,
                    'new_value' => 1
                ];

                GeneralModel::updateChangelog($changelog_info);
                $transaction = [
                    'table_name' => 'optic_comment',
                    'item_id' => $comment_id,
                    'type' => 'delete',
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'username' => $user['username'],
                    'site_id' => 0,
                    'reason' => 'Delete comment',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                TransactionModel::addOpticTransaction($transaction);
                return redirect()->to(route('optics'))->with('success', 'Comment deleted with id: '.$comment_id.' for optic id: '.$optic_id.'.');
            } else {
                return redirect()->to(route('optics'))->with('error', 'Unable to insert database entry.');
            }
        } else {
            return redirect()->to(route('optics', ['error' => 'Comment not found for id: '.$comment_id]));
        }
    }

    static public function addOptic($request)
    {
        $user = GeneralModel::getUser();

        // see if optic serial exists
        $find = DB::table('optic_item')->where('serial_number', $request['serial'])->first();
        
        // check for ids of each field
        foreach (['vendor', 'type', 'distance', 'speed', 'connector'] as $param) {
           $find_params = DB::table('optic_'.$param)->where('id', $request[$param])->where('deleted', 0)->first();
           if (!$find_params) {
                return redirect()->to(route('optics', ['error' => 'Optic '.$param.' not found for id: '.$request[$param]]));
            } 
        }

        // make sure site exists
        $find_site = DB::table('site')->where('id', $request['site'])->where('deleted', 0)->first();
        if (!$find_site) {
            return redirect()->to(route('optics', ['error' => 'Site not found for id: '.$request['site']]));
        } 
        $values = ['model' => $request['model'],
                        'vendor_id' => $request['vendor'],
                        'serial_number_id' => $request['serial'],
                        'type_id' => $request['type'],
                        'connector_id' => $request['connector'],
                        'mode' => $request['mode'],
                        'spectrum' => $request['spectrum'],
                        'speed_id' => $request['speed'],
                        'distance_id' => $request['distance'],
                        'site_id' => $request['site'],
                        'quantity' => 1,
                        'created_at' => now(), 
                        'updated_at' => now()];
        
        if (!$find) {
            // add optic

            $insert = DB::table('optic_item')->insertGetId($values);

            if ($insert) {
                // changelog
                $changelog_info = [
                    'user' => $user,
                    'table' => 'optic_item',
                    'record_id' => $insert,
                    'action' => 'New record',
                    'field' => 'serial_number',
                    'previous_value' => '',
                    'new_value' => $request['serial']
                ];

                GeneralModel::updateChangelog($changelog_info);
                $transaction = [
                    'table_name' => 'optic_item',
                    'item_id' => $insert,
                    'type' => 'add',
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'username' => $user['username'],
                    'site_id' => 0,
                    'reason' => 'Item Added',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                TransactionModel::addOpticTransaction($transaction);
                return redirect()->to(route('optics'))->with('success', 'Optic added: "'.$request['serial'].'" with id: '.$insert.'.');
            } else {
                if ($find->deleted == 1) {
                    // remove delete, and update any changes.
                    // update 
                    unset($values['serial_number']);

                    foreach (array_keys((array)$find) as $key) {
                        if (!in_array($key, ['id', 'serial_number', 'updated_at', 'creared_at'])) {
                            if ($values[$key] !== $find->$key) {
                                // update
                                $update = DB::table('optic_item')->where('id', $find->id)->update([$key => $values[$key]]);

                                if ($update) {
                                    // changelog
                                    $changelog_info = [
                                        'user' => $user,
                                        'table' => 'optic_item',
                                        'record_id' => $find->id,
                                        'action' => 'Update record',
                                        'field' => $key,
                                        'previous_value' => $find->$key,
                                        'new_value' => $values[$key]
                                    ];

                                    GeneralModel::updateChangelog($changelog_info);
                                    $transaction = [
                                        'table_name' => 'optic_item',
                                        'item_id' => $find->id,
                                        'type' => 'restore',
                                        'date' => date('Y-m-d'),
                                        'time' => date('H:i:s'),
                                        'username' => $user['username'],
                                        'site_id' => 0,
                                        'reason' => 'Item Restored',
                                        'created_at' => now(),
                                        'updated_at' => now()
                                    ];
                                    TransactionModel::addOpticTransaction($transaction);
                                } else {
                                    return redirect()->to(route('optics'))->with('error', 'Unable to insert database entry.');
                                }
                            }
                        }
                    }   
                    $data = ['id' => $find->id];
                    return OpticsModel::restore($data);
 
                } else {
                    return redirect()->to(route('optics'))->with('error', 'Optic already exists.');
                }  
            }
        } else {
            return redirect()->to(route('optics', ['error' => 'Optic already exists for serial number: '.$request['serial']]));
        }
    }

    static public function restoreOptic($request)
    {
        $optic_id = $request['id'];
        $user = GeneralModel::getUser();

        $find = DB::table('optic_item')->where('id', $optic_id)->where('deleted', 1)->first();

        if ($find) {
            $update = DB::table('optic_item')->where('id', $find->id)->update(['deleted' => 0]);

            if ($update) {
                // changelog
                $changelog_info = [
                    'user' => $user,
                    'table' => 'optic_item',
                    'record_id' => $optic_id,
                    'action' => 'Restore record',
                    'field' => 'deleted',
                    'previous_value' => $find->deleted,
                    'new_value' => 0
                ];

                GeneralModel::updateChangelog($changelog_info);

                $transaction = [
                    'table_name' => 'optic_item',
                    'item_id' => $optic_id,
                    'type' => 'restore',
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'username' => $user['username'],
                    'site_id' => 0,
                    'reason' => 'Item Restored',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                TransactionModel::addOpticTransaction($transaction);
                return redirect()->to(route('optics'))->with('success', 'Optic restored, with id: '.$optic_id.'.');
            } else {
                return redirect()->to(route('optics'))->with('error', 'Unable to insert database entry.');
            }
        } else {
            // optic doesnt exist
            return redirect()->to(route('optics'))->with('error', 'Optic not found with id: '.$optic_id.'.');
        }
    }

    static public function deleteOptic($request)
    {
        $optic_id = $request['id'];
        $reason = $request['reason'];
        $user = GeneralModel::getUser();

        // see if optic exists
        $find = DB::table('optic_item')->where('id', $optic_id)->first();

        if ($find && $find->deleted == 0) {
            $update = DB::table('optic_item')->where('id', $optic_id)->update(['deleted' => 1]);

            if ($update) {
                // changelog
                $changelog_info = [
                    'user' => $user,
                    'table' => 'optic_item',
                    'record_id' => $find->id,
                    'action' => 'Delete record',
                    'field' => 'deleted',
                    'previous_value' => $find->deleted,
                    'new_value' => 1
                ];

                GeneralModel::updateChangelog($changelog_info);
                $transaction = [
                    'table_name' => 'optic_item',
                    'item_id' => $optic_id,
                    'type' => 'delete',
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'username' => $user['username'],
                    'site_id' => 0,
                    'reason' => $reason,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                TransactionModel::addOpticTransaction($transaction);
            } else {
                return redirect()->to(route('optics'))->with('error', 'Unable to delete optic with id: '.$optic_id.'.');
            }
        } elseif ($find && $find->deleted == 1) {
            return redirect()->to(route('optics', ['error' => 'Optic already deleted for id: '.$optic_id]));
        } else {
            return redirect()->to(route('optics', ['error' => 'Optic not found for id: '.$optic_id.'.']));
        }
    }

    static public function moveOptic($request)
    {
        $optic_id = $request['id'];
        $site_id = $request['site'];
        $user = GeneralModel::getUser();

        // see if optic exists
        $find = DB::table('optic_item')->where('id', $optic_id)->first();

        if ($find) {
            // check if site exists
            $find_site = DB::table('site')->where('id', $site_id)->where('deleted', 0)->first();

            if ($find_site) {
                $update = DB::table('optic_item')->where('id', $optic_id)->update(['site_id' => $site_id]);

                if ($update) {
                    // changelog
                    $changelog_info = [
                        'user' => $user,
                        'table' => 'optic_item',
                        'record_id' => $optic_id,
                        'action' => 'Move record',
                        'field' => 'site_id',
                        'previous_value' => $find->site_id,
                        'new_value' => $site_id
                    ];

                    GeneralModel::updateChangelog($changelog_info);
                    $transaction = [
                        'table_name' => 'optic_item',
                        'item_id' => $optic_id,
                        'type' => 'move',
                        'date' => date('Y-m-d'),
                        'time' => date('H:i:s'),
                        'username' => $user['username'],
                        'site_id' => 0,
                        'reason' => 'Move optic',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    TransactionModel::addOpticTransaction($transaction);
                    return redirect()->to(route('optics'))->with('success', 'Optic for id: '.$optic_id.' moved.');
                } else {
                    return redirect()->to(route('optics'))->with('error', 'Unable to move optic with id: '.$optic_id.'.');
                }
            } else {
                return redirect()->to(route('optics', ['error' => 'Site not found for id: '.$site_id.'.']));
            }
        } else {
            return redirect()->to(route('optics', ['error' => 'Optic not found for id: '.$optic_id.'.']));
        }
    }

    static public function serialMatchChecker($request)
    {
        // search for the matching item
        $find = DB::table('optic_item')->where('serial_number', $request['serial'])->first();
        
        if ($find) {
            if ($find->deleted == 0) {
                $results['error'] = "Optic already exists.";
            } else {
                $results['error'] = "Found a matching deleted optic. Please restore this optic instead of adding.";
            }
        } else {
            $results['skip'] = 1;
        }

        return $results;
    }   
}
