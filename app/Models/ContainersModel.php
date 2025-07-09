<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

// use App\Models\FunctionsModel;
use App\Models\GeneralModel;


/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $shelf_id
 * @property int $deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContainersModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContainersModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContainersModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContainersModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContainersModel whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContainersModel whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContainersModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContainersModel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContainersModel whereShelfId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContainersModel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ContainersModel extends Model
{
    //
    protected $table = 'container'; // Specify your table name
    protected $fillable = ['name', 'description', 'shelf_id', 'deleted'];

    static public function checkForUncontaineredItems($shelf_id, $manufacturer_id, $stock_id, $compare_count)
    {
        $all_count = count(GeneralModel::getAllWhere('item', ['shelf_id' => $shelf_id, 'manufacturer_id' => $manufacturer_id, 'stock_id' => $stock_id, 'deleted' => 0]));

        if ($all_count > $compare_count) {
            return 1;
        } else {
            return 0;
        }
    }

    static public function getContainersInUse($shelf_id=null, $manufacturer_id=null, $stock_id=null) 
    {
        $instance = new self();
        $instance->setTable('item_container as ic');
        
        return $instance->selectRaw('
                        c.id AS c_id, c.name AS c_name, c.description AS c_description,
                        ic.id AS ic_id, ic.item_id AS ic_item_id, ic.container_id AS ic_container_id, ic.container_is_item AS ic_container_is_item,
                        icontainer.id AS icontainer_id,
                        scontainer.id AS scontainer_id, scontainer.name AS scontainer_name, scontainer.description AS scontainer_description,
                        i.id AS i_id,
                        c_sh.id AS c_sh_id, i_sh.id AS i_sh_id,
                        CONCAT(c_si.name, " - ", c_a.name, " - ", c_sh.name) AS c_location,
                        CONCAT(i_si.name, " - ", i_a.name, " - ", i_sh.name) AS i_location,
                        s.id AS s_id, s.name AS s_name, s.description AS s_description,
                        (SELECT COUNT(item_id) 
                        FROM item_container 
                        WHERE item_container.container_id = ic.container_id 
                        AND item_container.container_is_item = ic.container_is_item
                        ) AS object_count,
                        (SELECT id FROM stock_img WHERE stock_id = scontainer.id LIMIT 1) AS simgcontainer_id,
                        (SELECT image FROM stock_img WHERE stock_id = scontainer.id LIMIT 1) AS simgcontainer_image,
                        (SELECT id FROM stock_img WHERE stock_id = s.id LIMIT 1) AS simg_id,
                        (SELECT image FROM stock_img WHERE stock_id = s.id LIMIT 1) AS simg_image
                    ')
                    ->leftJoin('container as c', function ($join) {
                        $join->on('ic.container_id', '=', 'c.id')
                            ->where('ic.container_is_item', '=', 0)
                            ->where('c.deleted', '=', 0);
                    })
                    ->leftJoin('item as icontainer', function ($join) {
                        $join->on('icontainer.id', '=', 'ic.container_id')
                            ->where('ic.container_is_item', '=', 1)
                            ->where('icontainer.deleted', '=', 0);
                    })
                    ->leftJoin('stock as scontainer', 'scontainer.id', '=', 'icontainer.stock_id')
                    ->leftJoin('stock_img as simgcontainer', 'simgcontainer.stock_id', '=', 'scontainer.id')
                    ->leftJoin('item as i', 'i.id', '=', 'ic.item_id')
                    ->leftJoin('stock as s', 's.id', '=', 'i.stock_id')
                    ->leftJoin('stock_img as simg', 'simg.stock_id', '=', 's.id')
                    ->leftJoin('shelf as c_sh', 'c.shelf_id', '=', 'c_sh.id')
                    ->leftJoin('area as c_a', 'c_sh.area_id', '=', 'c_a.id')
                    ->leftJoin('site as c_si', 'c_a.site_id', '=', 'c_si.id')
                    ->leftJoin('shelf as i_sh', 'i.shelf_id', '=', 'i_sh.id')
                    ->leftJoin('area as i_a', 'i_sh.area_id', '=', 'i_a.id')
                    ->leftJoin('site as i_si', 'i_a.site_id', '=', 'i_si.id')
                    ->when($shelf_id !== null, function ($query) use ($shelf_id) {
                        $query->where('i_sh.id', '=', $shelf_id);
                    })
                    ->when($manufacturer_id !== null, function ($query) use ($manufacturer_id) {
                        $query->where('i.manufacturer_id', '=', $manufacturer_id);
                    })
                    ->when($stock_id !== null, function ($query) use ($stock_id) {
                        $query->where('s.id', '=', $stock_id);
                    })
                    ->groupBy([
                        'c.id', 'c.name', 'c.description',
                        'ic.id', 'ic.item_id', 'ic.container_id', 'ic.container_is_item',
                        'icontainer.id',
                        'scontainer.id', 'scontainer.name', 'scontainer.description',
                        'i.id',
                        's.id', 's.name', 's.description',
                        'c_sh.id', 'i_sh.id',
                        'i_location', 'c_location',
                        'simgcontainer_id', 'simgcontainer_image',
                        'simg_id', 'simg_image'
                    ])
                    ->orderBy('c.name')
                    ->orderBy('scontainer.name')
                    ->get()
                    ->toArray();
    }

    static public function getContainersByShelf($shelf_id) 
    {        
        $return = [];
        $count = 0;

        $containers = GeneralModel::getAllWhere('container', ['shelf_id' => $shelf_id], 'name') ?? [];
        $container_items = GeneralModel::getAllWhere('item', ['deleted' => 0, 'is_container' => 1, 'shelf_id' => $shelf_id], 'id') ?? [];
        foreach($container_items as $key => $ci) {
            $stock_info = GeneralModel::getAllWhere('stock', ['id' => $ci['stock_id']], 'name');
            $container_items[$key]['name'] = $stock_info[0]['name'] ?? null;
            $container_items[$key]['description'] = $stock_info[0]['description'] ?? null;
        }
        
        $count = count($containers)+count($container_items);

        $return['count'] = $count;
        $return['containers'] = $containers ?? [];
        $return['containers']['count'] = count($containers);
        $return['container_items'] = $container_items ?? [];
        $return['container_items']['count'] = count($container_items);

        return $return;
    }

    static public function getContainersEmpty() 
    {
        $instance = new self();
        $instance->setTable('container as c');

        return $instance->selectRaw('
                        c.id AS c_id, 
                        c.name AS c_name, 
                        c.description AS c_description, 
                        CONCAT(si.name, " - ", a.name, " - ", sh.name) AS location
                    ')
                    ->leftJoin('item_container as ic', function ($join) {
                        $join->on('c.id', '=', 'ic.container_id')
                            ->where('ic.container_is_item', '=', 0);
                    })
                    ->leftJoin('shelf as sh', 'sh.id', '=', 'c.shelf_id')
                    ->leftJoin('area as a', 'a.id', '=', 'sh.area_id')
                    ->leftJoin('site as si', 'si.id', '=', 'a.site_id')
                    ->whereNull('ic.id')
                    ->where('c.deleted', '=', 0)
                    ->orderBy('c.name')
                    ->get()
                    ->toArray();
    }

    static public function getContainersItemEmpty() 
    {
        $instance = new self();
        $instance->setTable('item as i');
        return $instance->selectRaw('
                        i.id AS i_id,
                        s.id AS s_id,
                        s.name AS s_name,
                        s.description AS s_description,
                        CONCAT(si.name, " - ", a.name, " - ", sh.name) AS location,
                        (SELECT id FROM stock_img WHERE stock_id = s.id LIMIT 1) AS img_id,
                        (SELECT image FROM stock_img WHERE stock_id = s.id LIMIT 1) AS img_image
                    ')
                    ->leftJoin('item_container as ic', function ($join) {
                        $join->on('i.id', '=', 'ic.container_id')
                            ->where('ic.container_is_item', '=', 1);
                    })
                    ->leftJoin('shelf as sh', 'sh.id', '=', 'i.shelf_id')
                    ->leftJoin('area as a', 'a.id', '=', 'sh.area_id')
                    ->leftJoin('site as si', 'si.id', '=', 'a.site_id')
                    ->leftJoin('stock as s', 's.id', '=', 'i.stock_id')
                    ->whereNull('ic.id')
                    ->where('i.is_container', '=', 1)
                    ->where('i.deleted', '=', 0)
                    ->orderBy('s.name')
                    ->get()
                    ->toArray();
    }

    static public function compileContainers()
    {
        $containers_in_use = ContainersModel::getContainersInUse();

        $containers_empty = ContainersModel::getContainersEmpty();
        $containers_item_empty = ContainersModel::getContainersItemEmpty();

        $container_array = [ 'container' => [],
                             'itemcontainer' => [] ];
        if (count($containers_in_use) > 0) {
            unset($row);
            foreach($containers_in_use as $row) {
                $containers_in_array = $container_array['container'];
                if (!is_null($row['c_id'])) {
                    if (!array_key_exists($row['c_id'], $containers_in_array)) {
                        $container_array['container'][$row['c_id']] = array('id' => $row['c_id'], 'name' => $row['c_name'], 'description' => $row['c_description'], 'count' => $row['object_count'],
                                                                            'img_id' => $row['simgcontainer_id'], 'img_image' => $row['simgcontainer_image'], 'location' => $row['c_location']);
                    }
                    $container_array['container'][$row['c_id']]['object'][] = array('ic_id' => $row['ic_id'], 'item_id' => $row['i_id'], 'id' => $row['s_id'], 'name' => $row['s_name'], 'description' => $row['s_description'],
                                                                                    'img_id' => $row['simg_id'], 'img_image' => $row['simg_image']);
                }
                $itemcontainers_in_array = $container_array['itemcontainer'];
                if (!is_null($row['icontainer_id'])) {
                    if (!array_key_exists($row['icontainer_id'], $itemcontainers_in_array)) {
                        $container_array['itemcontainer'][$row['icontainer_id']] = array('id' => $row['icontainer_id'], 'stock_id' => $row['scontainer_id'], 'name' => $row['scontainer_name'], 'description' => $row['scontainer_description'], 'count' => $row['object_count'],
                                                                                            'img_id' => $row['simgcontainer_id'], 'img_image' => $row['simgcontainer_image'], 'location' => $row['i_location']);
                    }
                    $container_array['itemcontainer'][$row['icontainer_id']]['object'][] = array('ic_id' => $row['icontainer_id'], 'item_id' => $row['i_id'], 'id' => $row['s_id'], 'name' => $row['s_name'], 'description' => $row['s_description'],
                                                                                                    'img_id' => $row['simg_id'], 'img_image' => $row['simg_image']);
                }
            }
        }

        if (count($containers_empty) > 0) {
            unset($row);
            foreach($containers_empty as $row) {
                $containers_in_array = $container_array['container'];
                if (!is_null($row['c_id'])) {
                    if (!array_key_exists($row['c_id'], $containers_in_array)) {
                        $container_array['container'][$row['c_id']] = array('id' => $row['c_id'], 'name' => $row['c_name'], 'description' => $row['c_description'], 'count' => 0,
                                                                            'img_id' => '', 'img_image' => '', 'location' => $row['location']);
                    }
                }
            }
        }

        if (count($containers_item_empty) > 0) {
            unset($row);
            foreach($containers_item_empty as $row) {
                $itemcontainers_in_array = $container_array['itemcontainer'];
                if (!is_null($row['i_id'])) {
                    if (!array_key_exists($row['i_id'], $itemcontainers_in_array)) {
                        $container_array['itemcontainer'][$row['i_id']] = array('id' => $row['i_id'], 'stock_id' => $row['s_id'], 'name' => $row['s_name'], 'description' => $row['s_description'], 
                                                                                            'count' => 0,
                                                                                            'img_id' => $row['img_id'], 'img_image' => $row['img_image'], 'location' => $row['location']);
                    }
                }
            }
        }

        return $container_array;
    }

    static public function addContainer($request) 
    {
        if (GeneralModel::checkShelfAreaMatch($request['shelf'], $request['area']) && GeneralModel::checkAreaSiteMatch($request['area'], $request['site'])) {
            $data = [
                    'name' => $request['container_name'], 
                    'description' => $request['container_description'], 
                    'shelf_id' => $request['shelf']
                    ];

            $insert = ContainersModel::create($data);

            $id = $insert->id;
                    
            $info = [
                'user' => GeneralModel::getUser(),
                'table' => 'container',
                'record_id' => $id,
                'field' => 'name',
                'new_value' => $request['container_name'],
                'action' => 'New record',
                'previous_value' => '',
            ];

            GeneralModel::updateChangelog($info);
            return redirect()->route('containers', ['success' => 'added']);
        }
    }

    static public function deleteContainer($request) 
    {   
        $id = $request['container_id'];
        $container = ContainersModel::find($id);

        if (!$container) {
            return redirect()->route('containers')->with('error', 'Container not found!');
        }

        $info = [
            'user' => GeneralModel::getUser(),
            'table' => 'container',
            'record_id' => $id,
            'field' => 'deleted',
            'new_value' => 1,
            'action' => 'Delete record',
            'previous_value' => null,
        ];

        GeneralModel::updateChangelog($info);

        $container->update([
            'deleted' => 1, 'updated_at' => now()
        ]);
        
        return redirect()->route('containers', ['success' => 'deleted']);
    }

    static public function deleteItemContainer($request) 
    {   
        $id = $request['container_id'];
        $container = DB::table('item')->where('id', '=', $id);

        if (!$container) {
            return redirect(GeneralModel::previousURL())->with('error', 'Container item not found.');
        }

        $update = DB::table('item')->where('id', '=', $id)->update([
            'is_container' => 0, 'updated_at' => now()
        ]);
        
        if ($update) {
                $info = [
                'user' => GeneralModel::getUser(),
                'table' => 'item',
                'record_id' => $id,
                'field' => 'is_container',
                'new_value' => 0,
                'action' => 'Update record',
                'previous_value' => 1,
            ];

            GeneralModel::updateChangelog($info);
            
            return redirect(GeneralModel::previousURL())->with('success', 'Item no longer a container.');
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'Unable to change item.');
        }

    }

    static public function editContainer($request) 
    {   
        
        $id = $request['container_id'];
        $name = $request['container_name'];
        $description = $request['container_description'];

        $container = ContainersModel::find($id);

        if (!$container) {
            return redirect()->route('containers')->with('error', 'Container not found!');
        }

        if ($name !== $container->toArray()['name']) {
            $info = [
                'user' => GeneralModel::getUser(),
                'table' => 'container',
                'record_id' => $id,
                'field' => 'name',
                'new_value' => $name,
                'action' => 'Update record',
                'previous_value' => null,
            ];
    
            GeneralModel::updateChangelog($info);
            
            $container->update([
                'name' => $name
            ]);
        }

        if ($description !== $container->toArray()['description']) {
            $info = [
                'user' => GeneralModel::getUser(),
                'table' => 'container',
                'record_id' => $id,
                'field' => 'description',
                'new_value' => $description,
                'action' => 'Update record',
                'previous_value' => null,
            ];
    
            GeneralModel::updateChangelog($info);
            
            $container->update([
                'description' => $description
            ]);
        }       

        return redirect()->route('containers', ['success' => 'updated']);
    }

    static public function unlinkFromContainer($request) 
    {
        $deleted_id = DB::table('item_container')
                    ->where('item_id', $request['item_id'])
                    ->value('id'); // Get the 'id' of the matching row

        // Perform the delete operation only if the row exists
        if ($deleted_id) {
            $info = [
                'user' => GeneralModel::getUser(),
                'table' => 'item_container',
                'record_id' => $deleted_id,
                'field' => 'item_id',
                'new_value' => null,
                'action' => 'Delete record',
                'previous_value' => $request['item_id'],
            ];
            GeneralModel::updateChangelog($info);

            DB::table('item_container')->where('id', $deleted_id)->delete();
            return redirect(GeneralModel::previousURL())->with('success', 'unlinked');
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'noRows');
        }
    }

    static public function linkToContainer($request, $redirect=null) 
    {
        $insert = DB::table('item_container')->insertGetId([
            'item_id' => $request['item_id'],
            'container_id' => $request['container_id'],
            'container_is_item' => $request['is_item'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $info = [
            'user' => GeneralModel::getUser(),
            'table' => 'item_container',
            'record_id' => $insert,
            'field' => 'item_id',
            'new_value' => $request['item_id'],
            'action' => 'New record',
            'previous_value' => '',
        ];

        GeneralModel::updateChangelog($info);

        if ($redirect !== null) {
            return ['success' => 'linked', 'id' => $insert ?? null];
        } else {
            return redirect(GeneralModel::previousURL())->with(['success' => 'linked', 'id' => $insert ?? null]);
        }
        
    }

    static public function getContainerChildrenInfo($container_id, $params=array())
    {
        $return = [];

        $instance = new self();
        $instance->setTable('item');

        $query = $instance->newQuery()
                            ->join('item_container', 'item_container.item_id', '=', 'item.id')
                            ->where('item_container.container_id', $container_id);

        if (!empty($params)) {
            foreach (array_keys($params) as $key) {
                $query->where($key, '=', $params[$key]);
            } 
        }

        $data = $query->orderBy($orderby ?? 'item.id') // Default to 'id' if $orderby is null
                    ->get()
                    ->toArray();

        return $data;
    }
}
