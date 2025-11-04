namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/',
        '/listing-grid',
        '/api/load-more-san',
        '/venue',
        '/thanh-toan',
        '/booking/add-slot',
        '/booking/remove-slot',
        '/thanh-toan/thanh-toan-complete',
        '/contract_bookings',
        '/contracts_preview',
        '/payment_contract',
        '/thanh-toan/thanh-toan-contract-complete',
        '/list_Invoices',
        '/list_Contracts',
        '/invoice_details',
        '/cancel_invoice',
    ];
}
