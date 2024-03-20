<?php

use App\Menu;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Menu::query()->create([
            'parent_id' => Menu::query()->where('old', 999923)->firstOrFail()->getKey(),
            'title' => 'Relatório de profissionais de apoio',
            'description' => null,
            'link' => '/module/Reports/SupportProfissional',
            'order' => 0,
            'old' => 9998930,
            'process' => 9998930,
        ]);
    }

    public function down(): void
    {
        Menu::query()->where('process', 9998930)->delete();
    }
};
