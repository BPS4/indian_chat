<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GstDetail extends Model
{
    protected $fillable = [
        'booking_id',
        'gst_no',
        'company_name',
        'address',
    ];
}
