<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Organisation\QuotaAllocation;
use App\Models\Organisation\QuotaAllocationBalance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupQuotaBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quota:cleanup-balances';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up duplicate quota allocation balance records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting quota balance cleanup...');

        // Get all quota allocations
        $quotaAllocations = QuotaAllocation::all();
        $this->info("Found {$quotaAllocations->count()} quota allocations");

        $totalFixed = 0;
        $totalDeleted = 0;

        DB::beginTransaction();
        try {
            foreach ($quotaAllocations as $quotaAllocation) {
                // Get all balances for this allocation
                $balances = QuotaAllocationBalance::where('quota_allocation_id', $quotaAllocation->id)
                    ->orderBy('updated_at', 'desc')
                    ->get();
                
                if ($balances->count() > 1) {
                    // Keep the most recently updated balance
                    $mainBalance = $balances->first();
                    $this->info("Found {$balances->count()} balances for quota allocation {$quotaAllocation->id}, keeping most recent: {$mainBalance->id}");
                    
                    // Delete duplicates
                    $duplicates = $balances->slice(1);
                    foreach ($duplicates as $duplicate) {
                        $this->info("Deleting duplicate balance {$duplicate->id}");
                        $duplicate->delete();
                        $totalDeleted++;
                    }

                    $totalFixed++;
                }
            }

            DB::commit();
            $this->info("Cleanup complete! Fixed {$totalFixed} quota allocations, deleted {$totalDeleted} duplicate balance records.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error during cleanup: {$e->getMessage()}");
            Log::error("Error during quota balance cleanup: {$e->getMessage()}", [
                'exception' => $e
            ]);
        }
    }
}
