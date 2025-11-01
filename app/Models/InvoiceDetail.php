<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    //
    protected $primaryKey = 'invoice_detail_id';
    protected $fillable = ['invoice_id', 'booking_id', 'sub_total', 'quantity'];
    
    // Chi tiết này thuộc về 1 booking
    public function booking()
    {
        return $this->belongsTo(Bookings::class, 'booking_id', 'booking_id');
    }
    
    // Chi tiết này thuộc về 1 hóa đơn
     public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }
}
