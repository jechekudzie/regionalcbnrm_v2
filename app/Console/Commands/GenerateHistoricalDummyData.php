<?php

namespace App\Console\Commands;

use App\Models\Admin\ConflictType;
use App\Models\Admin\CropType;
use App\Models\Admin\Gender;
use App\Models\Admin\LiveStockType;
use App\Models\Admin\Organisation;
use App\Models\Admin\OrganisationType;
use App\Models\Admin\PoachingMethod;
use App\Models\HistoricalData\AnimalControlRecord;
use App\Models\HistoricalData\ConflictRecord;
use App\Models\HistoricalData\CropConflictRecord;
use App\Models\HistoricalData\HumanConflictRecord;
use App\Models\HistoricalData\HumanResourceRecord;
use App\Models\HistoricalData\HuntingRecord;
use App\Models\HistoricalData\IncomeBeneficiaryRecord;
use App\Models\HistoricalData\IncomeRecord;
use App\Models\HistoricalData\IncomeUseRecord;
use App\Models\HistoricalData\LiveStockConflictRecord;
use App\Models\HistoricalData\PoachersRecord;
use App\Models\HistoricalData\PoachingRecord;
use App\Models\HistoricalData\SourceOfIncomeRecord;
use App\Models\Organisation\Species;
use Illuminate\Console\Command;

class GenerateHistoricalDummyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:generate-historical-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate historical dummy data from 2019 to 2023 for the dashboard';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to generate historical dummy data for the dashboard (2019-2023)...');
        
        // Get Rural District Council organizations
        $organisationType = OrganisationType::where('name', 'like', '%Rural District Council%')->first();
        if (!$organisationType) {
            $this->error('Rural District Council organization type not found. Aborting.');
            return 1;
        }
        
        $rdcOrganisations = Organisation::where('organisation_type_id', $organisationType->id)->get();
        if ($rdcOrganisations->isEmpty()) {
            $this->error('No Rural District Council organizations found. Aborting.');
            return 1;
        }
        
        // Get species
        $species = Species::all();
        if ($species->isEmpty()) {
            $this->error('No species found. Aborting.');
            return 1;
        }
        
        // Get poaching methods
        $poachingMethods = PoachingMethod::all();
        if ($poachingMethods->isEmpty()) {
            $this->error('No poaching methods found. Aborting.');
            return 1;
        }
        
        // Get crop types
        $cropTypes = CropType::all();
        if ($cropTypes->isEmpty()) {
            $this->error('No crop types found. Aborting.');
            return 1;
        }
        
        // Get livestock types
        $livestockTypes = LiveStockType::all();
        if ($livestockTypes->isEmpty()) {
            $this->error('No livestock types found. Some records may not be created.');
            return 1;
        }
        
        // Get genders
        $genders = Gender::all();
        if ($genders->isEmpty()) {
            $this->error('No genders found. Some records may not be created.');
            return 1;
        }
        
        $this->info('Found ' . $rdcOrganisations->count() . ' Rural District Councils');
        $this->info('Found ' . $species->count() . ' species');
        $this->info('Found ' . $cropTypes->count() . ' crop types');
        $this->info('Found ' . $livestockTypes->count() . ' livestock types');
        $this->info('Found ' . $genders->count() . ' genders');
        
        // Historical years
        $years = [2019, 2020, 2021, 2022, 2023];
        
        foreach ($rdcOrganisations as $rdc) {
            $this->info("Generating historical data for {$rdc->name}...");
            
            foreach ($years as $year) {
                $this->info("Processing year {$year}...");
                
                // Generate hunting records
                $this->info('Creating hunting records...');
                foreach ($species->random(rand(5, 10)) as $s) {
                    $allocated = rand(5, 50);
                    $utilized = rand(0, $allocated);
                    
                    HuntingRecord::updateOrCreate(
                        [
                            'organisation_id' => $rdc->id,
                            'species_id' => $s->id,
                            'period' => $year
                        ],
                        [
                            'allocated' => $allocated,
                            'utilised' => $utilized
                        ]
                    );
                }
                
                // Generate poaching records
                $this->info('Creating poaching records...');
                foreach ($species->random(rand(3, 8)) as $s) {
                    foreach ($poachingMethods->random(rand(1, 3)) as $method) {
                        PoachingRecord::updateOrCreate(
                            [
                                'organisation_id' => $rdc->id,
                                'species_id' => $s->id,
                                'poaching_method_id' => $method->id,
                                'period' => $year
                            ],
                            [
                                'number' => rand(1, 20),
                                'location' => $this->faker()->city() . ', ' . $this->faker()->country(),
                                'notes' => rand(0, 10) > 7 ? $this->faker()->sentence() : null
                            ]
                        );
                    }
                }
                
                // Generate poachers records
                $this->info('Creating poachers records...');
                foreach ($species->random(rand(2, 4)) as $s) {
                    PoachersRecord::updateOrCreate(
                        [
                            'organisation_id' => $rdc->id,
                            'species_id' => $s->id,
                            'period' => $year
                        ],
                        [
                            'arrested' => rand(0, 30),
                            'bailed' => rand(0, 20),
                            'sentenced' => rand(0, 10),
                            'notes' => rand(0, 10) > 7 ? $this->faker()->sentence() : null
                        ]
                    );
                }
                
                // Generate conflict records
                $this->info('Creating conflict records...');
                foreach ($species->random(rand(3, 6)) as $s) {
                    ConflictRecord::updateOrCreate(
                        [
                            'organisation_id' => $rdc->id,
                            'species_id' => $s->id,
                            'period' => $year
                        ],
                        [
                            'crop_damage_cases' => rand(0, 50),
                            'hectarage_destroyed' => rand(0, 200),
                            'human_injured' => rand(0, 10),
                            'human_death' => rand(0, 5),
                            'livestock_killed_injured' => rand(0, 30),
                            'infrastructure_destroyed' => rand(0, 15),
                            'threat_to_human_life' => rand(0, 40)
                        ]
                    );
                }
                
                // Generate crop conflict records
                $this->info('Creating crop conflict records...');
                foreach ($species->random(rand(2, 5)) as $s) {
                    foreach ($cropTypes->random(rand(1, 3)) as $cropType) {
                        CropConflictRecord::updateOrCreate(
                            [
                                'organisation_id' => $rdc->id,
                                'species_id' => $s->id,
                                'crop_type_id' => $cropType->id,
                                'period' => $year
                            ],
                            [
                                'hectrage_destroyed' => rand(1, 150)
                            ]
                        );
                    }
                }
                
                // Generate livestock conflict records
                $this->info('Creating livestock conflict records...');
                foreach ($species->random(rand(2, 5)) as $s) {
                    foreach ($livestockTypes->random(rand(1, 3)) as $livestockType) {
                        LiveStockConflictRecord::updateOrCreate(
                            [
                                'organisation_id' => $rdc->id,
                                'species_id' => $s->id,
                                'live_stock_type_id' => $livestockType->id,
                                'period' => $year
                            ],
                            [
                                'killed' => rand(0, 30),
                                'injured' => rand(0, 20),
                                'value_destroyed' => rand(1000, 100000)
                            ]
                        );
                    }
                }
                
                // Generate human conflict records
                $this->info('Creating human conflict records...');
                foreach ($species->random(rand(2, 5)) as $s) {
                    foreach ($genders->random(rand(1, 2)) as $gender) {
                        HumanConflictRecord::updateOrCreate(
                            [
                                'organisation_id' => $rdc->id,
                                'species_id' => $s->id,
                                'gender_id' => $gender->id,
                                'period' => $year
                            ],
                            [
                                'deaths' => rand(0, 5),
                                'injured' => rand(0, 15)
                            ]
                        );
                    }
                }
                
                // Generate animal control records
                $this->info('Creating animal control records...');
                foreach ($species->random(rand(2, 5)) as $s) {
                    $total = rand(5, 30);
                    AnimalControlRecord::updateOrCreate(
                        [
                            'organisation_id' => $rdc->id,
                            'species_id' => $s->id,
                            'period' => $year
                        ],
                        [
                            'total' => $total,
                            'shot_on_pad' => rand(0, $total),
                            'shot_on_crop_raids' => rand(0, $total / 2),
                            'shot_as_problem_animal' => rand(0, $total / 2),
                            'method' => $this->faker()->randomElement(['Shooting', 'Trapping', 'Tranquilizing', 'Other'])
                        ]
                    );
                }
                
                // Generate income records
                $this->info('Creating income records...');
                $rdcShare = rand(50000, 500000);
                $communityShare = rand(50000, 300000);
                $caShare = rand(20000, 100000);
                
                IncomeRecord::updateOrCreate(
                    [
                        'organisation_id' => $rdc->id,
                        'period' => $year
                    ],
                    [
                        'rdc_share' => $rdcShare,
                        'community_share' => $communityShare,
                        'ca_share' => $caShare,
                    ]
                );
                
                // Generate income use records
                $this->info('Creating income use records...');
                $totalIncome = $rdcShare + $communityShare + $caShare;
                
                IncomeUseRecord::updateOrCreate(
                    [
                        'organisation_id' => $rdc->id,
                        'period' => $year
                    ],
                    [
                        'administration' => rand(5000, 50000),
                        'conservation' => rand(5000, 50000),
                        'community_projects' => rand(10000, 100000),
                        'household_dividends' => rand(10000, 100000),
                        'education' => rand(5000, 30000),
                        'health' => rand(5000, 30000),
                        'infrastructure' => rand(10000, 80000),
                    ]
                );
                
                // Generate income beneficiary records
                $this->info('Creating income beneficiary records...');
                IncomeBeneficiaryRecord::updateOrCreate(
                    [
                        'organisation_id' => $rdc->id,
                        'period' => $year
                    ],
                    [
                        'direct_beneficiaries' => rand(100, 5000),
                        'indirect_beneficiaries' => rand(1000, 20000),
                        'notes' => rand(0, 10) > 7 ? $this->faker()->sentence() : null
                    ]
                );
                
                // Generate source of income records
                $this->info('Creating source of income records...');
                SourceOfIncomeRecord::updateOrCreate(
                    [
                        'organisation_id' => $rdc->id,
                        'period' => $year
                    ],
                    [
                        'safari_hunting' => rand(50000, 500000),
                        'tourism' => rand(10000, 200000),
                        'fishing' => rand(5000, 50000),
                        'ivory_sales' => rand(0, 30000),
                        'problem_animal_control' => rand(5000, 30000),
                        'hide_sales' => rand(1000, 20000),
                        'meat_sales' => rand(5000, 50000),
                        'carbon_credits' => rand(0, 100000),
                        'donations_grants' => rand(10000, 200000),
                        'miscellaneous' => rand(1000, 30000),
                    ]
                );
                
                // Generate human resource records
                $this->info('Creating human resource records...');
                $totalStaff = rand(10, 100);
                HumanResourceRecord::updateOrCreate(
                    [
                        'organisation_id' => $rdc->id,
                        'period' => $year
                    ],
                    [
                        'permanent_male' => rand(5, $totalStaff / 2),
                        'permanent_female' => rand(2, $totalStaff / 4),
                        'contract_male' => rand(2, $totalStaff / 4),
                        'contract_female' => rand(1, $totalStaff / 8),
                        'seasonal_male' => rand(5, $totalStaff / 2),
                        'seasonal_female' => rand(2, $totalStaff / 4),
                        'voluntary_male' => rand(2, $totalStaff / 4),
                        'voluntary_female' => rand(1, $totalStaff / 8),
                        'trained_male' => rand(5, $totalStaff / 2),
                        'trained_female' => rand(2, $totalStaff / 4),
                    ]
                );
            }
        }
        
        $this->info('Historical dummy data generation completed!');
        
        return 0;
    }
    
    /**
     * Get a Faker instance.
     *
     * @return \Faker\Generator
     */
    protected function faker()
    {
        return app(\Faker\Generator::class);
    }
} 