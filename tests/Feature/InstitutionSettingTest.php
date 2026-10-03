<?php

namespace Tests\Feature;

use App\Models\InstitutionSetting;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class InstitutionSettingTest extends TestCase
{
    public function test_signature_uses_saved_chair_and_falls_back_to_config(): void
    {
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        Schema::create('institution_settings', function (Blueprint $table) {
            $table->id();
            $table->string('ketua_nama');
            $table->string('ketua_nip');
            $table->timestamps();
        });

        $this->assertSame(config('institution.ketua_nama'), InstitutionSetting::details()['ketua_nama']);

        InstitutionSetting::create(['ketua_nama' => 'Ketua Baru', 'ketua_nip' => '123456789012345678']);
        $this->assertSame('Ketua Baru', InstitutionSetting::details()['ketua_nama']);
        $this->assertSame('123456789012345678', InstitutionSetting::details()['ketua_nip']);
    }
}
