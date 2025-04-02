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
        if (!Schema::hasTable('wildlife_conflict_dynamic_values')) {
            Schema::create('wildlife_conflict_dynamic_values', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('wildlife_conflict_outcome_id');
                $table->unsignedBigInteger('dynamic_field_id');
                $table->text('field_value')->nullable();
                $table->timestamps();
                
                // Foreign keys with shorter names
                $table->foreign('wildlife_conflict_outcome_id', 'wcdv_outcome_fk')
                    ->references('id')
                    ->on('wildlife_conflict_outcomes')
                    ->onDelete('cascade');
                    
                $table->foreign('dynamic_field_id', 'wcdv_field_fk')
                    ->references('id')
                    ->on('dynamic_fields')
                    ->onDelete('cascade');
                
                // Add a unique constraint with a shorter name
                $table->unique(['wildlife_conflict_outcome_id', 'dynamic_field_id'], 'wco_field_unique');
            });
        } else {
            // If the table exists but doesn't have the unique constraint, add it
            if (!$this->hasIndex('wildlife_conflict_dynamic_values', 'wco_field_unique')) {
                Schema::table('wildlife_conflict_dynamic_values', function (Blueprint $table) {
                    $table->unique(['wildlife_conflict_outcome_id', 'dynamic_field_id'], 'wco_field_unique');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wildlife_conflict_dynamic_values');
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
