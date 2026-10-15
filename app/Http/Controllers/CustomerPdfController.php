<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CustomerPdfController
{
    public function generate(Customer $customer)
    {
        $urlAgendamiento = 'https://flowacess-production.up.railway.app/agendamiento';

        $qrCode = base64_encode(QrCode::format('svg')->size(200)->generate($urlAgendamiento));

        $pdf = Pdf::loadView('pdf.customer-ticket', [
            'customer' => $customer,
            'qrCode' => $qrCode,
        ]);

        return $pdf->download(
            'Registro-' . $customer->CodigoCustomer . '.pdf'
        );
    }
}