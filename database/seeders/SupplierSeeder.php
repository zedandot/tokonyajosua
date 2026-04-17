<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Seed data supplier contoh untuk SIMTOKO.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name'            => 'PT. Maju Bersama Elektronik',
                'code'            => 'SUP-001',
                'contact_person'  => 'Budi Santoso',
                'phone'           => '021-55512345',
                'email'           => 'budi@majubersama.co.id',
                'address'         => 'Jl. Industri Raya No. 45, Jakarta Barat',
                'is_active'       => true,
            ],
            [
                'name'            => 'CV. Sumber Makmur',
                'code'            => 'SUP-002',
                'contact_person'  => 'Ani Wijayanti',
                'phone'           => '0274-987654',
                'email'           => 'ani@sumbermakmur.com',
                'address'         => 'Jl. Pasar Kembang No. 12, Yogyakarta',
                'is_active'       => true,
            ],
            [
                'name'            => 'Distributor Nusantara Jaya',
                'code'            => 'SUP-003',
                'contact_person'  => 'Rudi Hermawan',
                'phone'           => '031-77889900',
                'email'           => 'rudi@nusantarajaya.id',
                'address'         => 'Jl. Rungkut Industri No. 8, Surabaya',
                'is_active'       => true,
            ],
        ];

        foreach ($suppliers as $data) {
            Supplier::updateOrCreate(['code' => $data['code']], $data);
        }
    }
}
