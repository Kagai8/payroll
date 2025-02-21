<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; font-size: 20px; font-weight: bold; }
        .company-info { text-align: center; font-size: 14px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        .signature { margin-top: 20px; }
        .text-right { text-align: right; }
    </style>
</head>
<body>

    <div class="header">Payroll Slip</div>
    <div class="company-info">Your Company Name | Your Company Address</div>

    <!-- Employee Details -->
    <table class="employee-details">
        <tr>
            <td><strong>Name:</strong> {{ $salary->employee->first_name }} {{ $salary->employee->last_name }}</td>
            <td><strong>Position:</strong> {{ $salary->employee->position }}</td>
        </tr>
        <tr>
            <td><strong>Department:</strong> {{ $salary->employee->department }}</td>
            <td><strong>Contract Type:</strong> {{ $salary->employee->contract_type }}</td>
        </tr>
        <tr>
            <td><strong>Bank:</strong> {{ $salary->employee->bank_name }}</td>
            <td><strong>Account No:</strong> {{ $salary->employee->bank_account_number }}</td>
        </tr>
        <tr>
            <td><strong>NHIF No:</strong> {{ $salary->employee->nhif_document }}</td>
            <td><strong>NSSF No:</strong> {{ $salary->employee->nssf_document }}</td>
        </tr>
    </table>

    <!-- Salary Details Table -->
    <table class="table">
        <tr>
            <th>Earnings</th>
            <th>Amount (KES)</th>
            <th>Deductions</th>
            <th>Amount (KES)</th>
        </tr>
        <tr>
            <td>Basic Salary</td>
            <td>{{ number_format($salary->basic_salary, 2) }}</td>
            <td>PAYE</td>
            <td>{{ number_format($salary->paye_tax, 2) }}</td>
        </tr>
        <tr>
            <td>House Allowance</td>
            <td>{{ number_format($salary->house_allowance, 2) }}</td>
            <td>NHIF</td>
            <td>{{ number_format($salary->sha_contribution, 2) }}</td>
        </tr>
        <tr>
            <td>Transport Allowance</td>
            <td>{{ number_format($salary->transport_allowance, 2) }}</td>
            <td>NSSF</td>
            <td>{{ number_format($salary->nssf_contribution, 2) }}</td>
        </tr>

        @php
            $maxRows = max(count($extraEarnings), count($extraDeductions));
        @endphp

        @for ($i = 0; $i < $maxRows; $i++)
        <tr>
            <td>{{ $extraEarnings[$i]['name'] ?? '' }}</td>
            <td>{{ isset($extraEarnings[$i]) ? number_format($extraEarnings[$i]['amount'], 2) : '' }}</td>
            <td>{{ $extraDeductions[$i]['name'] ?? '' }}</td>
            <td>{{ isset($extraDeductions[$i]) ? number_format($extraDeductions[$i]['amount'], 2) : '' }}</td>
        </tr>
        @endfor

        <tr>
            <th>Total Earnings</th>
            <th>{{ number_format(
                $salary->basic_salary +
                $salary->house_allowance +
                $salary->transport_allowance +
                collect($extraEarnings)->sum('amount'), 2) }}
            </th>
            <th>Total Deductions</th>
            <th>{{ number_format(
                $salary->paye_tax +
                $salary->nhif_contribution +
                $salary->sha_contribution +
                collect($extraDeductions)->sum('amount'), 2) }}
            </th>
        </tr>

        <tr>
            <th colspan="3">Net Pay</th>
            <th>{{ number_format($salary->net_salary, 2) }}</th>
        </tr>
    </table>

    <!-- Signature Section -->
    <div class="signature">
        <table width="100%">
            <tr>
                <td style="text-align: right;">Employer's Signature: _______________</td>
            </tr>
        </table>
    </div>

</body>

</html>
