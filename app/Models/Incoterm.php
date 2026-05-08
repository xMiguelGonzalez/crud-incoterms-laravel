<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\IncotermType;

class Incoterm extends Model

{

    protected $table = 'INCOTERMS';
    
    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected $fillable = ['INCOTERM_TYPE_ID', 'TRACKING_STEP_ID'];


    public function incotermType()
    {

        return $this->belongsTo(IncotermType::class, 'INCOTERM_TYPE_ID');

    }

    public function trackingStep()
    {

        return $this->belongsTo(TrackingStep::class, 'TRACKING_STEP_ID');
        
    }


}
