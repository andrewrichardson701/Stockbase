<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetsModel extends Model
{
    //

    static public function getAssets() 
    {
        $disks = $optics = $cpus = $memory = [];

        $disks = GeneralModel::allDistinct('disk_item', 0);
        $optics = GeneralModel::allDistinct('optic_item', 0);
        $cpus = GeneralModel::allDistinct('cpu_item', 0);
        $memory = GeneralModel::allDistinct('memory_item', 0);

        $assets = [
            'disks' => GeneralModel::formatArrayOnIdAndCount($disks), 
            'optics' => GeneralModel::formatArrayOnIdAndCount($optics), 
            'cpus' => GeneralModel::formatArrayOnIdAndCount($cpus), 
            'memory' => GeneralModel::formatArrayOnIdAndCount($memory), 
        ];

        $temp = [];

        foreach ($disks as $key => $row) {
           $temp['d-'.$row['id']] = $row;
        }
        
         foreach ($optics as $key => $row) {
           $temp['o-'.$row['id']] = $row;
        }

         foreach ($cpus as $key => $row) {
           $temp['c-'.$row['id']] = $row;
        }

         foreach ($memory as $key => $row) {
           $temp['m-'.$row['id']] = $row;
        }
        
        $assets['all'] = GeneralModel::formatArrayOnIdAndCount($temp);

        return $assets;
    }
}
