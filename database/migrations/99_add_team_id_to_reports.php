<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'reportes_soporte' => 'fecha_creacion',
            'reportes_moderacion' => 'fecha',
        ] as $tableName => $dateColumn) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (! Schema::hasColumn($tableName, 'team_id')) {
                    $table->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete();
                }
            });

            $indexName = $tableName.'_team_id_'.$dateColumn.'_index';

            if (! Schema::hasIndex($tableName, $indexName)) {
                Schema::table($tableName, function (Blueprint $table) use ($dateColumn, $indexName): void {
                    $table->index(['team_id', $dateColumn], $indexName);
                });
            }

            $userColumn = $tableName === 'reportes_soporte' ? 'id_usuario' : 'id_reportador';
            DB::table($tableName)
                ->whereNull('team_id')
                ->orderBy('id_reporte')
                ->chunkById(100, function ($reports) use ($tableName, $userColumn): void {
                    foreach ($reports as $report) {
                        $teamId = DB::table('users')
                            ->where('id', $report->{$userColumn})
                            ->value('current_team_id');

                        if ($teamId !== null) {
                            DB::table($tableName)
                                ->where('id_reporte', $report->id_reporte)
                                ->update(['team_id' => $teamId]);
                        }
                    }
                }, 'id_reporte', 'id_reporte');
        }
    }

    public function down(): void
    {
        foreach (['reportes_soporte', 'reportes_moderacion'] as $tableName) {
            if (! Schema::hasTable($tableName) || ! Schema::hasColumn($tableName, 'team_id')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropForeign(['team_id']);
                $table->dropColumn('team_id');
            });
        }
    }
};