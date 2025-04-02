<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('wildlife_conflict_outcomes')) {
            Schema::create('wildlife_conflict_outcomes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('wildlife_conflict_incident_id');
                $table->unsignedBigInteger('conflict_out_come_id');
                $table->timestamps();
                
                // Foreign keys with shorter names
                $table->foreign('wildlife_conflict_incident_id', 'wco_incident_fk')
                    ->references('id')
                    ->on('wildlife_conflict_incidents')
                    ->onDelete('cascade');
                    
                $table->foreign('conflict_out_come_id', 'wco_outcome_fk')
                    ->references('id')
                    ->on('conflict_out_comes')
                    ->onDelete('cascade');
                
                // Add a unique constraint
                $table->unique(['wildlife_conflict_incident_id', 'conflict_out_come_id'], 'wci_outcome_unique');
            });
        } else {
            // If the table exists but doesn't have the unique constraint, add it
            if (!$this->hasIndex('wildlife_conflict_outcomes', 'wci_outcome_unique')) {
                Schema::table('wildlife_conflict_outcomes', function (Blueprint $table) {
                    $table->unique(['wildlife_conflict_incident_id', 'conflict_out_come_id'], 'wci_outcome_unique');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wildlife_conflict_outcomes');
    }
    
    /**
     * Check if an index exists using raw SQL
     */
    private function hasIndex($table, $indexName)
    {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = '{$indexName}'");
        return count($indexes) > 0;
    }
};
