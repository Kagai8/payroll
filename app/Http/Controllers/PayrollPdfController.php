<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PayrollPdfController extends Controller
{
    public function generate(Salary $salary)
    {
        $pdf = Pdf::loadView('pdf.payroll', ['salary' => $salary]);
        return $pdf->download('Payroll_' . $salary->id . '.pdf');
    }
}
