<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    protected $table = 'invoice_details';
    protected $primaryKey = 'invoice_detail_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'invoice_detail_id',
        'invoice_id',   
        'facility_id',
        'sub_total',   
    ];
}