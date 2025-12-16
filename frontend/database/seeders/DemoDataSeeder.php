<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // UPKP
        DB::table('upkp')->updateOrInsert(
            ['id' => 'c445f81e-974a-4860-b706-052996f32f69'],
            [
                'user_id'       => 'da027061-65ca-4627-bafd-38ad387035b2',
                'nama_upkp'     => 'UPKP Test',
                'nama_operator' => 'Operator Test',
                'no_hp_operator'=> '081234567890',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]
        );

        // KSM
        DB::table('ksm')->updateOrInsert(
            ['id' => '03063452-ae44-4c94-9a65-4695273fbee0'],
            [
                'user_id'    => '49942b7a-bab0-4a9e-b9f2-e05a001a8c9f',
                'id_upkp'    => 'c445f81e-974a-4860-b706-052996f32f69',
                'nama_ksm'   => 'KSM Contoh',
                'no_hp'      => '08123456789',
                'alamat'     => 'Jl. Contoh No.1',
                'kelurahan'  => 'Kelurahan A',
                'kecamatan'  => 'Kecamatan B',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Pengurus KSM
        DB::table('pengurus_ksm')->updateOrInsert(
            ['id_ksm' => '03063452-ae44-4c94-9a65-4695273fbee0'],
            [
                'id'                                 => Str::uuid(),
                'ketua_ksm'                          => 'Budi',
                'sekretaris_ksm'                     => 'Sari',
                'seksi_iuran_pengguna_ksm'           => 'Andi',
                'seksi_pengoperasian_dan_pemliharaan_ksm' => 'Rina',
                'seksi_penyuluhan_kesehatan_ksm'     => 'Dewi',
                'laki_laki'                          => '5',
                'perempuan'                          => '3',
                'bendahara_ksm'                      => 'Tono',
                'created_at'                         => now(),
                'updated_at'                         => now(),
            ]
        );

        // Profiles UPKP
        DB::table('profiles')->updateOrInsert(
            ['user_id' => 'da027061-65ca-4627-bafd-38ad387035b2'],
            [
                'id'         => Str::uuid(),
                'role'       => 'upkp',
                'id_upkp'    => 'c445f81e-974a-4860-b706-052996f32f69',
                'id_ksm'     => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Profiles KSM
        DB::table('profiles')->updateOrInsert(
            ['user_id' => '49942b7a-bab0-4a9e-b9f2-e05a001a8c9f'],
            [
                'id'         => Str::uuid(),
                'role'       => 'ksm',
                'id_ksm'     => '03063452-ae44-4c94-9a65-4695273fbee0',
                'id_upkp'    => 'c445f81e-974a-4860-b706-052996f32f69',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}