<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dosen;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DosenSeeder extends Seeder
{
    public function run(): void
    {
        $dosenData = [
            [
                'nama' => 'Andasuryani, Dr., S.TP., M.Si.',
                'nip' => '198001012010122001',
                'email' => 'andasuryani@eng.unand.ac.id'
            ],
            [
                'nama' => 'Aninda Tifani Puari, S.Si, M.Sc',
                'nip' => '198501012010122001',
                'email' => 'aninda.tifani@eng.unand.ac.id'
            ],
            [
                'nama' => 'Aries Kusumawati, SP, M.Si',
                'nip' => '198601012010122001',
                'email' => 'aries.kusumawati@eng.unand.ac.id'
            ],
            [
                'nama' => 'Armansyah, Dr., SP, MP',
                'nip' => '198701012010122001',
                'email' => 'armansyah@eng.unand.ac.id'
            ],
            [
                'nama' => 'Ashadi Hasan, S.TP, M.Tech',
                'nip' => '198801012010122001',
                'email' => 'ashadi.hasan@eng.unand.ac.id'
            ],
            [
                'nama' => 'Ayendra Asmuti, Ir., M.Si',
                'nip' => '198901012010122001',
                'email' => 'ayendra.asmuti@eng.unand.ac.id'
            ],
            [
                'nama' => 'Azrifirwan, Dr., S.TP, M.Eng',
                'nip' => '199001012010122001',
                'email' => 'azrifirwan@eng.unand.ac.id'
            ],
            [
                'nama' => 'Dedi Mardiansyah, Dr.',
                'nip' => '199101012010122001',
                'email' => 'dedi.mardiansyah@eng.unand.ac.id'
            ],
            [
                'nama' => 'Delvi Yanti, Dr., S.TP, MP',
                'nip' => '199201012010122001',
                'email' => 'delvi.yanti@eng.unand.ac.id'
            ],
            [
                'nama' => 'Dinah Cherie, Dr., S.TP, M.Si',
                'nip' => '199301012010122001',
                'email' => 'dinah.cherie@eng.unand.ac.id'
            ],
            [
                'nama' => 'Dosen MKWU Unand',
                'nip' => '199401012010122001',
                'email' => 'dosen.mkwu@eng.unand.ac.id'
            ],
            [
                'nama' => 'Dwi Puryanti, Dr.',
                'nip' => '199501012010122001',
                'email' => 'dwi.puryanti@eng.unand.ac.id'
            ],
            [
                'nama' => 'Elvaswer, Dr., SSi',
                'nip' => '199601012010122001',
                'email' => 'elvaswer@eng.unand.ac.id'
            ],
            [
                'nama' => 'Eri Stiyanto, S.TP, M.Si',
                'nip' => '199701012010122001',
                'email' => 'eri.stiyanto@eng.unand.ac.id'
            ],
            [
                'nama' => 'Fadli Hafizulhaq, Dr., ST',
                'nip' => '199801012010122001',
                'email' => 'fadli.hafizulhaq@eng.unand.ac.id'
            ],
            [
                'nama' => 'Fadli Irsyad, S.TP, M.Si, Ph.D',
                'nip' => '199901012010122001',
                'email' => 'fadli.irsyad@eng.unand.ac.id'
            ],
            [
                'nama' => 'Ifmalinda, Dr., S.TP, MP',
                'nip' => '200001012010122001',
                'email' => 'ifmalinda@eng.unand.ac.id'
            ],
            [
                'nama' => 'Ilona Bella, Dr.',
                'nip' => '200101012010122001',
                'email' => 'ilona.bella@eng.unand.ac.id'
            ],
            [
                'nama' => 'Khairil Agustoria, S.TP, MT',
                'nip' => '200201012010122001',
                'email' => 'khairil.agustoria@eng.unand.ac.id'
            ],
            [
                'nama' => 'Khandra Fahmy, S.TP, MP, Ph.D',
                'nip' => '200301012010122001',
                'email' => 'khandra.fahmy@eng.unand.ac.id'
            ],
            [
                'nama' => 'Mislaini R., Dr., S.TP, MP',
                'nip' => '200401012010122001',
                'email' => 'mislaini.r@eng.unand.ac.id'
            ],
            [
                'nama' => 'Moh. Agita Tjandra, M.Sc, Ph.D',
                'nip' => '200501012010122001',
                'email' => 'moh.agita@eng.unand.ac.id'
            ],
            [
                'nama' => 'Mora, Drs., M.Si',
                'nip' => '200601012010122001',
                'email' => 'mora@eng.unand.ac.id'
            ],
            [
                'nama' => 'Muhammad Iqbal Abdi Lubis, S.TP, MP',
                'nip' => '200701012010122001',
                'email' => 'muhammad.iqbal@eng.unand.ac.id'
            ],
            [
                'nama' => 'Muhammad Makky, Dr.Eng., S.TP, M.Si',
                'nip' => '200801012010122001',
                'email' => 'muhammad.makky@eng.unand.ac.id'
            ],
            [
                'nama' => 'Nalwida Rozen, Dr.Ir., MP',
                'nip' => '200901012010122001',
                'email' => 'nalwida.rozen@eng.unand.ac.id'
            ],
            [
                'nama' => 'Nika Rahma Yanti, S.TP, MP',
                'nip' => '201001012010122001',
                'email' => 'nika.rahma@eng.unand.ac.id'
            ],
            [
                'nama' => 'Nurmala Sari, S.TP, M.Si',
                'nip' => '201101012010122001',
                'email' => 'nurmala.sari@eng.unand.ac.id'
            ],
            [
                'nama' => 'Nurwanita Ekasari Putri, Dr., SP, M.Si',
                'nip' => '201201012010122001',
                'email' => 'nurwanita.ekasari@eng.unand.ac.id'
            ],
            [
                'nama' => 'Omil Charmyn Chatib, Dr., S.TP. M.Si.',
                'nip' => '201301012010122001',
                'email' => 'omil.charmyn@eng.unand.ac.id'
            ],
            [
                'nama' => 'P.K. Dewi Hayati, Dr., SP. M.Si',
                'nip' => '201401012010122001',
                'email' => 'pk.dewi.hayati@eng.unand.ac.id'
            ],
            [
                'nama' => 'Putri Wulandari Zainal, S.TP, M.Si, Ph.D',
                'nip' => '201501012010122001',
                'email' => 'putri.wulandari@eng.unand.ac.id'
            ],
            [
                'nama' => 'Rahmi Awalina, S.TP, MP',
                'nip' => '201601012010122001',
                'email' => 'rahmi.awalina@eng.unand.ac.id'
            ],
            [
                'nama' => 'Renny Eka Putri, Dr., S.TP, MP',
                'nip' => '201701012010122001',
                'email' => 'renny.eka@eng.unand.ac.id'
            ],
            [
                'nama' => 'Rusnam, Prof. Dr. Ir., MS',
                'nip' => '201801012010122001',
                'email' => 'rusnam@eng.unand.ac.id'
            ],
            [
                'nama' => 'Saddam Pebrianto, S.TP, MT',
                'nip' => '201901012010122001',
                'email' => 'saddam.pebrianto@eng.unand.ac.id'
            ],
            [
                'nama' => 'Tio Putra Wendari, Dr., S.Si',
                'nip' => '202001012010122001',
                'email' => 'tio.putra@eng.unand.ac.id'
            ],
            [
                'nama' => 'Upita Septiani, Dr., MSi',
                'nip' => '202101012010122001',
                'email' => 'upita.septiani@eng.unand.ac.id'
            ],
            [
                'nama' => 'Yusniwati, Prof. Dr., SP, MP',
                'nip' => '202201012010122001',
                'email' => 'yusniwati@eng.unand.ac.id'
            ]
        ];

        foreach ($dosenData as $dosen) {
            $user = User::create([
                'name' => $dosen['nama'],
                'email' => $dosen['email'],
                'password' => Hash::make('password'),
                'role' => 'dosen',
                'isAktif' => true,
                'email_verified_at' => now(),
            ]);

            Dosen::create([
                'userId' => $user->id,
                'nama' => $dosen['nama'],
                'nip' => $dosen['nip'],
            ]);
        }
    }
}
