<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncotermType extends Model

{
    protected $table = 'INCOTERM_TYPES';

    protected $primaryKey = 'ID';

    public $timestamps = false;



    protected $fillable = ['CODE', 'NAME'];


    public function trackingSteps()
    {

        return $this->belongsToMany(TrackingStep::class, 'INCOTERMS', 'INCOTERM_TYPE_ID', 'TRACKING_STEP_ID');

    }




}
