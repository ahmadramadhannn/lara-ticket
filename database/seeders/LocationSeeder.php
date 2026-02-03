<?php

namespace Database\Seeders;

use App\Models\Province;
use App\Models\City;
use App\Models\Terminal;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create provinces
        $provinces = [
            ['name' => 'DKI Jakarta', 'code' => 'JKT'],
            ['name' => 'Jawa Barat', 'code' => 'JBR'],
            ['name' => 'Jawa Tengah', 'code' => 'JTG'],
            ['name' => 'Jawa Timur', 'code' => 'JTM'],
            ['name' => 'Sumatera Utara', 'code' => 'SMU'],
            ['name' => 'Sumatera Barat', 'code' => 'SMB'],
            ['name' => 'Bali', 'code' => 'BAL'],
            ['name' => 'Yogyakarta', 'code' => 'YGY'],
        ];

        foreach ($provinces as $provinceData) {
            Province::create($provinceData);
        }

        // Jakarta
        $jakarta = Province::where('code', 'JKT')->first();
        $jakartaCities = [
            ['name' => 'Jakarta Barat', 'type' => 'city'],
            ['name' => 'Jakarta Selatan', 'type' => 'city'],
            ['name' => 'Jakarta Timur', 'type' => 'city'],
            ['name' => 'Jakarta Pusat', 'type' => 'city'],
        ];

        foreach ($jakartaCities as $cityData) {
            $city = $jakarta->cities()->create($cityData);

            // Terminals based on city
            match ($city->name) {
                'Jakarta Barat' => $city->terminals()->createMany([
                    ['name' => 'Terminal Kalideres', 'code' => 'JKT-KLD', 'address' => 'Jl. Daan Mogot KM 17, Kalideres'],
                ]),
                'Jakarta Selatan' => $city->terminals()->createMany([
                    ['name' => 'Terminal Lebak Bulus', 'code' => 'JKT-LBB', 'address' => 'Jl. Lebak Bulus Raya'],
                    ['name' => 'Terminal Kampung Rambutan', 'code' => 'JKT-KPR', 'address' => 'Jl. Raya Bogor KM 35'],
                ]),
                'Jakarta Timur' => $city->terminals()->createMany([
                    ['name' => 'Terminal Pulo Gebang', 'code' => 'JKT-PLG', 'address' => 'Jl. Pulo Gebang Raya'],
                ]),
                default => null,
            };
        }

        // Jawa Barat
        $jabar = Province::where('code', 'JBR')->first();
        $jabarCities = [
            ['name' => 'Bandung', 'type' => 'city'],
            ['name' => 'Bekasi', 'type' => 'city'],
            ['name' => 'Bogor', 'type' => 'city'],
            ['name' => 'Tasikmalaya', 'type' => 'city'],
        ];

        foreach ($jabarCities as $cityData) {
            $city = $jabar->cities()->create($cityData);

            match ($city->name) {
                'Bandung' => $city->terminals()->createMany([
                    ['name' => 'Terminal Cicaheum', 'code' => 'BDG-CCH', 'address' => 'Jl. Jendral A. Yani, Cicaheum'],
                    ['name' => 'Terminal Leuwi Panjang', 'code' => 'BDG-LWP', 'address' => 'Jl. Soekarno Hatta, Leuwi Panjang'],
                ]),
                'Bekasi' => $city->terminals()->createMany([
                    ['name' => 'Terminal Bekasi', 'code' => 'BKS-TMN', 'address' => 'Jl. Ir. H. Juanda, Bekasi'],
                ]),
                'Bogor' => $city->terminals()->createMany([
                    ['name' => 'Terminal Baranangsiang', 'code' => 'BGR-BRN', 'address' => 'Jl. Raya Pajajaran, Bogor'],
                ]),
                'Tasikmalaya' => $city->terminals()->createMany([
                    ['name' => 'Terminal Indihiang', 'code' => 'TSM-IDH', 'address' => 'Jl. A.H. Nasution, Indihiang'],
                ]),
                default => null,
            };
        }

        // Jawa Tengah
        $jateng = Province::where('code', 'JTG')->first();
        $jatengCities = [
            ['name' => 'Semarang', 'type' => 'city'],
            ['name' => 'Solo', 'type' => 'city'],
            ['name' => 'Purwokerto', 'type' => 'regency'],
            ['name' => 'Cilacap', 'type' => 'regency'],
        ];

        foreach ($jatengCities as $cityData) {
            $city = $jateng->cities()->create($cityData);

            match ($city->name) {
                'Semarang' => $city->terminals()->createMany([
                    ['name' => 'Terminal Terboyo', 'code' => 'SMG-TRB', 'address' => 'Jl. Kaligawe, Terboyo'],
                    ['name' => 'Terminal Mangkang', 'code' => 'SMG-MKG', 'address' => 'Jl. Raya Semarang-Kendal'],
                ]),
                'Solo' => $city->terminals()->createMany([
                    ['name' => 'Terminal Tirtonadi', 'code' => 'SLO-TRT', 'address' => 'Jl. A. Yani, Tirtonadi'],
                ]),
                'Purwokerto' => $city->terminals()->createMany([
                    ['name' => 'Terminal Bulupitu', 'code' => 'PWK-BLP', 'address' => 'Jl. Gerilya, Purwokerto'],
                ]),
                'Cilacap' => $city->terminals()->createMany([
                    ['name' => 'Terminal Maos', 'code' => 'CLP-MOS', 'address' => 'Jl. Diponegoro, Maos'],
                ]),
                default => null,
            };
        }

        // Jawa Timur
        $jatim = Province::where('code', 'JTM')->first();
        $jatimCities = [
            ['name' => 'Surabaya', 'type' => 'city'],
            ['name' => 'Malang', 'type' => 'city'],
            ['name' => 'Kediri', 'type' => 'city'],
        ];

        foreach ($jatimCities as $cityData) {
            $city = $jatim->cities()->create($cityData);

            match ($city->name) {
                'Surabaya' => $city->terminals()->createMany([
                    ['name' => 'Terminal Purabaya (Bungurasih)', 'code' => 'SBY-PRB', 'address' => 'Jl. Bungurasih, Waru'],
                ]),
                'Malang' => $city->terminals()->createMany([
                    ['name' => 'Terminal Arjosari', 'code' => 'MLG-ARJ', 'address' => 'Jl. Simpang Panji Suroso, Arjosari'],
                ]),
                'Kediri' => $city->terminals()->createMany([
                    ['name' => 'Terminal Tamanan', 'code' => 'KDR-TMN', 'address' => 'Jl. Raya Tamanan, Kediri'],
                ]),
                default => null,
            };
        }

        // Yogyakarta
        $yogya = Province::where('code', 'YGY')->first();
        $yogyaCities = [
            ['name' => 'Yogyakarta', 'type' => 'city'],
        ];

        foreach ($yogyaCities as $cityData) {
            $city = $yogya->cities()->create($cityData);

            $city->terminals()->createMany([
                ['name' => 'Terminal Giwangan', 'code' => 'YGY-GWN', 'address' => 'Jl. Imogiri Timur, Giwangan'],
            ]);
        }

        // Bali
        $bali = Province::where('code', 'BAL')->first();
        $baliCities = [
            ['name' => 'Denpasar', 'type' => 'city'],
        ];

        foreach ($baliCities as $cityData) {
            $city = $bali->cities()->create($cityData);

            $city->terminals()->createMany([
                ['name' => 'Terminal Mengwi', 'code' => 'DPS-MWI', 'address' => 'Jl. Raya Denpasar-Gilimanuk'],
                ['name' => 'Terminal Ubung', 'code' => 'DPS-UBG', 'address' => 'Jl. Cokroaminoto, Ubung'],
            ]);
        }
    }
}
