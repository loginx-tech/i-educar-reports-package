<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Setting::query()->updateOrCreate([
            'key' => 'legacy.report.carteira_estudante_frente',
        ], [
            'type' => 'string',
            'description' => 'Carteira do estudante - Imagem da frente (Modelo 4)',
            'setting_category_id' => 9,
            'value' => '',
        ]);

        Setting::query()->updateOrCreate([
            'key' => 'legacy.report.carteira_estudante_verso',
        ], [
            'type' => 'string',
            'description' => 'Carteira do estudante - Imagem do verso (Modelo 4)',
            'setting_category_id' => 9,
            'value' => '',
        ]);
    }

    public function down(): void
    {
        Setting::query()->where('key', 'legacy.report.carteira_estudante_frente')->delete();
        Setting::query()->where('key', 'legacy.report.carteira_estudante_verso')->delete();
    }
};
