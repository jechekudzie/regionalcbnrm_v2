<?php

namespace App\Console\Commands;

use App\Models\Admin\ConflictType;
use App\Models\Admin\ControlMeasure;
use App\Models\Admin\IdentificationType;
use App\Models\Admin\OffenceType;
use App\Models\Admin\Organisation;
use App\Models\Admin\OrganisationType;
use App\Models\Admin\PoacherType;
use App\Models\Admin\PoachingMethod;
use App\Models\Admin\PoachingReason;
use App\Models\Organisation\HuntingActivity;
use App\Models\Organisation\HuntingConcession;
use App\Models\Organisation\Poacher;
use App\Models\Organisation\PoachingIncident;
use App\Models\Organisation\PoachingIncidentSpecies;
use App\Models\Organisation\ProblemAnimalControl;
use App\Models\Organisation\QuotaAllocation;
use App\Models\Organisation\Species;
use App\Models\Organisation\WildlifeConflictIncident;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateDashboardDummyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:generate-dummy-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate dummy data for the organisation dashboard';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to generate dummy data for the dashboard...');
        
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
        
        // Get conflict types and control measures
        $conflictTypes = ConflictType::all();
        $controlMeasures = ControlMeasure::all();
        
        $this->info('Found ' . $rdcOrganisations->count() . ' Rural District Councils');
        $this->info('Found ' . $species->count() . ' species');
        
        foreach ($rdcOrganisations as $rdc) {
            $this->info("Generating data for {$rdc->name}...");
            
            // Create hunting concessions
            $this->info('Creating hunting concessions...');
            $concessions = [];
            for ($i = 0; $i < rand(2, 5); $i++) {
                $concession = new HuntingConcession();
                $concession->organisation_id = $rdc->id;
                $concession->name = "Concession " . ($i + 1) . " - " . $rdc->name;
                $concession->description = "Hunting concession " . ($i + 1) . " for " . $rdc->name;
                $concession->hectarage = rand(1000, 50000);
                $concession->latitude = $this->faker()->latitude(-22.0, -15.0);
                $concession->longitude = $this->faker()->longitude(25.0, 33.0);
                $concession->save();
                
                $concessions[] = $concession;
            }
            
            // Create quota allocations
            $this->info('Creating quota allocations...');
            foreach ($species->random(rand(5, 10)) as $s) {
                $quota = new QuotaAllocation();
                $quota->organisation_id = $rdc->id;
                $quota->species_id = $s->id;
                $quota->hunting_quota = rand(5, 20);
                $quota->rational_killing_quota = rand(1, $quota->hunting_quota);
                $quota->start_date = now()->startOfYear();
                $quota->end_date = now()->endOfYear();
                $quota->period = now()->year;
                $quota->notes = rand(0, 10) > 7 ? $this->faker()->sentence() : null;
                $quota->save();
            }
            
            // Create hunting activities
            $this->info('Creating hunting activities...');
            $huntingActivities = [];
            for ($i = 0; $i < rand(5, 15); $i++) {
                $startDate = now()->subMonths(rand(0, 11))->subDays(rand(0, 30));
                $endDate = (clone $startDate)->addDays(rand(1, 30));
                
                $activity = new HuntingActivity();
                $activity->organisation_id = $rdc->id;
                $activity->hunting_concession_id = $concessions[array_rand($concessions)]->id;
                $activity->safari_id = $rdc->id; // Using the same RDC ID for safari_id for simplicity
                $activity->start_date = $startDate;
                $activity->end_date = $endDate;
                $activity->period = $startDate->year;
                $activity->save();
                
                // Add species to hunting activity
                foreach ($species->random(rand(1, 3)) as $s) {
                    DB::table('hunting_activity_species')->insert([
                        'hunting_activity_id' => $activity->id,
                        'species_id' => $s->id,
                        'off_take' => rand(1, 5),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                $huntingActivities[] = $activity;
            }
            
            // Create wildlife conflict incidents
            $this->info('Creating wildlife conflict incidents...');
            $conflictIncidents = [];
            for ($i = 0; $i < rand(8, 20); $i++) {
                $incidentDate = now()->subMonths(rand(0, 11))->subDays(rand(0, 30));
                $latitude = $this->faker()->latitude(-22.0, -15.0);
                $longitude = $this->faker()->longitude(25.0, 33.0);
                
                $incident = new WildlifeConflictIncident();
                $incident->organisation_id = $rdc->id;
                $incident->title = 'Wildlife Conflict: ' . $this->faker()->words(3, true);
                $incident->period = $incidentDate->year;
                $incident->incident_date = $incidentDate;
                $incident->incident_time = $incidentDate->format('H:i:s');
                $incident->longitude = $longitude;
                $incident->latitude = $latitude;
                $incident->location_description = rand(0, 10) > 2 ? $this->faker()->sentence() : null;
                $incident->description = $this->faker()->paragraph();
                $incident->conflict_type_id = $conflictTypes->isNotEmpty() ? $conflictTypes->random()->id : null;
                $incident->save();
                
                // Add species to conflict incident
                foreach ($species->random(rand(1, 2)) as $s) {
                    $incident->species()->attach($s->id);
                }
                
                $conflictIncidents[] = $incident;
            }
            
            // Create problem animal control records
            $this->info('Creating problem animal control records...');
            for ($i = 0; $i < rand(5, 15); $i++) {
                $controlDate = now()->subMonths(rand(0, 11))->subDays(rand(0, 30));
                $latitude = $this->faker()->latitude(-22.0, -15.0);
                $longitude = $this->faker()->longitude(25.0, 33.0);
                
                $pac = new ProblemAnimalControl();
                $pac->organisation_id = $rdc->id;
                $pac->wildlife_conflict_incident_id = !empty($conflictIncidents) ? $conflictIncidents[array_rand($conflictIncidents)]->id : null;
                $pac->control_date = $controlDate;
                $pac->control_time = $controlDate->format('H:i:s');
                $pac->period = $controlDate->year;
                $pac->location = $this->faker()->city() . ', ' . $this->faker()->country();
                $pac->description = $this->faker()->paragraph();
                $pac->latitude = $latitude;
                $pac->longitude = $longitude;
                $pac->estimated_number = rand(1, 10);
                $pac->save();
                
                // Attach control measures if available
                if ($controlMeasures->isNotEmpty()) {
                    $pac->controlMeasures()->attach(
                        $controlMeasures->random(rand(1, 3))->pluck('id')->toArray()
                    );
                }
            }
            
            // Create poaching incidents
            $this->info('Creating poaching incidents...');
            $poachingMethods = PoachingMethod::all();
            for ($i = 0; $i < rand(5, 15); $i++) {
                $incidentDate = now()->subMonths(rand(0, 11))->subDays(rand(0, 30));
                $latitude = $this->faker()->latitude(-22.0, -15.0);
                $longitude = $this->faker()->longitude(25.0, 33.0);
                
                $poachingIncident = new PoachingIncident();
                $poachingIncident->organisation_id = $rdc->id;
                $poachingIncident->title = 'Poaching Incident: ' . $this->faker()->words(3, true);
                $poachingIncident->location = $this->faker()->city() . ', ' . $this->faker()->country();
                $poachingIncident->longitude = $longitude;
                $poachingIncident->latitude = $latitude;
                $poachingIncident->docket_number = 'DOC-' . rand(1000, 9999);
                $poachingIncident->docket_status = $this->faker()->randomElement(['open', 'under investigation', 'closed', 'pending court', 'convicted']);
                $poachingIncident->period = $incidentDate->year;
                $poachingIncident->date = $incidentDate;
                $poachingIncident->time = $incidentDate->format('H:i:s');
                $poachingIncident->save();
                
                // Add species to poaching incident
                foreach ($species->random(rand(1, 3)) as $s) {
                    PoachingIncidentSpecies::create([
                        'poaching_incident_id' => $poachingIncident->id,
                        'species_id' => $s->id,
                        'estimate_number' => rand(1, 5),
                    ]);
                }
                
                // Add poaching methods
                if ($poachingMethods->isNotEmpty()) {
                    $poachingIncident->methods()->attach(
                        $poachingMethods->random(rand(1, 2))->pluck('id')->toArray()
                    );
                }
                
                // Create poachers
                $poacherTypes = PoacherType::all();
                $offenceTypes = OffenceType::all();
                $poachingReasons = PoachingReason::all();
                $statuses = ['suspected', 'arrested', 'bailed', 'sentenced', 'released'];
                
                for ($j = 0; $j < rand(1, 4); $j++) {
                    $poacher = new Poacher();
                    $poacher->poaching_incident_id = $poachingIncident->id;
                    $poacher->first_name = $this->faker()->firstName();
                    $poacher->last_name = $this->faker()->lastName();
                    $poacher->middle_name = rand(0, 10) > 6 ? $this->faker()->firstName() : null;
                    $poacher->age = rand(18, 60);
                    $poacher->country_id = null; // Would need to fetch from a countries table
                    $poacher->province_id = null; // Would need to fetch from a provinces table
                    $poacher->city_id = null; // Would need to fetch from a cities table
                    $poacher->poacher_type_id = $poacherTypes->isNotEmpty() ? $poacherTypes->random()->id : null;
                    $poacher->offence_type_id = $offenceTypes->isNotEmpty() ? $offenceTypes->random()->id : null;
                    $poacher->poaching_reason_id = $poachingReasons->isNotEmpty() ? $poachingReasons->random()->id : null;
                    $poacher->status = $this->faker()->randomElement($statuses);
                    $poacher->save();
                }
            }
        }
        
        $this->info('Dummy data generation completed!');
        
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