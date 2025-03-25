<?php

namespace App\Models\HistoricalData;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HumanResourceRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'organisation_id',
        'period',
        'wildlife_managers',
        'game_scouts',
        'rangers',
        'employed_by',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'period' => 'integer',
        'wildlife_managers' => 'integer',
        'game_scouts' => 'integer',
        'rangers' => 'integer',
    ];

    /**
     * Get the organisation that owns the human resource record.
     */
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Get the total number of staff.
     *
     * @return int
     */
    public function getTotalStaffAttribute()
    {
        return $this->wildlife_managers + $this->game_scouts + $this->rangers;
    }

    /**
     * Get the total number of community employees.
     *
     * @return int
     */
    public function getTotalCommunityEmployeesAttribute()
    {
        return $this->employed_by === 'community' ? $this->total_staff : 0;
    }

    /**
     * Get the total number of organisation employees.
     *
     * @return int
     */
    public function getTotalOrganisationEmployeesAttribute()
    {
        return $this->employed_by === 'organisation' ? $this->total_staff : 0;
    }
}
