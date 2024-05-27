<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BusStationsSeeder extends Seeder
{
    public function run()
    {
        $terminalNames = [
            'Terminal Jati Manis',
            'Terminal Merdeka',
            'Terminal Kampung Baru',
            'Terminal Kampung Jati',
            'Terminal Serpong',
            'Terminal Tangerang',
            'Terminal Tangerang Selatan',
            'Terminal BSD',
            'Terminal Cengkareng',
            'Terminal Ciater',
            'Terminal Jakarta',
            'Terminal Jatinegara',
            'Terminal Kemayoran',
            'Terminal Kemang',
            'Terminal Ciputat',
            'Terminal Kota',
            'Terminal Subang Jaya',
            'Terminal Aceh',
            'Terminal Bandung',
            'Terminal Cibinong',
            'Terminal Depok',
            'Terminal Embakasi',
            'Terminal Garut',
            'Terminal Halim Perdana Kusuma',
            'Terminal Jakarta Barat',
            'Terminal Kebon Jeruk',
            'Terminal Kalibata',
            'Terminal Pasar Minggu',
            'Terminal Pancoran',
            'Terminal Cikarang',
            'Terminal Karawang',
            'Terminal Cikampek',
            'Terminal Purwakarta',
            'Terminal Subang',
            'Terminal Majalengka',
            'Terminal Cirebon',
            'Terminal Indramayu',
            'Terminal Kuningan',
            'Terminal Sumedang',
            'Terminal Tasikmalaya',
            'Terminal Garut',
            'Terminal Banjar',
            'Terminal Pangandaran',
            'Terminal Ciamis',
            'Terminal Majalengka',
            'Terminal Banjar',
            'Terminal Cilacap',
            'Terminal Banyumas',
            'Terminal Purwokerto',
            'Terminal Purbalingga',
            'Terminal Wonosobo',
            'Terminal Kebumen',
            'Terminal Purworejo',
            'Terminal Magelang',
            'Terminal Boyolali',
            'Terminal Solo',
            'Terminal Karanganyar',
            'Terminal Sukoharjo',
            'Terminal Sragen',
            'Terminal Ngawi',
            'Terminal Madiun',
            'Terminal Ponorogo',
            'Terminal Pacitan',
            'Terminal Trenggalek',
            'Terminal Tulungagung',
            'Terminal Blitar',
            'Terminal Kediri',
            'Terminal Nganjuk',
            'Terminal Jombang',
            'Terminal Mojokerto',
            'Terminal Surabaya',
            'Terminal Gresik',
            'Terminal Lamongan',
            'Terminal Tuban',
            'Terminal Bojonegoro',
            'Terminal Ngawi',
            'Terminal Magetan',
            'Terminal Madiun',
            'Terminal Ponorogo',
            'Terminal Pacitan',
            'Terminal Trenggalek',
            'Terminal Tulungagung',
            'Terminal Blitar',
            'Terminal Kediri',
            'Terminal Nganjuk',
            'Terminal Jombang',
            'Terminal Mojokerto',
            'Terminal Surabaya',
            'Terminal Gresik',
            'Terminal Lamongan',
            'Terminal Tuban',
            'Terminal Bojonegoro',
            'Terminal Ngawi',
            'Terminal Magetan',
            'Terminal Madiun',
            'Terminal Ponorogo',
            'Terminal Pacitan',
            'Terminal Trenggale',
        ];

        $now = Carbon::now();

        // Insert bus stations
        foreach ($terminalNames as $terminalName) {
            DB::table('bus_stations')->insert([
                'name' => $terminalName,
                'address' => fake('id_ID')->address(),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Get all bus stations
        $busStations = DB::table('bus_stations')->get();

        // Slice terminal names into chunks for each user ID
        $userIdValues = [
            2 => $busStations->slice(0, 20),
            3 => $busStations->slice(20, 20),
            4 => $busStations->slice(40, 20),
        ];

        // Loop through and assign terminals to each user ID
        foreach ($userIdValues as $userId => $busStationsChunk) {
            foreach ($busStationsChunk as $busStation) {
                DB::table('user_bus_station')->insert([
                    'user_id' => $userId,
                    'bus_station_id' => $busStation->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
