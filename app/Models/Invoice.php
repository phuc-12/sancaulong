<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    //
    protected $primaryKey = 'invoice_id';
    protected $fillable = ['customer_id', 'issue_date', 'total_amount', 'promotion_id', 'final_amount', 'payment_status', 'payment_method'];

    // Một hóa đơn có nhiều chi tiết (thường là 1 trong TH này)
    public function invoiceDetails()
    {
        return $this->hasMany(InvoiceDetail::class, 'invoice_id', 'invoice_id');
    }
    
    // Lấy thông tin khách hàng
    public function customer()
    {
        return $this->belongsTo(Users::class, 'customer_id', 'user_id');
    }
}
