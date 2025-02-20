<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll PDF</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Payroll for {{ $salary->employee->first_name }} {{ $salary->employee->last_name }}</h2>
    <table>
        <tr><th>Basic Salary</th><td>KES {{ number_format($salary->basic_salary, 2) }}</td></tr>
        <tr><th>House Allowance</th><td>KES {{ number_format($salary->house_allowance, 2) }}</td></tr>
        <tr><th>Transport Allowance</th><td>KES {{ number_format($salary->transport_allowance, 2) }}</td></tr>
        <tr><th>Other Earnings</th><td>KES {{ number_format(collect($salary->extra_earnings)->sum('amount'), 2) }}</td></tr>
        <tr><th>Total Deductions</th><td>KES {{ number_format($salary->paye_tax + $salary->nhif_contribution + $salary->nssf_contribution + collect($salary->extra_deductions)->sum('amount'), 2) }}</td></tr>
        <tr><th>Net Salary</th><td><strong>KES {{ number_format($salary->net_salary, 2) }}</strong></td></tr>
    </table>
</body>
</html>
