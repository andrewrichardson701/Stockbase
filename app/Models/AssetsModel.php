<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetsModel extends Model
{
    //

    static public function getAssets() 
    {
        $disks = $optics = $cpus = $memory = $fans = $psus = [];

        $disks = GeneralModel::allDistinct('disk_item', 0);
        $optics = GeneralModel::allDistinct('optic_item', 0);
        // $cpus = GeneralModel::allDistinct('cpu_item', 0);
        // $memory = GeneralModel::allDistinct('memory_item', 0);
        // $fans = GeneralModel::allDistinct('fan_item', 0);
        // $psus = GeneralModel::allDistinct('psu_item', 0);

        $assets = [$disks, $optics, $cpus, $memory, $fans, $psus];

        foreach ($disks as $key => $row) {
           $assets['d-'.$row['id']] = $row;
        }
        
         foreach ($optics as $key => $row) {
           $assets['o-'.$row['id']] = $row;
        }

         foreach ($cpus as $key => $row) {
           $assets['c-'.$row['id']] = $row;
        }

         foreach ($memory as $key => $row) {
           $assets['m-'.$row['id']] = $row;
        }

         foreach ($fans as $key => $row) {
           $assets['f-'.$row['id']] = $row;
        }

         foreach ($psus as $key => $row) {
           $assets['p-'.$row['id']] = $row;
        }
        
        return $assets;
    }
}
