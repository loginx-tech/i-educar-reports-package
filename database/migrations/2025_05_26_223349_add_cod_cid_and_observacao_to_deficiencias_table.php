<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cadastro.deficiencia', function (Blueprint $table) {
            $table->string('cod_cid', 20)->nullable();
            $table->text('observacao')->nullable()->after('cod_cid');
        });

        DB::statement("
            UPDATE cadastro.deficiencia
            SET
                cod_cid = CASE cod_deficiencia
                    WHEN 2 THEN 'H54.0'
                    WHEN 3 THEN 'H54.2'
                    WHEN 4 THEN 'H90.3'
                    WHEN 5 THEN 'H90.3'
                    WHEN 6 THEN 'H90 + H54'
                    WHEN 7 THEN 'G83.9'
                    WHEN 8 THEN 'F70–F79'
                    WHEN 9 THEN 'F88'
                    WHEN 10 THEN 'F84.0'
                    WHEN 11 THEN 'F84.5'
                    WHEN 12 THEN 'F84.2'
                    WHEN 13 THEN 'F84.3'
                    WHEN 14 THEN 'Z13.9'
                    WHEN 15 THEN 'Q90.9'
                    WHEN 16 THEN 'F90.0'
                    WHEN 18 THEN 'F91.3'
                    WHEN 19 THEN 'Z99.9'
                    WHEN 20 THEN 'G80.9'
                    WHEN 21 THEN 'G82.2'
                    WHEN 22 THEN 'H91.9'
                    WHEN 23 THEN 'H90.2'
                    WHEN 24 THEN 'G83.4'
                    WHEN 25 THEN 'F70'
                    WHEN 26 THEN 'H54.4'
                    ELSE cod_cid
                END,
                observacao = CASE cod_deficiencia
                    WHEN 2 THEN 'Cegueira, ambos os olhos'
                    WHEN 3 THEN 'Baixa visão em ambos os olhos'
                    WHEN 4 THEN 'Surdez neurossensorial bilateral'
                    WHEN 5 THEN 'Perda auditiva neurossensorial bilateral'
                    WHEN 6 THEN 'Associação entre deficiência auditiva e visual'
                    WHEN 7 THEN 'Transtorno do sistema nervoso central, não especificado'
                    WHEN 8 THEN 'Deficiência intelectual (varia por grau)'
                    WHEN 9 THEN 'Transtornos do desenvolvimento + outra deficiência'
                    WHEN 10 THEN 'Transtorno autista'
                    WHEN 11 THEN 'Síndrome de Asperger'
                    WHEN 12 THEN 'Síndrome de Rett'
                    WHEN 13 THEN 'Transtorno desintegrativo infantil (psicose infantil)'
                    WHEN 14 THEN 'Observação por outras causas (sem CID específico; uso educacional)'
                    WHEN 15 THEN 'Síndrome de Down não especificada'
                    WHEN 16 THEN 'Transtorno de déficit de atenção com hiperatividade'
                    WHEN 18 THEN 'Transtorno desafiador de oposição'
                    WHEN 19 THEN 'Dependência de dispositivos ou anomalias congênitas não especificadas'
                    WHEN 20 THEN 'Paralisia cerebral não especificada'
                    WHEN 21 THEN 'Paraplegia (paralisia dos membros inferiores)'
                    WHEN 22 THEN 'Perda auditiva não especificada (pode ser profunda)'
                    WHEN 23 THEN 'Perda auditiva condutiva bilateral'
                    WHEN 24 THEN 'Monoplegia (paralisia de um membro), entre outros'
                    WHEN 25 THEN 'Déficit intelectual leve (ou outro da faixa F70–F79)'
                    WHEN 26 THEN 'Perda da visão em um olho'
                    ELSE observacao
                END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cadastro.deficiencia', function (Blueprint $table) {
            $table->dropColumn(['cod_cid', 'observacao']);
        });
    }
};
