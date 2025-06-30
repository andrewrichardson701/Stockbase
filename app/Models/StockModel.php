<?php

namespace App\Models;

use Illuminate\Http\Request as HttpRequest;
use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralModel;
use App\Models\FavouritesModel;
use App\Models\ItemModel;
use App\Models\TagModel;
use Illuminate\Support\Facades\DB;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $sku
 * @property int|null $min_stock
 * @property int $is_cable
 * @property int|null $deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockModel whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockModel whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockModel whereIsCable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockModel whereMinStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockModel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockModel whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockModel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StockModel extends Model
{
    //
    protected $table = 'stock'; // Specify your table name
    protected $fillable = ['name', 'description', 'sku', 'min_stock', 'is_cable', 'deleted'];

    static public function getStockAjax($request, $limit, $offset)
    {
        $oos = isset($request['oos']) ? (int)$request['oos'] : 0;
        $site = isset($request['site']) ? $request['site'] : "0";
        $area = isset($request['area']) && !empty($request['area']) ? $request['area'] : "0";
        $name = isset($request['name']) ? $request['name'] : "";
        $sku = isset($request['sku']) ? $request['sku'] : "";
        $location = isset($request['location']) ? $request['location'] : "";
        $shelf = isset($request['shelf']) ? $request['shelf'] : "";
        $tag = isset($request['tag']) ? $request['tag'] : "";
        $manufacturer = isset($request['manufacturer']) ? $request['manufacturer'] : "";
        $type = isset($request['type']) ? $request['type'] : null; // for checking if this is a normal search or for the add / remove query

        // Confirm the site and area are a match
        if (($site !== "0" && $site !== '' && $site !== 0) && ($area !== "0" && $area !== '' && $area !== 0)) {
            if (GeneralModel::checkAreaSiteMatch($area, $site) == 0) {
                $area = 0;
            }
        }

        $instance = new self();
        $instance->setTable('stock');

        // Define the subquery for calculating item quantities (CTE equivalent)
        $quantityCTE = DB::table('item')
            ->select([
                'item.stock_id',
                'area.site_id',
                DB::raw('SUM(quantity) AS total_item_quantity'),
            ])
            ->join('shelf', 'item.shelf_id', '=', 'shelf.id')
            ->join('area', 'shelf.area_id', '=', 'area.id')
            ->where('item.deleted', 0)
            ->groupBy('item.stock_id', 'area.site_id');

        // Main query
        if ($type == null) {
            $query = $instance->select([
                    'stock.id AS stock_id',
                    'stock.name AS stock_name',
                    'stock.description AS stock_description',
                    'stock.sku AS stock_sku',
                    'stock.min_stock AS stock_min_stock',
                    'stock.is_cable AS stock_is_cable',
                    DB::raw("GROUP_CONCAT(DISTINCT area.name SEPARATOR ', ') AS area_names"),
                    'site.id AS site_id',
                    'site.name AS site_name',
                    'site.description AS site_description',
                    DB::raw('COALESCE(quantity_cte.total_item_quantity, 0) AS item_quantity'),
                    'tag_names.tag_names AS tag_names',
                    'tag_ids.tag_ids AS tag_ids',
                    'stock_img_image.stock_img_image',
                ])
                // ->distinct()
                ->leftJoin('item', 'stock.id', '=', 'item.stock_id')
                ->leftJoin('shelf', 'item.shelf_id', '=', 'shelf.id')
                ->leftJoin('area', 'shelf.area_id', '=', 'area.id')
                ->leftJoin('site', 'area.site_id', '=', 'site.id')
                ->leftJoin('manufacturer', 'item.manufacturer_id', '=', 'manufacturer.id')
                ->leftJoinSub(
                    DB::table('stock_img')
                        ->select(['stock_id', DB::raw('MIN(image) AS stock_img_image')])
                        ->groupBy('stock_id'),
                    'stock_img_image',
                    'stock_img_image.stock_id',
                    '=',
                    'stock.id'
                )
                ->leftJoinSub(
                    DB::table('stock_tag')
                        ->join('tag', 'stock_tag.tag_id', '=', 'tag.id')
                        ->select(['stock_tag.stock_id', DB::raw("GROUP_CONCAT(DISTINCT tag.name SEPARATOR ', ') AS tag_names")])
                        ->groupBy('stock_tag.stock_id'),
                    'tag_names',
                    'tag_names.stock_id',
                    '=',
                    'stock.id'
                )
                ->leftJoinSub(
                    DB::table('stock_tag')
                        ->select(['stock_tag.stock_id', DB::raw("GROUP_CONCAT(DISTINCT tag_id SEPARATOR ', ') AS tag_ids")])
                        ->groupBy('stock_tag.stock_id'),
                    'tag_ids',
                    'tag_ids.stock_id',
                    '=',
                    'stock.id'
                )
                ->leftJoinSub($quantityCTE, 'quantity_cte', function ($join) {
                    $join->on('stock.id', '=', 'quantity_cte.stock_id')
                        ->on('site.id', '=', 'quantity_cte.site_id');
                })
                ->where('stock.is_cable', 0)
                ->where('stock.deleted', 0)
                ->when($oos === 0, function ($query) {
                    $query->where('item.deleted', 0);
                })
                ->when($site !== '0', function ($query) use ($site) {
                    $query->where('site.id', $site);
                })
                ->when($area !== '0', function ($query) use ($area) {
                    $query->where('area.id', $area);
                })
                ->when(!empty($name), function ($query) use ($name) {
                    $query->where(function ($subQuery) use ($name) {
                        $subQuery->whereRaw("MATCH(stock.name) AGAINST (? IN NATURAL LANGUAGE MODE)", [$name])
                                ->orWhereRaw("MATCH(stock.description) AGAINST (? IN NATURAL LANGUAGE MODE)", [$name])
                                ->orWhere('stock.name', 'LIKE', "%{$name}%");
                    });
                })
                ->when(!empty($sku), function ($query) use ($sku) {
                    $query->where('stock.sku', 'LIKE', "%{$sku}%");
                })
                ->when(!empty($location), function ($query) use ($location) {
                    $query->where('area.name', 'LIKE', "%{$location}%");
                })
                ->when(!empty($shelf), function ($query) use ($shelf) {
                    $query->where('shelf.name', 'LIKE', "%{$shelf}%");
                })
                ->when(!empty($tag), function ($query) use ($tag) {
                    $query->where('tag_names', 'LIKE', "%{$tag}%");
                })
                ->when(!empty($manufacturer), function ($query) use ($manufacturer) {
                    $query->where('manufacturer.name', $manufacturer);
                })
                ->when($oos === 1, function ($query) {
                    $query->havingRaw('item_quantity IS NULL OR item_quantity = 0');
                })
                ->when($limit !== 0, function ($query) use ($limit) {
                    $query->limit($limit);
                })
                ->when($offset > 0, function ($query) use ($offset) {
                    $query->offset($offset);
                })
                ->groupBy([
                    'stock.id', 'stock.name', 'stock.description', 'stock.sku',
                    'stock.min_stock', 'stock.is_cable', 'site.id', 'site.name',
                    'site.description', 'stock_img_image.stock_img_image', 'quantity_cte.total_item_quantity',
                ])
                ->when($area != 0, function ($query) {
                    $query->groupBy('area.id');
                })
                ->orderBy('stock.name');
        } else {
            $query = $instance->select([
                    'stock.id AS stock_id',
                    'stock.name AS stock_name',
                    'stock.description AS stock_description',
                    'stock.sku AS stock_sku',
                    'stock.min_stock AS stock_min_stock',
                    'stock.is_cable AS stock_is_cable',
                    DB::raw("SUM(item.quantity) AS item_quantity"),
                    'tag_names.tag_names AS tag_names',
                    'tag_ids.tag_ids AS tag_ids',
                    'stock_img_image.stock_img_image',
                    ])
                // ->distinct()
                ->leftJoin('item', 'stock.id', '=', 'item.stock_id')
                ->leftJoin('manufacturer', 'item.manufacturer_id', '=', 'manufacturer.id')
                ->leftJoinSub(
                    DB::table('stock_img')
                        ->select(['stock_id', DB::raw('MIN(image) AS stock_img_image')])
                        ->groupBy('stock_id'),
                    'stock_img_image',
                    'stock_img_image.stock_id',
                    '=',
                    'stock.id'
                )
                ->leftJoinSub(
                    DB::table('stock_tag')
                        ->join('tag', 'stock_tag.tag_id', '=', 'tag.id')
                        ->select(['stock_tag.stock_id', DB::raw("GROUP_CONCAT(DISTINCT tag.name SEPARATOR ', ') AS tag_names")])
                        ->groupBy('stock_tag.stock_id'),
                    'tag_names',
                    'tag_names.stock_id',
                    '=',
                    'stock.id'
                )
                ->leftJoinSub(
                    DB::table('stock_tag')
                        ->select(['stock_tag.stock_id', DB::raw("GROUP_CONCAT(DISTINCT tag_id SEPARATOR ', ') AS tag_ids")])
                        ->groupBy('stock_tag.stock_id'),
                    'tag_ids',
                    'tag_ids.stock_id',
                    '=',
                    'stock.id'
                )
                ->where('stock.is_cable', 0)
                ->where('stock.deleted', 0)
                ->when($oos === 0, function ($query) {
                    $query->where('item.deleted', 0);
                })
                ->when(!empty($name), function ($query) use ($name) {
                    $query->where(function ($subQuery) use ($name) {
                        $subQuery->whereRaw("MATCH(stock.name) AGAINST (? IN NATURAL LANGUAGE MODE)", [$name])
                                ->orWhereRaw("MATCH(stock.description) AGAINST (? IN NATURAL LANGUAGE MODE)", [$name])
                                ->orWhere('stock.name', 'LIKE', "%{$name}%");
                    });
                })
                ->when(!empty($sku), function ($query) use ($sku) {
                    $query->where('stock.sku', 'LIKE', "%{$sku}%");
                })
                ->when(!empty($tag), function ($query) use ($tag) {
                    $query->where('tag_names', 'LIKE', "%{$tag}%");
                })
                ->when(!empty($manufacturer), function ($query) use ($manufacturer) {
                    $query->where('manufacturer.name', $manufacturer);
                })
                ->when($oos === 1, function ($query) {
                    $query->havingRaw('item_quantity IS NULL OR item_quantity = 0');
                })
                ->when($limit !== 0, function ($query) use ($limit) {
                    $query->limit($limit);
                })
                ->when($offset > 0, function ($query) use ($offset) {
                    $query->offset($offset);
                })
                ->groupBy([
                    'stock.id', 'stock.name', 'stock.description', 'stock.sku',
                    'stock.min_stock', 'stock.is_cable',
                    'stock_img_image.stock_img_image',
                ])
                ->orderBy('stock.name');
        }
        return [
            'query' => $query,
            'data' => [
                'site' => $site,
                'area' => $area,
                'shelf' => $shelf,
                'name' => $name,
                'sku' => $sku,
                'tag' => $tag,
                'location' => $location,
                'manufacturer' => $manufacturer,
                'oos' => $oos,
            ],
        ];
    }

    static public function returnStockAjax($request) 
    {
        $results = []; // to return
        if (isset($request['request-inventory']) && $request['request-inventory'] == 1) {
            $query_type = $request['type'] ?? null;
            $all_rows_data = StockModel::getStockAjax($request, 0, -1);
            if ($all_rows_data == null) {
                return null;
            }
            $all_rows_count = count($all_rows_data['query']->get()->toArray());

            if (isset($request['rows'])) {
                if ($request['rows'] == 50 || $request['rows'] == 100) {
                    $results_per_page = htmlspecialchars($request['rows']);
                } else {
                    $results_per_page = 10;
                }
            } else {
                $results_per_page = 10;
            }

            $total_pages = ceil($all_rows_count / $results_per_page);

            $current_page = isset($request['page']) ? intval($request['page']) : 1;
            if ($current_page < 1) {
                $current_page = 1;
            } elseif ($current_page > $total_pages) {
                $current_page = $total_pages;
            } 

            $offset = ($current_page - 1) * $results_per_page;
            if ($offset < 0) {
                $offset = $results_per_page;
            }

            $requested_rows_data = StockModel::getStockAjax($request, $results_per_page, $offset);
            $requested_rows_array = $requested_rows_data['query']->get()->toArray();

            $page_number_area = StockModel::getPageNumberArea($total_pages, $current_page);
                
            $results[-1]['site'] = $site = $requested_rows_data['data']['site'] ?? 0;
            $results[-1]['area'] = $area = $requested_rows_data['data']['area'] ?? 0;
            $results[-1]['shelf'] = $shelf = $requested_rows_data['data']['shelf'] ?? 0;
            $results[-1]['name'] = $name = $requested_rows_data['data']['name'] ?? null;
            $results[-1]['sku'] = $sku = $requested_rows_data['data']['sku'] ?? null;
            $results[-1]['location'] = $location = $requested_rows_data['data']['location'] ?? null;
            $results[-1]['tag'] = $tag = $requested_rows_data['data']['tag'] ?? null;
            $results[-1]['manufacturer'] = $manufacturer = $requested_rows_data['data']['manufacturer'] ?? null;
            $results[-1]['total-pages'] = $total_pages;
            $results[-1]['page-number-area'] = $page_number_area;
            $results[-1]['page'] = $page = $current_page;
            $results[-1]['rows'] = $rows = $results_per_page;
            $results[-1]['oos'] = $oos = $requested_rows_data['data']['oos'] ?? null;
            $results[-1]['url'] = "./?oos=$oos&site=$site&area=$area&name=$name&sku=$sku&shelf=$shelf&manufacturer=$manufacturer&tag=$tag&page=$page&rows=$rows";
            $results[-1]['sql'] = GeneralModel::interpolatedQuery($requested_rows_data['query']->toSql(),$requested_rows_data['query']->getBindings());
            $results[-1]['areas'] = GeneralModel::allDistinctAreas($site, 0);
            $results[-1]['query_data'] = $all_rows_data['query']->get()->toArray();
            $results[-1]['siteNeeded'] = $site > 0 ? 0 : 1;

            $img_directory = 'img/stock/';

            if (count($requested_rows_array) < 1) {
                $result = "<tr><td colspan=100%>No Inventory Found</td></tr>";
                $results[] = $result;
            } else {
                foreach ($requested_rows_array as $row) {
                    $stock_id = $row['stock_id'];
                    $stock_img_file_name = $row['stock_img_image'];
                    $stock_name = $row['stock_name'];
                    $stock_sku = $row['stock_sku'];
                    $stock_quantity_total = $row['item_quantity'];
                    $stock_locations = $row['area_names'] ?? null;
                    $stock_site_id = $row['site_id'] ?? null;
                    $stock_site_name = $row['site_name'] ?? null;
                    $stock_tag_names = ($row['tag_names'] !== null) ? explode(", ", $row['tag_names']) : '---';
                    

                    // Echo each row (inside of SQL results)

                    $result =
                    '<tr class="vertical-align align-middle highlight" id="'.$stock_id.'">';
                    if ($query_type !== null) { 
                        $result .= '<td class="align-middle" id="'.$stock_id.'-id">'.$stock_id.'</td>';
                    } else {
                        $result .= '<td class="align-middle" id="'.$stock_id.'-id" hidden>'.$stock_id.'</td>';
                    }
                    $result .= '<td class="align-middle" id="'.$stock_id.'-img-td">';
                    if (!is_null($stock_img_file_name)) {
                        $result .= '<img id="'.$stock_id.'-img" class="inv-img-main thumb" src="'.url($img_directory.$stock_img_file_name).'" alt="'.$stock_name.'" onclick="modalLoad(this)" />';
                    }
                    $result .= '</td>
                        <td class="align-middle gold" id="'.$stock_id.'-name" style="white-space:wrap"><a class="link" href="'.url('stock/'.$stock_id.'/'.$query_type).'">'.$stock_name.'</a></td>
                        <td class="align-middle viewport-large-empty" id="'.$stock_id.'-sku">'.$stock_sku.'</td>
                        <td class="align-middle" id="'.$stock_id.'-quantity">'; 
                    if ($stock_quantity_total == 0) {
                        $result .= '<or class="red" title="Out of Stock">0 <i class="fa fa-warning" /></or>';
                    } else {
                        $result .= $stock_quantity_total;
                    }
                    $result .= '</td>';
                    if ($query_type == null) {
                        if ($site == 0) { $result .= '<td class="align-middle link gold" style="white-space: nowrap !important;"id="'.$stock_id.'-site" onclick="navPage(updateQueryParameter(\'\', \'site\', \''.$stock_site_id.'\'))">'.$stock_site_name.'</td>'; }
                        $result .= '</td>
                        <td class="align-middle" id="'.$stock_id.'-location">'.$stock_locations.'</td>
                        ';
                        $result .= '<td class="align-middle viewport-large-empty" style="white-space: wrap" id="'.$stock_id.'-tag">';
                        if (is_array($stock_tag_names)) {
                            for ($o=0; $o < count($stock_tag_names); $o++) {
                                $divider = $o < count($stock_tag_names)-1 ? ', ' : '';
                                $result .= '<or class="gold link" onclick="navPage(updateQueryParameter(\'\', \'tag\', \''.$stock_tag_names[$o].'\'))">'.$stock_tag_names[$o].'</or>'.$divider;
                            }
                        } 
                    }
                    $result .= '</tr>';
                    
                    $results[] = $result;
                }
            }
        } else {
            $result = null;
        }

        return $results;
    }

    static public function getPageNumberArea($total_pages, $current_page) 
    {
        $pageNumberArea = '';

        if ($total_pages > 1) {
            if ($current_page > 1) {
                $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.($current_page - 1).'\') + \'\')"><</or>';
            }
            if ($total_pages > 5) {
                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $current_page) {
                        $pageNumberArea .= '<span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">' . $i . '</span>';
                        // onclick="navPage(updateQueryParameter(\'\', \'page\', \'$i\'))"
                    } elseif ($i == 1 && $current_page > 5) {
                        $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or><or style="padding-left:5px;padding-right:5px">...</or>';  
                    } elseif ($i < $current_page && $i >= $current_page-2) {
                        $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';
                    } elseif ($i > $current_page && $i <= $current_page+2) {
                        $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';
                    } elseif ($i == $total_pages) {
                        $pageNumberArea .= '<or style="padding-left:5px;padding-right:5px">...</or><or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';  
                    }
                }
            } else {
                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $current_page) {
                        $pageNumberArea .= '<span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">' . $i . '</span>';
                        // onclick="navPage(updateQueryParameter(\'\', \'page\', \'$i\'))"
                    } else {
                        $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';
                    }
                }
            }

            if ($current_page < $total_pages) {
                $pageNumberArea .= '<or class="gold clickable" style="padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.($current_page + 1).'\') + \'\')">></or>';
            }  
        }
        
        return $pageNumberArea;
    }

    static public function getNearbyStockAjax($request) 
    {
        $results = [];
        if (isset($request['item_id']) && is_numeric($request['item_id'])) {
            $item_id = htmlspecialchars($request['item_id']);

            if (isset($rquest['name']) && $request['name'] !== null) {
                $name = htmlspecialchars($request['name']);
            } else {
                $name = '';
            }

            $instance = new self();
            $instance->setTable('item as i');

            if (isset($request['is_item']) && $request['is_item'] == 1) {
                $rows = $instance->select(
                                ['st.id as st_id',
                                'st.name as st_name',
                                'st.sku as st_sku',
                                'i.serial_number as i_serial_number',
                                DB::raw('COUNT(i.quantity) as quantity'),
                                DB::raw('MIN(i.id) as item_id')] // Ensure we get the lowest item.id, without any NULL values
                            )
                            ->from('item as i') // Explicitly selecting from the 'item' table
                            ->join('stock as st', 'i.stock_id', '=', 'st.id') // Joining stock table
                            ->join('shelf as sh', 'i.shelf_id', '=', 'sh.id') // Joining shelf table
                            ->leftJoin('item_container as ic', 'i.id', '=', 'ic.item_id') // Left join with item_container (ic)
                            ->leftJoin('item_container as ic2', function($join) {
                                $join->on('i.id', '=', 'ic2.container_id')
                                    ->where('ic2.container_is_item', '=', 1); // Left join condition for ic2
                            })
                            ->where('i.shelf_id', function($query) use ($item_id) {
                                $query->select('shelf_id')
                                    ->from('item')
                                    ->where('id', $item_id);
                            })
                            ->where('i.is_container', 0) // Ensuring the item is not a container
                            ->where('i.deleted', 0) // Ensuring the item is not deleted
                            ->where('i.id', '!=', $item_id) // Excluding the current item from the query
                            ->whereNull('ic.item_id') // Ensuring that the item is not in a container
                            ->whereNull('ic2.container_id') // Ensuring the item is not a container in ic2
                            ->when($name !== '', function ($query) use ($name) {
                                $query->where('st.name', 'LIKE', "%$name%");
                            })
                            ->groupBy('st.id', 'st.name', 'st.sku', 'i.serial_number')
                            ->orderBy('st_name')
                            ->orderBy('i_serial_number')
                            ->get()
                            ->toArray();
            } else {
                $rows = $instance->select(
                                ['st.id as st_id',
                                'st.name as st_name',
                                'st.sku as st_sku',
                                'i.serial_number as i_serial_number',
                                DB::raw('COUNT(i.quantity) as quantity'),
                                DB::raw('MIN(i.id) as item_id')] // Ensure we get the smallest item.id
                            )
                            ->join('stock as st', 'i.stock_id', '=', 'st.id') // Join with stock
                            ->join('shelf as sh', 'i.shelf_id', '=', 'sh.id') // Join with shelf
                            ->leftJoin('item_container as ic', 'i.id', '=', 'ic.item_id') // Left join with item_container (ic)
                            ->leftJoin('item_container as ic2', function($join) {
                                $join->on('i.id', '=', 'ic2.container_id')
                                    ->where('ic2.container_is_item', '=', 0); // Left join condition for ic2
                            })
                            ->where('i.shelf_id', function($query) use ($item_id) {
                                $query->select('shelf_id')
                                    ->from('container')
                                    ->where('id', $item_id);
                            }) // Filtering by shelf_id based on container
                            ->where('i.is_container', 0) // Ensuring the item is not a container
                            ->where('i.deleted', 0) // Ensuring the item is not deleted
                            ->where('i.id', '!=', $item_id) // Excluding the current item from the query
                            ->whereNull('ic.item_id') // Ensuring that the item is not in a container
                            ->whereNull('ic2.container_id') // Ensuring the item is not a container in ic2
                            ->when($name !== '', function ($query) use ($name) {
                                $query->where('st.name', 'LIKE', "%$name%"); // Optional filtering by stock name
                            })
                            ->groupBy('st.id', 'st.name', 'st.sku', 'i.serial_number') // Grouping by stock details and item serial number
                            ->orderBy('st_name') // Ordering by stock name
                            ->orderBy('i_serial_number') // Ordering by item serial number
                            ->get()
                            ->toArray(); // Fetch results as an array
            }

            if (count($rows) > 0) {
                $results['count'] = count($rows);
                foreach ($rows as $row) {
                    $results['data'][] = array('stock_id' => $row['st_id'], 'stock_name' => $row['st_name'], 'stock_sku' => $row['st_sku'], 
                                            'item_serial_number' => $row['i_serial_number'], 
                                            'quantity' => $row['quantity'], 
                                            'item_id' => $row['item_id']);
                    
                }
            } else {
                $results['count'] = 0;
            }
        }
        return $results;
    }

    static public function getStockData($stock_id)
    {
        $return = [];
        $return['img_data'] = [];

        $instance = new self();
        $instance->setTable('stock');

        $data = $instance->selectRaw('stock.id AS stock_id, 
                                        stock.name AS stock_name, 
                                        stock.description AS stock_description, 
                                        stock.sku AS stock_sku, 
                                        stock.min_stock AS stock_min_stock, 
                                        stock.is_cable AS stock_is_cable,
                                        stock_img.id AS stock_img_id, 
                                        stock_img.stock_id AS stock_img_stock_id, 
                                        stock_img.image AS stock_img_image, 
                                        stock.deleted AS stock_deleted')
                        ->leftJoin('stock_img', 'stock.id', '=', 'stock_img.stock_id')
                        ->where('stock.id', '=', $stock_id)
                        ->get()
                        ->toarray();

        $r = 0;
        if (count($data) > 0) {
            foreach ($data as $row) {
                if ($r == 0) {
                    $return = ['id' => $row['stock_id'],
                        'name' => $row['stock_name'],
                        'description' => $row['stock_description'],
                        'sku' => $row['stock_sku'],
                        'min_stock' => $row['stock_min_stock'],
                        'is_cable' => $row['stock_is_cable'],
                        'deleted' => $row['stock_deleted']
                        ];
                }
                if (!empty($row['stock_img_id'])) {
                    $return['img_data']['rows'][$row['stock_img_id']]['id'] = $row['stock_img_id'];
                    $return['img_data']['rows'][$row['stock_img_id']]['stock_id'] = $row['stock_img_stock_id'];
                    $return['img_data']['rows'][$row['stock_img_id']]['image'] = $row['stock_img_image'];
                }
                $r++;
            }
        }
        $return['img_data']['count'] = count($return['img_data']['rows'] ?? []) ;
        $return['count'] = $r;

        return $return;
    }

    static public function getStockDataByTag($tag_id)
    {
        $return['rows'] = [];
        $return['count'] = 0;

        $instance = new self();
        $instance->setTable('stock');

        $data = $instance->distinct()
                            ->selectRaw('stock.id AS stock_id, 
                                        stock.name AS stock_name, 
                                        stock.description AS stock_description, 
                                        stock.sku AS stock_sku, 
                                        stock.min_stock AS stock_min_stock, 
                                        stock.is_cable AS stock_is_cable,
                                        stock.deleted AS stock_deleted,
                                        SUM(item.quantity) AS quantity')
                            ->leftJoin('stock_tag', 'stock_tag.stock_id', '=', 'stock.id')
                            ->leftJoin('tag', 'tag.id', '=', 'stock_tag.tag_id')
                            ->leftJoin('item', 'item.stock_id', '=', 'stock.id')
                            ->where('tag.id', '=', $tag_id)
                            ->groupBy('stock.id', 
                                    'stock.name', 
                                    'stock.description', 
                                    'stock.sku', 
                                    'stock.min_stock', 
                                    'stock.is_cable', 
                                    'stock.deleted')
                            ->get()
                            ->toArray();

        $img_instance = new self();
        $img_instance->setTable('stock_img');          

        if (count($data) > 0) {
            foreach ($data as $row) {
                $return['rows'][$row['stock_id']] = ['id' => $row['stock_id'],
                    'name' => $row['stock_name'],
                    'description' => $row['stock_description'],
                    'sku' => $row['stock_sku'],
                    'min_stock' => $row['stock_min_stock'],
                    'is_cable' => $row['stock_is_cable'],
                    'deleted' => $row['stock_deleted'],
                    'quantity' => $row['quantity']
                    ];
                
                $img_data = $img_instance->distinct()->selectRaw('stock_img.id AS id, 
                                                    stock_img.stock_id AS stock_id, 
                                                    stock_img.image AS image')
                                        ->where('stock_img.stock_id', '=', $row['stock_id'])
                                        ->orderby('stock_img.id')
                                        ->get()
                                        ->toarray();
                
                $return['rows'][$row['stock_id']]['img_data']['rows'] = $img_data;
                $return['rows'][$row['stock_id']]['img_data']['count'] = count($return['rows'][$row['stock_id']]['img_data']['rows']);
            }
        }
        
        $return['count'] = count($return['rows']);

        return $return;
    }

    static public function checkFavourited($stock_id) 
    {
        $favourites_list = FavouritesModel::getUserFavourites(GeneralModel::getUser()['id']);

        $favourites_rows = $favourites_list['rows'];

        if (array_key_exists($stock_id, $favourites_rows)){
            return 1;
        }

        return 0;
    }

    static public function getStockInvData($stock_id, $is_cable)
    {
        $stock_inv_data = [];
        $stock_inv_data['rows'] = [];
        $stock_inv_data['manufacturers'] = [];

        $instance = new self();
        $instance->setTable('stock AS s');

        if ($is_cable == 0) {
            $rows = $instance->selectRaw("
                    s.id AS stock_id, s.name AS stock_name, s.description AS stock_description, 
                    s.sku AS stock_sku, s.min_stock AS stock_min_stock, 
                    a.id AS area_id, a.name AS area_name, 
                    sh.id AS shelf_id, sh.name AS shelf_name, 
                    si.id AS site_id, si.name AS site_name, si.description AS site_description,

                    (SELECT SUM(i.quantity) 
                    FROM item AS i 
                    WHERE i.stock_id = s.id AND i.shelf_id = sh.id
                    ) AS item_quantity,

                    (SELECT GROUP_CONCAT(DISTINCT m.name ORDER BY m.name SEPARATOR ', ') 
                    FROM item AS i 
                    INNER JOIN manufacturer AS m ON m.id = i.manufacturer_id 
                    WHERE i.stock_id = s.id
                    ) AS manufacturer_names,

                    (SELECT GROUP_CONCAT(DISTINCT m.id ORDER BY m.name SEPARATOR ', ') 
                    FROM item AS i 
                    INNER JOIN manufacturer AS m ON m.id = i.manufacturer_id 
                    WHERE i.stock_id = s.id
                    ) AS manufacturer_ids,

                    (SELECT GROUP_CONCAT(DISTINCT t.name ORDER BY t.name SEPARATOR ', ') 
                    FROM stock_tag AS st
                    INNER JOIN tag AS t ON st.tag_id = t.id 
                    WHERE st.stock_id = s.id
                    ORDER BY t.name
                    ) AS tag_names,

                    (SELECT GROUP_CONCAT(DISTINCT t.id ORDER BY t.name SEPARATOR ', ') 
                    FROM stock_tag AS st
                    INNER JOIN tag AS t ON st.tag_id = t.id
                    WHERE st.stock_id = s.id
                    ORDER BY t.name
                    ) AS tag_ids
                ")
                ->leftJoin('item AS i', 's.id', '=', 'i.stock_id')
                ->leftJoin('shelf AS sh', 'i.shelf_id', '=', 'sh.id')
                ->leftJoin('area AS a', 'sh.area_id', '=', 'a.id')
                ->leftJoin('site AS si', 'a.site_id', '=', 'si.id')
                ->where('s.id', '=', $stock_id)
                ->where('quantity', '>', 0)
                ->groupBy(
                    's.id', 's.name', 's.description', 's.sku', 's.min_stock', 
                    'si.id', 'si.name', 'si.description', 
                    'a.id', 'a.name', 
                    'sh.id', 'sh.name'
                )
                ->orderBy('si.id')
                ->orderBy('a.name')
                ->orderBy('sh.name')
                ->get()
                ->toArray();
        } elseif ($is_cable == 1) {
            $rows = $instance->selectRaw("
                    s.id AS stock_id, s.name AS stock_name, s.description AS stock_description, 
                    s.sku AS stock_sku, s.min_stock AS stock_min_stock, 
                    a.id AS area_id, a.name AS area_name, 
                    sh.id AS shelf_id, sh.name AS shelf_name, 
                    si.id AS site_id, si.name AS site_name, si.description AS site_description,

                    (SELECT SUM(ci.quantity) 
                    FROM cable_item AS ci
                    WHERE ci.stock_id = s.id AND ci.shelf_id = sh.id
                    ) AS item_quantity
                ")
                ->leftJoin('cable_item AS ci', 's.id', '=', 'ci.stock_id')
                ->leftJoin('shelf AS sh', 'ci.shelf_id', '=', 'sh.id')
                ->leftJoin('area AS a', 'sh.area_id', '=', 'a.id')
                ->leftJoin('site AS si', 'a.site_id', '=', 'si.id')
                ->where('s.id', '=', $stock_id)
                ->groupBy(
                    's.id', 's.name', 's.description', 's.sku', 's.min_stock', 
                    'si.id', 'si.name', 'si.description', 
                    'a.id', 'a.name', 
                    'sh.id', 'sh.name'
                )
                ->orderBy('si.id')
                ->orderBy('a.name')
                ->orderBy('sh.name')
                ->get()
                ->toArray();
        }

        foreach ($rows as $row) {
            if ($is_cable == 0) {
                $stock_manufacturer_ids = $row['manufacturer_ids'];
                $stock_manufacturer_names = $row['manufacturer_names'];
                $stock_tag_ids = $row['tag_ids'];
                $stock_tag_names = $row['tag_names'];

                $stock_tag_data = [];
                if ($stock_tag_ids !== null) {
                    for ($n=0; $n < count(explode(", ", $stock_tag_ids)); $n++) {
                        $stock_tag_data[$n] = array('id' => explode(", ", $stock_tag_ids)[$n],
                                                            'name' => explode(", ", $stock_tag_names)[$n]);
                    }
                } else {
                    $stock_tag_data = null;
                }

                $stock_manufacturer_data = [];
                if ($stock_manufacturer_ids !== null) {
                    for ($n=0; $n < count(explode(", ", $stock_manufacturer_ids)); $n++) {
                        $stock_manufacturer_data[$n] = array('id' => explode(", ", $stock_manufacturer_ids)[$n],
                                                            'name' => explode(", ", $stock_manufacturer_names)[$n]);
                    }
                } else {
                    $stock_manufacturer_data = null;
                }
            } else {
                $stock_manufacturer_data = null;
                $stock_tag_data = null;
            }

            $stock_inv_data['rows'][] = array('id' => $row['stock_id'],
                                        'name' => $row['stock_name'],
                                        'sku' => $row['stock_sku'],
                                        'min_stock' => $row['stock_min_stock'],
                                        'quantity' => $row['item_quantity'],
                                        'shelf_id' => $row['shelf_id'],
                                        'shelf_name' => $row['shelf_name'],
                                        'area_id' => $row['area_id'],
                                        'area_name' => $row['area_name'],
                                        'site_id' => $row['site_id'],
                                        'site_name' => $row['site_name']
                                        ); 
        }

        $stock_inv_data['count'] = count($stock_inv_data['rows']) ?? 0;
        $stock_inv_data['tags'] = $stock_tag_data ?? [];
        $stock_inv_data['manufacturers'] = $stock_manufacturer_data ?? [];

        $total_quantity = 0;
        foreach ($stock_inv_data['rows'] as $row) {
            $total_quantity = $total_quantity + (int)$row['quantity'];
        }
        $stock_inv_data['total_quantity'] = $total_quantity;
        
        return $stock_inv_data;
    }

    static public function getContainerChildren($container_id, $is_item) 
    {
        $return = [];
        $count = 0;
        
        $instance = new self();
        $instance->setTable('item_container');
        $children = $instance->select(['item.id AS item_id', 'stock.id AS stock_id', 'stock.name AS stock_name',
                                'item.upc AS item_upc', 'item.quantity AS item_quantity', 'item.cost AS item_cost',
                                'item.serial_number AS item_serial_number', 'item.comments AS item_comments', 'item.manufacturer_id AS item_manufacturer_id',
                                'item.shelf_id AS item_shelf_id', 'item.is_container AS item_is_container', 'item.deleted AS item_deleted'])
                                ->join('item', 'item.id', '=', 'item_container.item_id')
                                ->join('stock', 'stock.id', '=', 'item.stock_id')
                                ->orderby('container_id')
                                ->orderby('container_is_item')
                                ->orderby('item_id')
                                ->where('container_id', '=', $container_id)
                                ->where('container_is_item', '=', $is_item)
                                ->get()
                                ->toArray();

        foreach($children as $child) {
            $return['rows'][] = $child;
            $count++;
        }
        $return['count'] = $count;

        return $return;
    }

    static public function getAllContainerData($stock_id)
    {
        $return = [];
        $count = 0;
        $stock_item_data = StockModel::getStockItemData($stock_id, 0);

        foreach($stock_item_data['rows'] as $row) {
            if ($row['is_container'] == 1) {
                $container_data = StockModel::getContainerChildren($row['item_id'], 1);
                $return['rows'][$row['item_id']] = $container_data;
                $count ++;
            }
        }
        $return['count'] = $count;

        return $return;
    }

    static public function getContainerData($container_id, $is_item=0) 
    {
        $return = [];
        $count = 0;

        $instance = new self();
        $instance->setTable('item_container AS ic');

        $data = $instance->selectRaw('
                            s.name AS stock_name, 
                            s.id AS stock_id, 
                            i.id AS item_id, 
                            i.upc AS item_upc, 
                            i.serial_number AS item_serial_number, 
                            i.comments AS item_comments, 
                            ic.id AS ic_id
                        ')
                        ->join('item as i', function ($join) {
                            $join->on('i.id', '=', 'ic.container_id')
                                ->where('ic.container_is_item', '=', 1);
                        })
                        ->join('stock as s', 's.id', '=', 'i.stock_id')
                        ->where('ic.container_id', '=', $container_id)
                        ->where('ic.container_is_item', '=', $is_item)
                        ->get()
                        ->toArray();

        foreach ($data as $row) {
            $return['rows'][$row['item_id']] = $row;
            $count++;
        }
        $return['count'] = $count;#
        
        return $return;
    }

    static public function getDistinctStockItemData($stock_id, $is_cable)
    {
        $stock_inv_data = [];

        $instance = new self();
        $instance->setTable('stock');

        $stock_item_data = $stock_tag_data = ['rows' => []];
        
        if ($is_cable == 0) {
            $rows = $instance->selectRaw("
                                    stock.id AS stock_id, 
                                    stock.name AS stock_name, 
                                    stock.description AS stock_description, 
                                    stock.sku AS stock_sku, 
                                    stock.min_stock AS stock_min_stock, 
                                    area.id AS area_id, 
                                    area.name AS area_name, 
                                    shelf.id AS shelf_id, 
                                    shelf.name AS shelf_name, 
                                    site.id AS site_id, 
                                    site.name AS site_name, 
                                    site.description AS site_description, 
                                    item.serial_number AS item_serial_number, 
                                    item.upc AS item_upc, 
                                    item.cost AS item_cost, 
                                    item.comments AS item_comments, 
                                    item.is_container AS item_is_container,
                                    (SELECT SUM(quantity) 
                                    FROM item AS i
                                    WHERE i.stock_id = stock.id 
                                    AND i.shelf_id = shelf.id 
                                    AND i.manufacturer_id = manufacturer.id 
                                    AND i.serial_number = item.serial_number 
                                    AND (
                                        i.upc = item.upc OR (i.upc IS NULL AND item.upc IS NULL)
                                    )
                                    AND i.comments = item.comments 
                                    AND i.cost = item.cost) AS item_quantity, 
                                    manufacturer.id AS manufacturer_id, 
                                    manufacturer.name AS manufacturer_name, 
                                    (SELECT GROUP_CONCAT(DISTINCT manufacturer.id ORDER BY manufacturer.name SEPARATOR ', ') 
                                        FROM item 
                                        INNER JOIN manufacturer ON manufacturer.id = item.manufacturer_id 
                                        WHERE item.stock_id = stock.id
                                    ) AS manufacturer_ids,
                                    (SELECT GROUP_CONCAT(DISTINCT manufacturer.name ORDER BY manufacturer.name SEPARATOR ', ') 
                                        FROM item 
                                        INNER JOIN manufacturer ON manufacturer.id = item.manufacturer_id 
                                        WHERE item.stock_id = stock.id
                                    ) AS manufacturer_names,
                                    (SELECT GROUP_CONCAT(DISTINCT tag.name ORDER BY tag.name SEPARATOR ', ') 
                                    FROM stock_tag 
                                    INNER JOIN tag ON stock_tag.tag_id = tag.id 
                                    WHERE stock_tag.stock_id = stock.id 
                                    ORDER BY tag.name) AS tag_names, 
                                    (SELECT GROUP_CONCAT(DISTINCT tag.id ORDER BY tag.name SEPARATOR ', ') 
                                    FROM stock_tag 
                                    INNER JOIN tag ON stock_tag.tag_id = tag.id 
                                    WHERE stock_tag.stock_id = stock.id 
                                    ORDER BY tag.name) AS tag_ids
                                ")
                                ->leftJoin('item', 'stock.id', '=', 'item.stock_id')
                                ->leftJoin('shelf', 'item.shelf_id', '=', 'shelf.id')
                                ->leftJoin('area', 'shelf.area_id', '=', 'area.id')
                                ->leftJoin('site', 'area.site_id', '=', 'site.id')
                                ->leftJoin('manufacturer', 'item.manufacturer_id', '=', 'manufacturer.id')
                                ->where('stock.id', $stock_id)
                                ->where('quantity', '!=', 0)
                                ->groupBy([
                                    'stock.id', 'stock_name', 'stock_description', 'stock_sku', 'stock_min_stock', 
                                    'site_id', 'site_name', 'site_description', 
                                    'area_id', 'area_name', 
                                    'shelf_id', 'shelf_name', 
                                    'manufacturer_name', 'manufacturer_id', 
                                    'item_serial_number', 'item_upc', 'item_comments', 'item_cost', 'item_is_container'
                                ])
                                ->orderBy('site.id')
                                ->orderBy('area.name')
                                ->orderBy('shelf.name')
                                ->get()
                                ->toArray();

        } elseif ($is_cable == 1) {
            $rows = $instance->selectRaw("
                        stock.id AS stock_id, 
                        stock.name AS stock_name, 
                        stock.description AS stock_description, 
                        stock.sku AS stock_sku, 
                        stock.min_stock AS stock_min_stock, 
                        area.id AS area_id, 
                        area.name AS area_name, 
                        shelf.id AS shelf_id, 
                        shelf.name AS shelf_name, 
                        site.id AS site_id, 
                        site.name AS site_name, 
                        site.description AS site_description, 
                        cable_item.cost AS item_cost, 
                        (SELECT SUM(quantity) FROM cable_item 
                            WHERE cable_item.stock_id = stock.id 
                            AND cable_item.shelf_id = shelf.id) AS item_quantity, 
                        (SELECT GROUP_CONCAT(DISTINCT tag.name ORDER BY tag.name SEPARATOR ', ') 
                            FROM stock_tag 
                            INNER JOIN tag ON stock_tag.tag_id = tag.id 
                            WHERE stock_tag.stock_id = stock.id 
                            ORDER BY tag.name) AS tag_names, 
                        (SELECT GROUP_CONCAT(DISTINCT tag.id ORDER BY tag.name SEPARATOR ', ') 
                            FROM stock_tag 
                            INNER JOIN tag ON stock_tag.tag_id = tag.id 
                            WHERE stock_tag.stock_id = stock.id 
                            ORDER BY tag.name) AS tag_ids")
                    ->leftJoin('cable_item', 'stock.id', '=', 'cable_item.stock_id')
                    ->leftJoin('shelf', 'cable_item.shelf_id', '=', 'shelf.id')
                    ->leftJoin('area', 'shelf.area_id', '=', 'area.id')
                    ->leftJoin('site', 'area.site_id', '=', 'site.id')
                    ->where('stock.id', '=', $stock_id)
                    ->where('quantity', '!=', 0)
                    ->groupBy([
                        'stock.id', 'stock_name', 'stock_description', 'stock_sku', 'stock_min_stock', 
                        'site_id', 'site_name', 'site_description', 
                        'area_id', 'area_name', 
                        'shelf_id', 'shelf_name',
                        'item_cost'
                    ])
                    ->orderBy('site.id')
                    ->orderBy('area.name')
                    ->orderBy('shelf.name')
                    ->get()
                    ->toArray();
        }

        foreach ($rows as $row) {
            if ($is_cable == 0) {
                $stock_manufacturer_ids = $row['manufacturer_ids'];
                $stock_manufacturer_names = $row['manufacturer_names'];
                $stock_tag_ids = $row['tag_ids'];
                $stock_tag_names = $row['tag_names'];

                $stock_tag_data = [];
                if ($stock_tag_ids !== null) {
                    for ($n=0; $n < count(explode(", ", $stock_tag_ids)); $n++) {
                        $stock_tag_data[$n] = array('id' => explode(", ", $stock_tag_ids)[$n],
                                                            'name' => explode(", ", $stock_tag_names)[$n]);
                    }
                } else {
                    $stock_tag_data = null;
                }

            } else {
                $stock_tag_data = null;
            }

            $stock_item_data['rows'][] = array('id' => $row['stock_id'],
                                        'name' => $row['stock_name'],
                                        'sku' => $row['stock_sku'],
                                        'quantity' => $row['item_quantity'] ?? 0,
                                        'min_stock' => $row['stock_min_stock'],
                                        'shelf_id' => $row['shelf_id'],
                                        'shelf_name' => $row['shelf_name'],
                                        'area_id' => $row['area_id'],
                                        'area_name' => $row['area_name'],
                                        'site_id' => $row['site_id'],
                                        'site_name' => $row['site_name'],
                                        'manufacturer_id' => $row['manufacturer_id'] ?? null,
                                        'manufacturer_name' => $row['manufacturer_name'] ?? null,
                                        'tag_names' => $row['tag_names'] ?? null,
                                        'upc' => $row['item_upc'] ?? null,
                                        'cost' => $row['item_cost'] ?? 0,
                                        'comments' => $row['item_comments'] ?? null,
                                        'serial_number' => $row['item_serial_number'] ?? null,
                                        'is_container' => $row['item_is_container'] ?? null,
                                        ); 
        }

        $stock_item_data['count'] = count($stock_item_data['rows']) ?? 0;
        $stock_item_data['tags'] = $stock_tag_data;

        $total_quantity = 0;
        foreach ($stock_item_data['rows'] as $row) {
            $total_quantity = $total_quantity + (int)$row['quantity'];
        }
        $stock_item_data['total_quantity'] = $total_quantity;
        
        return $stock_item_data;
    }

    static public function getStockItemData($stock_id, $is_cable)
    {
        $stock_item_data = [];
        $stock_item_data['rows'] = [];
        $stock_tag_data = null;

        $instance = new self();
        $instance->setTable('stock');

        if ($is_cable == 0) {
            $rows = $instance->selectRaw("
                                    stock.id AS stock_id, 
                                    stock.name AS stock_name, 
                                    stock.description AS stock_description, 
                                    stock.sku AS stock_sku, 
                                    stock.min_stock AS stock_min_stock, 
                                    area.id AS area_id, 
                                    area.name AS area_name, 
                                    shelf.id AS shelf_id, 
                                    shelf.name AS shelf_name, 
                                    site.id AS site_id, 
                                    site.name AS site_name, 
                                    site.description AS site_description, 
                                    item.serial_number AS item_serial_number, 
                                    item.upc AS item_upc, 
                                    item.cost AS item_cost, 
                                    item.comments AS item_comments, 
                                    item.is_container AS item_is_container,
                                    item.id AS item_id,
                                    item.quantity AS item_quantity, 
                                    manufacturer.id AS manufacturer_id, 
                                    manufacturer.name AS manufacturer_name, 
                                    item_container.container_id as container_id,
                                    container.name as container_name,
                                    container_item_stock.name as container_item_name,
                                    item_container.container_is_item as container_is_item,
                                    (SELECT GROUP_CONCAT(DISTINCT manufacturer.id ORDER BY manufacturer.name SEPARATOR ', ') 
                                        FROM item 
                                        INNER JOIN manufacturer ON manufacturer.id = item.manufacturer_id 
                                        WHERE item.stock_id = stock.id
                                    ) AS manufacturer_ids,
                                    (SELECT GROUP_CONCAT(DISTINCT manufacturer.name ORDER BY manufacturer.name SEPARATOR ', ') 
                                        FROM item 
                                        INNER JOIN manufacturer ON manufacturer.id = item.manufacturer_id 
                                        WHERE item.stock_id = stock.id
                                    ) AS manufacturer_names,
                                    (SELECT GROUP_CONCAT(DISTINCT tag.name ORDER BY tag.name SEPARATOR ', ') 
                                    FROM stock_tag 
                                    INNER JOIN tag ON stock_tag.tag_id = tag.id 
                                    WHERE stock_tag.stock_id = stock.id 
                                    ORDER BY tag.name) AS tag_names, 
                                    (SELECT GROUP_CONCAT(DISTINCT tag.id ORDER BY tag.name SEPARATOR ', ') 
                                    FROM stock_tag 
                                    INNER JOIN tag ON stock_tag.tag_id = tag.id 
                                    WHERE stock_tag.stock_id = stock.id 
                                    ORDER BY tag.name) AS tag_ids
                                ")
                                ->leftJoin('item', 'stock.id', '=', 'item.stock_id')
                                ->leftJoin('shelf', 'item.shelf_id', '=', 'shelf.id')
                                ->leftJoin('area', 'shelf.area_id', '=', 'area.id')
                                ->leftJoin('site', 'area.site_id', '=', 'site.id')
                                ->leftJoin('manufacturer', 'item.manufacturer_id', '=', 'manufacturer.id')
                                ->leftJoin('item_container', 'item_container.item_id', '=', 'item.id')
                                ->leftJoin('container', function ($join) {
                                    $join->on('item_container.container_id', '=', 'container.id')
                                         ->where('item_container.container_is_item', '=', 0);
                                })
                                ->leftJoin('item AS container_item', function ($join) {
                                    $join->on('item_container.container_id', '=', 'container_item.id')
                                         ->where('item_container.container_is_item', '=', 1);
                                })
                                ->leftJoin('stock AS container_item_stock', 'container_item.stock_id', '=', 'container_item_stock.id')
                                ->where('stock.id', $stock_id)
                                ->where('item.quantity', '!=', 0)
                                ->orderBy('site.id')
                                ->orderBy('area.name')
                                ->orderBy('shelf.name')
                                ->get()
                                ->toArray();

        } elseif ($is_cable == 1) {
            $rows = $instance->selectRaw("
                        stock.id AS stock_id, 
                        stock.name AS stock_name, 
                        stock.description AS stock_description, 
                        stock.sku AS stock_sku, 
                        stock.min_stock AS stock_min_stock, 
                        area.id AS area_id, 
                        area.name AS area_name, 
                        shelf.id AS shelf_id, 
                        shelf.name AS shelf_name, 
                        site.id AS site_id, 
                        site.name AS site_name, 
                        site.description AS site_description, 
                        cable_item.cost AS item_cost, 
                        (SELECT SUM(quantity) FROM cable_item 
                            WHERE cable_item.stock_id = stock.id 
                            AND cable_item.shelf_id = shelf.id) AS item_quantity, 
                        (SELECT GROUP_CONCAT(DISTINCT tag.name ORDER BY tag.name SEPARATOR ', ') 
                            FROM stock_tag 
                            INNER JOIN tag ON stock_tag.tag_id = tag.id 
                            WHERE stock_tag.stock_id = stock.id 
                            ORDER BY tag.name) AS tag_names, 
                        (SELECT GROUP_CONCAT(DISTINCT tag.id ORDER BY tag.name SEPARATOR ', ') 
                            FROM stock_tag 
                            INNER JOIN tag ON stock_tag.tag_id = tag.id 
                            WHERE stock_tag.stock_id = stock.id 
                            ORDER BY tag.name) AS tag_ids")
                    ->leftJoin('cable_item', 'stock.id', '=', 'cable_item.stock_id')
                    ->leftJoin('shelf', 'cable_item.shelf_id', '=', 'shelf.id')
                    ->leftJoin('area', 'shelf.area_id', '=', 'area.id')
                    ->leftJoin('site', 'area.site_id', '=', 'site.id')
                    ->where('stock.id', '=', $stock_id)
                    ->where('quantity', '!=', 0)
                    ->groupBy([
                        'stock.id', 'stock_name', 'stock_description', 'stock_sku', 'stock_min_stock', 
                        'site_id', 'site_name', 'site_description', 
                        'area_id', 'area_name', 
                        'shelf_id', 'shelf_name',
                        'item_cost'
                    ])
                    ->orderBy('site.id')
                    ->orderBy('area.name')
                    ->orderBy('shelf.name')
                    ->get()
                    ->toArray();
        }

        foreach ($rows as $row) {
            if ($is_cable == 0) {
                $stock_manufacturer_ids = $row['manufacturer_ids'];
                $stock_manufacturer_names = $row['manufacturer_names'];
                $stock_tag_ids = $row['tag_ids'];
                $stock_tag_names = $row['tag_names'];

                $stock_tag_data = [];
                if ($stock_tag_ids !== null) {
                    for ($n=0; $n < count(explode(", ", $stock_tag_ids)); $n++) {
                        $stock_tag_data[$n] = array('id' => explode(", ", $stock_tag_ids)[$n],
                                                            'name' => explode(", ", $stock_tag_names)[$n]);
                    }
                } else {
                    $stock_tag_data = null;
                }

            }
            
            $stock_item_data['rows'][] = array('id' => $row['stock_id'],
                                        'name' => $row['stock_name'],
                                        'sku' => $row['stock_sku'],
                                        'quantity' => $row['item_quantity'] ?? 0,
                                        'min_stock' => $row['stock_min_stock'],
                                        'shelf_id' => $row['shelf_id'],
                                        'shelf_name' => $row['shelf_name'],
                                        'area_id' => $row['area_id'],
                                        'area_name' => $row['area_name'],
                                        'site_id' => $row['site_id'],
                                        'site_name' => $row['site_name'],
                                        'item_id' => $row['item_id'] ?? null,
                                        'manufacturer_id' => $row['manufacturer_id'] ?? null,
                                        'manufacturer_name' => $row['manufacturer_name'] ?? null,
                                        'tag_names' => $row['tag_names'] ?? null,
                                        'upc' => $row['item_upc'] ?? null,
                                        'cost' => $row['item_cost'] ?? 0,
                                        'comments' => $row['item_comments'] ?? null,
                                        'serial_number' => $row['item_serial_number'] ?? null,
                                        'is_container' => $row['item_is_container'] ?? null,
                                        'container_id' => $row['container_id'] ?? null,
                                        'container_name' => $row['container_name'] ?? null,
                                        'container_item_name' => $row['container_item_name'] ?? null,
                                        'container_is_item' => $row['container_is_item'] ?? null,
                                        ); 
        }

        $stock_item_data['count'] = count($stock_item_data['rows']);
        $stock_item_data['tags'] = $stock_tag_data;

        $total_quantity = 0;
        foreach ($stock_item_data['rows'] as $row) {
            $total_quantity = $total_quantity + (int)$row['quantity'];
        }
        $stock_item_data['total_quantity'] = $total_quantity;
        
        return $stock_item_data;
    }

    static public function getDistinctSerials($stock_id)
    {
        $instance = new self();
        $instance->setTable('item');

        return $instance->selectRaw('serial_number, id')
                        ->where('stock_id', '=', $stock_id)
                        ->where('serial_number', '!=', '')
                        ->where('quantity', '!=', 0)
                        ->where('deleted', '=', 0)
                        ->orderby('id')
                        ->distinct()
                        ->get()
                        ->toarray();
    }

    static public function getFavouriteStockData($stock_id)
    {
        $return = [];
        $return['stock_data'] = [];
        $return['img_data'] = [];
        $return['area_data'] = [];
        $return['tag_data'] = [];

        $stock_instance = new self();
        $stock_instance->setTable('stock');
        $stock_data = $stock_instance->distinct()->selectRaw('stock.id AS id, 
                                            stock.name AS name, 
                                            stock.description AS description, 
                                            stock.sku AS sku, 
                                            stock.min_stock AS min_stock, 
                                            stock.is_cable AS is_cable,
                                            stock.deleted AS deleted')
                                ->where('stock.id', '=', $stock_id)
                                ->get()
                                ->toarray();

        $tag_instance = new self();
        $tag_instance->setTable('tag');
        $tag_data = $tag_instance->distinct()->selectRaw('tag.id AS id,
                                        tag.name AS name,
                                        tag.description AS description,
                                        tag.deleted AS deleted,
                                        stock_tag.id AS stock_tag_id,
                                        stock_tag.stock_id AS stock_id')
                                ->leftJoin('stock_tag', 'stock_tag.tag_id', '=', 'tag.id')
                                ->where('stock_tag.stock_id', '=', $stock_id)
                                ->get()
                                ->toarray();

        $img_instance = new self();
        $img_instance->setTable('stock_img');          
        $img_data = $img_instance->distinct()->selectRaw('stock_img.id AS id, 
                                            stock_img.stock_id AS stock_id, 
                                            stock_img.image AS image')
                                ->where('stock_img.stock_id', '=', $stock_id)
                                ->get()
                                ->toarray();

        $area_instance = new self();
        $area_instance->setTable('area');         
        $area_data = $area_instance->distinct()->selectRaw('area.id AS id,
                                                area.name AS name,
                                                area.site_id AS site_id')
                                ->leftJoin('shelf', 'shelf.area_id', '=', 'area.id')
                                ->leftJoin('item', 'item.shelf_id', '=', 'shelf.id')
                                ->where('item.stock_id', '=', $stock_id)
                                ->get()
                                ->toarray();

        if (count($stock_data) == 1) {
            $data = $stock_data[0];
            $return['stock_data'] = $data;
            
            if (count($tag_data) > 0) {
                $return['tag_data']['rows'] = [];
                foreach($tag_data as $tag) {
                    $return['tag_data']['rows'][] = $tag;
                }
                $return['tag_data']['count'] = count($tag_data);
            }
            if (count($img_data) > 0) {
                $return['img_data']['rows'] = [];
                foreach($img_data as $img) {
                    $return['img_data']['rows'][] = $img;
                }
                $return['img_data']['count'] = count($img_data);
            }
            if (count($area_data) > 0) {
                $return['area_data']['rows'] = [];
                foreach($area_data as $area) {
                    $return['area_data']['rows'][] = $area;
                }
                $return['area_data']['count'] = count($area_data);
            }
        } 
        
        return $return;
    }

    static public function addExistingStock($request, $redirect=null) 
    {
        if ($request['_token'] == csrf_token()) {
            if (GeneralModel::checkShelfAreaMatch($request['shelf'], $request['area']) && GeneralModel::checkAreaSiteMatch($request['area'], $request['site'])) {
                $return = [];
                $return['ids'] = [];
                $counter = 0;
                $redirect_array = [];

                $user = GeneralModel::getUser();

                $serials = array_map('trim', explode(',', $request['serial-number']));

                // check for non=uniques.
                foreach ($serials as $sn) {
                    if ($sn !== null && $sn !== '') {
                        if (StockModel::checkUniqueSerial($sn) == 0) {
                            $redirect_array = ['stock_id'   => $request['id'],
                                            'modify_type' => 'add',
                                            'error' => 'non-unique serial found, aborted.'];
                            return redirect()->route('stock', $redirect_array)->with('return', $return);
                        }
                    }
                }

                for ($i = 0; $i < (int)$request['quantity']; $i++) {
                    // item data
                    if (isset($serials[$i])) {
                        $serial = $serials[$i];
                    } else {
                        $serial = '';
                    }
                    
                    $data = [
                            'stock_id' => $request['id'], 
                            'upc' => $request['upc'],
                            'quantity' => 1,
                            'cost' => $request['cost'] ?? 0,
                            'serial_number' => $serial,
                            'comments' => '',
                            'manufacturer_id' => $request['manufacturer'],
                            'shelf_id' => $request['shelf'],
                            'is_container' => 0
                            ];

                    $insert = ItemModel::create($data);
                    $id = $insert->id;
                    
                    // changelog data for item
                    $info = [
                        'user' => $user,
                        'table' => 'item',
                        'record_id' => $id,
                        'field' => 'quantity',
                        'new_value' => 1,
                        'action' => 'Add quantity',
                        'previous_value' => '',
                    ];

                    $return['insert'][] = ['item_id' => $id, 
                                            'data' => $data, 
                                            'changelog' => $info];
                                            
                    if ($id) {
                        $counter++;
                        GeneralModel::updateChangelog($info);

                        // link to container if needed
                        if (isset($request['container']) && is_numeric($request['container'])) {
                            if ($request['container'] < 0) {
                                $container_id = $request['container'] *-1;
                                $container_is_item = 1;
                            } else {
                                $container_id = $request['container'];
                                $container_is_item = 0;
                            }
                            $container_data = ['item_id' => $id, 
                                                'container_id' => $container_id, 
                                                'is_item' => $container_is_item];

                            // add the container link
                            ContainersModel::linkToContainer($container_data, 'no');
                        }

                        // update the transactions
                        $transaction_data = new HttpRequest([
                            'stock_id' => $request['id'],
                            'item_id' => $id,
                            'type' => 'add',
                            'quantity' => 1,
                            'price' => $request['cost'] ?? 0,
                            'serial_number' => $serial ?? '',
                            'date' => date('Y-m-d'),
                            'time' => date('h:i:s'),
                            'username' => $user['username'],
                            'shelf_id' => $request['shelf'],
                            'reason' => $request['reason']
                        ]);

                        TransactionModel::addTransaction($transaction_data);
                    }
                }
                

                if ($counter == (int)$request['quantity']) {
                    $redirect_array = ['stock_id'   => $request['id'],
                                        'modify_type' => 'add',
                                        'success' => 'added'];
                } else {
                    $redirect_array = ['stock_id'   => $request['id'],
                                            'modify_type' => 'add',
                                            'error' => 'partially_added'];
                }
                
            } else {
                $redirect_array = ['stock_id'   => $request['id'],
                                        'modify_type' => 'add',
                                        'error' => 'missmatch'];
            }

            if ($redirect !== null) {
                return $redirect_array;
            } else {
                return redirect()->route('stock', $redirect_array)->with('return', $return);
            }
        } else {
            return redirect(GeneralModel::previousURL())->with('error', 'CSRF missmatch');
        }
    }

    static public function addNewStock($request, $is_cable)
    {
        $return = [];
        $input = $request->toArray();

        if (GeneralModel::checkShelfAreaMatch($input['shelf'], $input['area']) && GeneralModel::checkAreaSiteMatch($input['area'], $input['site'])) {
            $next_sku = StockModel::getNextSKU();
            
            $data = [
                'name' => $input['name'], 
                'sku' => $input['sku'] ?? $next_sku, // use pre-defined or use the next available
                'description' => $input['description'] ?? '',
                'min_stock' => $input['min-stock'] ?? 0,
                'is_cable' => $is_cable
                ];
            $insert = StockModel::create($data);

            $stock_id = $insert->id;
            $input['id'] = $request['id'] = $stock_id;
            
            // add the tag links
            if (array_key_exists('tags', $input) && is_array($input['tags'])) {
               foreach ($input['tags'] as $tag_id) {
                    TagModel::addTagToStock($tag_id, $stock_id);
                } 
            }
            
            
            // add inventory items
            if ($input['quantity'] > 0) {
                StockModel::addExistingStock($input, 'no');
            }

            // add image
            
            if ($request->hasFile('image')) {
                $uploaded = StockModel::imageUpload($request);
                if ($uploaded == 0) {
                    return redirect()->route('stock', ['stock_id' => 0,
                                                    'modify_type' => 'add',
                                                    'error' => 'Image upload failed.'])
                                                    ->with('return', $return);
                }
            }

            return redirect()->route('stock', ['stock_id' => 0,
                                                    'modify_type' => 'add',
                                                    'success' => 'added'])
                                                    ->with('return', $return);
        } else {
            return redirect()->route('stock', ['stock_id' => 0,
                                                    'modify_type' => 'add',
                                                    'error' => 'missmatch'])
                                                    ->with('return', $return);
        }
    }

    public static function getNextSKU() 
    {
        $sku_prefix = GeneralModel::config()['sku_prefix'] ?? GeneralModel::configDefault()['sku_prefix'];

        $max_number = DB::table('stock')
                        ->select(DB::raw("MAX(CAST(SUBSTRING(sku, LENGTH('$sku_prefix') + 1) AS UNSIGNED)) as max_number"))
                        ->where('sku', 'LIKE', $sku_prefix . '%')
                        ->whereRaw("sku REGEXP '^" . preg_quote($sku_prefix, '/') . "[0-9]{5}$'")
                        ->value('max_number');

        $next_number = $max_number ? $max_number + 1 : 1;

        $next_sku = $sku_prefix . str_pad($next_number, 5, '0', STR_PAD_LEFT);

        return $next_sku;
    }

    public static function checkUniqueSerial($serial)
    {
        $all = GeneralModel::getAllWhere('item', ['serial_number' => $serial]);
        if (count($all) < 1) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function editStock($request)
    {
        // change the stock info in the database
        $return = [];
        $errors = [];

        $input = $request->toArray();

        dd($input);

        $data = [
            'name' => $input['name'], 
            'sku' => $input['sku'],
            'description' => $input['description'] ?? '',
            'min_stock' => $input['min-stock'] ?? 0
        ];

        $user = GeneralModel::getUser();

        $stock_id = $input['id'];

        $current = StockModel::where('id', '=', $stock_id)->get()->toArray()[0];
        // dd($current);
        if ($current['sku'] == $data['sku']) {
            unset($data['sku']);
        } else {
            $matching_sku = StockModel::where('sku', '=', $data['sku'])->get()->toArray();
            if (count($matching_sku) > 0) {
                return redirect()->route('stock', ['stock_id' => $stock_id,
                                                    'modify_type' => 'edit',
                                                    'error' => 'SKU in use'])
                                                    ->with('return', $return);
            }
        }

        $changelog_entries = [];
        foreach (array_keys($data) as $key) {
            if ($current[$key] != $data[$key]) {
                $changelog_entries[] = $key;
            }
        }

        StockModel::where('id', $stock_id)->update($data);

        foreach ($changelog_entries as $entry) {
            $info = [
                'user' => $user,
                'table' => 'stock',
                'record_id' => $stock_id,
                'field' => $entry,
                'new_value' => $data[$entry],
                'action' => 'Update record',
                'previous_value' => $current[$entry],
            ];
            GeneralModel::updateChangelog($info);
        }

        // image uploading
        if ($request->hasFile('image')) {
            $uploaded = StockModel::imageUpload($request);
                if ($uploaded == 0) {
                    return redirect()->route('stock', ['stock_id' => $stock_id,
                                                    'modify_type' => 'edit',
                                                    'error' => 'Image upload failed.'])
                                                    ->with('return', $return);
                }
        }

        $stock_tags = isset($input['tags']) ? $input['tags'] : '';
        $stock_tags_selected = isset($input['tags-selected']) ? $input['tags-selected'] : '';
        $stock_tags_selected = explode(', ', $stock_tags_selected);

        $tags_temp_array = [];
        $tags_selected_temp_array = [];

        if (is_array($stock_tags_selected)) {
            foreach ($stock_tags_selected as $l) {
                array_push($tags_temp_array, $l);
            }
        } else {
            array_push($tags_temp_array, $stock_tags_selected);
        }

        if (is_array($stock_tags)) {
            foreach ($stock_tags as $ll) {
                array_push($tags_temp_array, $ll);
            }
        } else {
            array_push($tags_temp_array, $stock_tags);
        }

        // $tags is the array of selected tags on page
        $tags = array_unique(array_merge($tags_selected_temp_array, $tags_temp_array), SORT_REGULAR);
        
        $current_tags = [];

        $current_tags_array = TagModel::getTagsForStock($stock_id) ?? [];
        if ($current_tags_array['count'] > 0) {
            foreach ($current_tags_array['rows'] as $tag) {
                $current_tags[] = $tag['tag_id'];
            }
        }
        
        $tags_to_stay = array_values(array_intersect($current_tags, $tags));

        $tags_to_remove = array_values(array_diff($current_tags, $tags));
       
        $tags_to_add = array_values(array_diff($tags, $tags_to_stay));
        if ($tags_to_add[0] == "") {
            unset($tags_to_add[0]);
        }

        foreach($tags_to_remove as $tag) {
            if (!in_array($tag, $tags)) {
                if (TagModel::removeTagFromStock($tag, $stock_id) > 0) {

                } else {
                    $errors[] = 'failed to remove tag';
                }
                // changelog happens in the above function
            }
        }
      
        foreach ($tags_to_add as $tag) {
            if (!in_array($tag, $current_tags)) {
                if (TagModel::addTagToStock($tag, $stock_id) > 0) {
                    
                } else {
                    $errors[] = 'failed to add tag';
                }
                // changelog happens in the above function
            }
        }
        
        if (empty($errors)) {
            $redirect_array = ['stock_id'   => $request['id'],
                            'modify_type' => 'edit',
                            'success' => 'Updated Successfully'];
            $return = 1;
        } else {
            $redirect_array = ['stock_id'   => $request['id'],
                            'modify_type' => 'edit',
                            'error' => last($errors)];
            $return = 0;
        }

        return redirect()->route('stock', $redirect_array)->with('return', $return);

    }
    
    static public function moveStock($request, $is_container, $move_children) //move stock quantity 
    {
        $errors = [];
        $moved_count = 0;
        $quantity = $request['quantity'];
        $new_shelf_id = $request['shelf'];
        $user = GeneralModel::getUser();

        if ($request['current_shelf'] == $request['shelf']) {
            $errors[] = $error = 'Cannot move to current shelf.';
                            
            $redirect_array = ['stock_id' => $request['current_stock'],
                                'modify_type' => 'move',
                                'error' => $error];

            return redirect()->route('stock', $redirect_array)->with('error', $error);
        }
        
        $find_where = array_filter([
            'stock_id' => $request['current_stock'],
            'shelf_id' => $request['current_shelf'],
            'manufacturer_id' => $request['current_manufacturer'],
            'upc' => $request['current_upc'],
            'serial_number' => $request['current_serial'],
            'comments' => $request['current_comments'],
            'cost' => $request['current_cost'],
            'is_container' => $is_container,
            'deleted' => 0
        ], function ($value) {
            return !is_null($value);
        });

        $find = DB::table('item')
                ->where($find_where)
                ->get()
                ->toArray(); 

        if (!empty($find)) {
            // check if the count is more than or equal to the move quantity
            $find_count = count($find);

            if ($find_count >= $quantity) {
                // the total find is more or equal to the quantiy to be removed
                $count = 0;
                for ($i = 0; $i < $quantity; $i++) {
                    // move one for the id
                    $move_id = $find[$i]->id;

                    $find_current = DB::table('item')->where('id', $move_id)->where('deleted', 0)->first();

                    if ($find_current) {
                        $update = DB::table('item')->where('id', $move_id)->update(['shelf_id' => $new_shelf_id, 'updated_at' => now()]);

                        if ($update) {
                            // changelog
                            $changelog_info = [
                                'user' => $user,
                                'table' => 'item',
                                'record_id' => $move_id,
                                'action' => 'Update record',
                                'field' => 'shelf_id',
                                'previous_value' => $find_current->shelf_id,
                                'new_value' => $new_shelf_id
                            ];

                            GeneralModel::updateChangelog($changelog_info);

                            // add Transaction
                            $remove_transaction_data = new HttpRequest([
                                'stock_id' => $request['current_stock'],
                                'item_id' => $move_id,
                                'type' => 'move',
                                'quantity' => -1,
                                'price' => 0,
                                'serial_number' => $find_current->serial_number,
                                'date' => date('Y-m-d'),
                                'time' => date('h:i:s'),
                                'username' => $user['username'],
                                'shelf_id' => $find_current->shelf_id,
                                'reason' => 'Move Stock'
                            ]);

                            $transaction_remove = TransactionModel::addTransaction($remove_transaction_data);

                            if (!array_key_exists('error', $transaction_remove)) {
                                $add_transaction_data = new HttpRequest([
                                    'stock_id' => $request['current_stock'],
                                    'item_id' => $move_id,
                                    'type' => 'move',
                                    'quantity' => 1,
                                    'price' => 0,
                                    'serial_number' => $find_current->serial_number,
                                    'date' => date('Y-m-d'),
                                    'time' => date('h:i:s'),
                                    'username' => $user['username'],
                                    'shelf_id' => $new_shelf_id,
                                    'reason' => 'Move Stock'
                                ]);

                                $transaction_add = TransactionModel::addTransaction($add_transaction_data);

                                if (!array_key_exists('error', $transaction_add)) {
                                    if ($move_children == 0) {
                                        // remove any container links 
                                        $unlink_id = DB::table('item_container')
                                                    ->where('item_id', $move_id)
                                                    ->value('id'); // Get the 'id' of the matching row
                                        
                                        if ($unlink_id) {
                                            DB::table('item_container')->where('id', $unlink_id)->delete();

                                            $info = [
                                                'user' => $user,
                                                'table' => 'item_container',
                                                'record_id' => $unlink_id,
                                                'field' => 'item_id',
                                                'new_value' => null,
                                                'action' => 'Delete record',
                                                'previous_value' => $move_id,
                                            ];
                                            GeneralModel::updateChangelog($info);
                                        } else {
                                            //nothing to unlink, continue
                                        }
                                    } else {
                                        // get the children
                                        $children = ContainersModel::getContainerChildrenInfo($move_id, ['item_container.container_is_item' => 1]);
                                        
                                        if ($children) {
                                            foreach($children as $child) {
                                                $child_id = $child['id'];

                                                $update_child = DB::table('item')->where('id', $child_id)->update(['shelf_id' => $new_shelf_id, 'updated_at' => now()]);
                                                if ($update_child) {
                                                    // changelog
                                                    $changelog_info = [
                                                        'user' => $user,
                                                        'table' => 'item',
                                                        'record_id' => $child_id,
                                                        'action' => 'Update record',
                                                        'field' => 'shelf_id',
                                                        'previous_value' => $child['shelf_id'],
                                                        'new_value' => $new_shelf_id
                                                    ];

                                                    GeneralModel::updateChangelog($changelog_info);

                                                    // add Transaction
                                                    $remove_transaction_data = new HttpRequest([
                                                        'stock_id' => $child['stock_id'],
                                                        'item_id' => $child_id,
                                                        'type' => 'move',
                                                        'quantity' => -1,
                                                        'price' => 0,
                                                        'serial_number' => $child['serial_number'],
                                                        'date' => date('Y-m-d'),
                                                        'time' => date('h:i:s'),
                                                        'username' => $user['username'],
                                                        'shelf_id' => $child['shelf_id'],
                                                        'reason' => 'Move Stock'
                                                    ]);

                                                    $transaction_remove = TransactionModel::addTransaction($remove_transaction_data);

                                                    if (!array_key_exists('error', $transaction_remove)) {
                                                        $add_transaction_data = new HttpRequest([
                                                            'stock_id' => $child['stock_id'],
                                                            'item_id' => $child_id,
                                                            'type' => 'move',
                                                            'quantity' => 1,
                                                            'price' => 0,
                                                            'serial_number' => $child['serial_number'],
                                                            'date' => date('Y-m-d'),
                                                            'time' => date('h:i:s'),
                                                            'username' => $user['username'],
                                                            'shelf_id' => $new_shelf_id,
                                                            'reason' => 'Move Stock'
                                                        ]);

                                                        $transaction_add = TransactionModel::addTransaction($add_transaction_data);

                                                        if (!array_key_exists('error', $transaction_add)) {

                                                        } else {
                                                            $errors[] = 'transaction not added for id: '.$move_id.', type = move, quantity = 1.';
                                                        }
                                                    } else {
                                                        $errors[] = 'transaction not added for id: '.$move_id.', type = move, quantity = -1.';
                                                    }
                                                } else {
                                                    $errors[] = $error = 'unable to move id: '.$move_id;
                            
                                                    $redirect_array = ['stock_id' => $request['current_stock'],
                                                                        'modify_type' => 'move',
                                                                        'error' => $error];

                                                    return redirect()->route('stock', $redirect_array)->with('error', $error);
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    $errors[] = 'transaction not added for id: '.$move_id.', type = move, quantity = 1.';
                                }
                            } else {
                                $errors[] = 'transaction not added for id: '.$move_id.', type = move, quantity = -1.';
                            }
                            
                            $moved_count++ ; 
                            
                        } else {
                            $errors[] = $error = 'unable to move id: '.$move_id;
                            
                            $redirect_array = ['stock_id' => $request['current_stock'],
                                                'modify_type' => 'move',
                                                'error' => $error];

                            return redirect()->route('stock', $redirect_array)->with('error', $error);
                        }
                    } else {
                        $errors[] = $error = 'unable to find id: '.$move_id;
                        $redirect_array = ['stock_id' => $request['current_stock'],
                                            'modify_type' => 'move',
                                            'error' => $error];

                        return redirect()->route('stock', $redirect_array)->with('error', $error);
                    }
                    $count++;
                }

            } else {
                // not enough to do the move
                $errors[] = $error = 'Not enough quantity to remove. Current quantity: '.$find_count.', quantity to remove: '.$quantity;
                $redirect_array = ['stock_id' => $request['current_stock'],
                                    'modify_type' => 'move',
                                    'error' => $error];

                return redirect()->route('stock', $redirect_array)->with('error', $error);
            }
            if (empty($errors)) {
                $redirect_array = ['stock_id' => $request['current_stock'],
                                    'modify_type' => 'move',
                                    'success' => 'Successfully moved '.$moved_count.' of requested '.$quantity.'.'];

                return redirect()->route('stock', $redirect_array)->with('success', 'Successfully moved '.$moved_count.' of requested '.$quantity.'.');
            } else {
                $redirect_array = ['stock_id' => $request['current_stock'],
                                    'modify_type' => 'move',
                                    'error' => last($errors)];

                return redirect()->route('stock', $redirect_array)->with('error', last($errors));
            }
            
        } else {
            // row not found, error and return
            $errors[] = $error = 'No results found to move.';
            $redirect_array = ['stock_id' => $request['current_stock'],
                                    'modify_type' => 'move',
                                    'error' => $error];

            return redirect()->route('stock', $redirect_array)->with('error', last($errors));
        }
    }

    static public function moveStockContainer($request, $move_all) // moving stock quantity that is a container
    {
        // get the info
        $find_where = array_filter([
            'stock_id' => $request['stock_id'],
            'shelf_id' => $request['current_shelf'],
            'is_container' => 1,
            'deleted' => 0
        ], function ($value) {
            return !is_null($value);
        });

        $find = DB::table('item')
                ->where($find_where)
                ->whereNotIn('id', function ($subquery) {
                    $subquery->select('item_id')->from('item_container');
                })
                ->get()
                ->toArray(); 

    }

    static public function imageUnlink($request)
    {
        $data = $request->toArray();

        $record = DB::table('stock_img')
            ->where('stock_id', $data['stock_id'])
            ->where('id', $data['img_id'])
            ->first();

        if ($record) {
            DB::table('stock_img')
            ->where('stock_id', $data['stock_id'])
            ->where('id', $data['img_id'])
            ->delete();

            $user = GeneralModel::getUser();
            $info = [
                'user' => $user,
                'table' => 'stock_img',
                'record_id' => $record->id,
                'field' => 'stock_id',
                'new_value' => '',
                'action' => 'Delete record',
                'previous_value' => $data['stock_id'],
            ];
            GeneralModel::updateChangelog($info);
            return $record->id;
        } else {
            return 0;
        }
    }

    static public function imageLink($request)
    {
        $data = $request->toArray();

        $insert = DB::table('stock_img')->insertGetId([
            'stock_id' => $data['stock_id'],
            'image' => $data['img-file-name']
        ]);

        if ($insert) {
            $user = GeneralModel::getUser();
            $info = [
                'user' => $user,
                'table' => 'stock_img',
                'record_id' => $insert,
                'field' => 'stock_id',
                'new_value' => $data['stock_id'],
                'action' => 'New record',
                'previous_value' => '',
            ];
            GeneralModel::updateChangelog($info);
            return $insert;
        } else {
            return 0;
        }
    }

    static public function getMoveStockData($stock_id)// Gives the list shown on the move page - all unique rows with their quantities
    {
        $return = [];

        $results = DB::table('stock')
                    ->join('item', 'item.stock_id', '=', 'stock.id')
                    ->join('manufacturer', 'item.manufacturer_id', '=', 'manufacturer.id')
                    ->join('shelf', 'item.shelf_id', '=', 'shelf.id')
                    ->join('area', 'shelf.area_id', '=', 'area.id')
                    ->join('site', 'area.site_id', '=', 'site.id')
                    ->leftJoin('item_container', 'item.id', '=', 'item_container.item_id')
                    ->where('stock.id', $stock_id)
                    ->where('stock.deleted', 0)
                    ->where('item.quantity', 1)
                    ->groupBy(
                        'stock.id', 'stock.name', 'stock.description', 'stock.is_cable',
                        'item.upc', 'item.cost', 'item.serial_number', 'item.comments', 'item.is_container',
                        'manufacturer.id', 'manufacturer.name',
                        'shelf.id', 'shelf.name', 'area.id', 'area.name', 'site.id', 'site.name',
                        'item_container.id', 'item_container.container_id', 'item_container.container_is_item'
                    )
                    ->orderBy('shelf.id')
                    ->orderBy('item.upc')
                    ->orderBy('manufacturer.id')
                    ->orderBy('item_container.container_id', 'desc')
                    ->orderBy('item.serial_number')
                    ->orderBy('item.comments')
                    ->select([
                        'stock.id as stock_id', 'stock.name as stock_name', 'stock.description as stock_description', 'stock.is_cable as stock_is_cable',
                        'item.upc as upc', 'item.cost as cost', 'item.serial_number as serial_number', 'item.comments as comments', 'item.is_container as is_container',
                        'manufacturer.id as manufacturer_id', 'manufacturer.name as manufacturer_name',
                        'shelf.id as shelf_id', 'shelf.name as shelf_name', 'area.id as area_id', 'area.name as area_name', 'site.id as site_id', 'site.name as site_name',
                        'item_container.id as item_container_id', 'item_container.container_id as container_id', 'item_container.container_is_item as container_is_item',
                        DB::raw('SUM(item.quantity) as quantity')
                    ])
                    ->get()
                    ->map(function ($item) {
                        return (array) $item;
                    })
                    ->toArray();
                    
        foreach($results as $row) {
            if (isset($row['container_id']) && $row['container_id'] !== null) {
                $container_data = StockModel::getMoveContainerData($row['container_id'], $row['container_is_item'])[0];
                $row['container_data'] = $container_data;
            }
            if (isset($row['is_container']) && $row['is_container'] == 1) {
                $params = [
                    'upc' => $row['upc'], 
                    'cost' => $row['cost'], 
                    'serial_number' => $row['serial_number'], 
                    'comments' => $row['comments'],
                    'is_container' => $row['is_container'],
                    'stock_id' => $row['stock_id'],
                    'shelf_id' => $row['shelf_id']
                ];
                $item_data = GeneralModel::getAllWhere('item', $params);
                if (array_key_exists(0, $item_data)) {
                    $row['container_item_data'] = $item_data[0];
                }
            }
            $return[] = $row;
        }

        return $return;
    }

    static public function getMoveStockCableData($stock_id)// Gives the list shown on the move page - all unique rows with their quantities
    {
        $return = [];

        $results = DB::table('stock')
                    ->join('cable_item', 'cable_item.stock_id', '=', 'stock.id')
                    ->join('shelf', 'cable_item.shelf_id', '=', 'shelf.id')
                    ->join('area', 'shelf.area_id', '=', 'area.id')
                    ->join('site', 'area.site_id', '=', 'site.id')
                    ->where('stock.id', $stock_id)
                    ->where('stock.deleted', 0)
                    ->where('cable_item.deleted', 0)
                    ->groupBy(
                        'stock.id', 'stock.name', 'stock.description', 'stock.is_cable',
                        'cable_item.cost', 'cable_item.type_id',
                        'shelf.id', 'shelf.name', 'area.id', 'area.name', 'site.id', 'site.name'
                    )
                    ->orderBy('shelf.id')
                    ->select([
                        'stock.id as stock_id', 'stock.name as stock_name', 'stock.description as stock_description', 'stock.is_cable as stock_is_cable',
                        'cable_item.cost as cost',
                        'shelf.id as shelf_id', 'shelf.name as shelf_name', 'area.id as area_id', 'area.name as area_name', 'site.id as site_id', 'site.name as site_name',
                        DB::raw('SUM(cable_item.quantity) as quantity')
                    ])
                    ->get()
                    ->map(function ($item) {
                        return (array) $item;
                    })
                    ->toArray();
                    
        foreach($results as $row) {
            $return[] = $row;
        }

        return $return;
    }

    static public function getMoveContainerData($container_id, $is_item=0) 
    {
        $instance = new self();
        $instance->setTable('item_container AS ic');

        if ($is_item == 1) {
            $data = $instance->selectRaw('
                                s.name AS container_name, 
                                s.id AS stock_id, 
                                i.id AS container_id,
                                ic.id AS ic_id
                            ')
                            ->join('item as i', function ($join) {
                                $join->on('i.id', '=', 'ic.container_id')
                                    ->where('ic.container_is_item', '=', 1);
                            })
                            ->join('stock as s', 's.id', '=', 'i.stock_id')
                            ->where('ic.container_id', '=', $container_id)
                            ->where('ic.container_is_item', '=', $is_item)
                            ->get()
                            ->toArray();
        } else {
            $data = $instance->selectRaw('
                                c.name AS container_name, 
                                c.id AS container_id, 
                                ic.id AS ic_id
                            ')
                            ->join('container as c', 'c.id', '=', 'ic.container_id')
                            ->where('ic.container_id', '=', $container_id)
                            ->where('ic.container_is_item', '=', $is_item)
                            ->get()
                            ->toArray();
        }
        
        return $data;
    }


    static public function imageUpload($request)
    {
        // $request->validate([
        //     'file' => 'required|image|mimes:jpeg,png,jpg,gif,ico|max:10000',
        //     'stock_id' => 'required|integer',
        // ]);
    
        $file = $request->file('image');
        $stock_id = $request->id;
        $timestamp = now()->format('YmdHis');
    
        // Create a unique filename
        $filename = "stock-{$stock_id}-img-{$timestamp}-" . uniqid() . "." . $file->getClientOriginalExtension();

        // Move to public/img/stock
        $destinationPath = public_path('img/stock');
        $moved = $file->move($destinationPath, $filename);
        
        if ($moved) {
            // Save to DB
            $new_stock_img_id = DB::table('stock_img')->insertGetId([
                'stock_id' => $stock_id,
                'image' => $filename,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            if (isset($new_stock_img_id)) {
                // update changelog
                $user = GeneralModel::getUser();
                $info = [
                    'user' => $user,
                    'table' => 'stock_img',
                    'record_id' => $new_stock_img_id,
                    'field' => 'image',
                    'new_value' => $filename,
                    'action' => 'New record',
                    'previous_value' => '',
                ];
                GeneralModel::updateChangelog($info);
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
        
    }

    static public function restoreStock($request)
    {
        $attribute = $request['stockmanagement-type'];
        $id = $request['id'];
        $anchor = 'stockmanagement-settings';

        if ($attribute !== 'deleted') {
            return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Incorrect type.');
        }

        // check permissions
        $user = GeneralModel::getUser();

        if ($user['permissions']['root'] !== 1 && $user['permissions']['admin'] !== 1) {
            return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Permission denied.');
        }

        // get current data
        $current_data = DB::table('stock')
                            ->where('id', $id)
                            ->where('deleted', 1)
                            ->first();

        if ($current_data) {
            //update
            $update = DB::table('stock')->where('id', $id)->update(['deleted' => 0, 'updated_at' => now()]);

            if ($update) {
                // changelog
                $changelog_info = [
                    'user' => GeneralModel::getUser(),
                    'table' => 'stock',
                    'record_id' => $id,
                    'action' => 'Restore record',
                    'field' => 'deleted',
                    'previous_value' => $current_data->deleted,
                    'new_value' => 0
                ];

                GeneralModel::updateChangelog($changelog_info);
                // add Transaction
                $transaction_data = new HttpRequest([
                    'stock_id' => $id,
                    'item_id' => 0,
                    'type' => 'restore',
                    'quantity' => 0,
                    'price' => 0,
                    'serial_number' => '',
                    'date' => date('Y-m-d'),
                    'time' => date('h:i:s'),
                    'username' => $user['username'],
                    'shelf_id' => NULL,
                    'reason' => 'Stock restored by an admin.'
                ]);

                TransactionModel::addTransaction($transaction_data);
                return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('success', 'Stock restored: '.$current_data->name);
            } else {
                return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'No changes made. Unable restore attribute');
            }
            
        } else {
            return redirect()->to(route('admin', ['section' => $anchor]) . '#'.$anchor)->with('error', 'Unable to confirm stock data.');
        }
    }
}
