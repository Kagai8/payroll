<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'basic_salary',
        'house_allowance',
        'transport_allowance',
        'extra_earnings',
        'paye_tax',
        'sha_contribution',
        'nssf_contribution',
        'extra_deductions',
        'net_salary',
    ];

    protected $casts = [
        'extra_earnings' => 'array',
        'extra_deductions' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public static function beforeSave($record, $data)
        {
            $basic = $data['basic_salary'] ?? 0;
            $house = $data['house_allowance'] ?? 0;
            $transport = $data['transport_allowance'] ?? 0;
            $earnings = collect($data['extra_earnings'] ?? [])->sum(fn ($item) => (float) $item['amount']);
            $deductions = collect($data['extra_deductions'] ?? [])->sum(fn ($item) => (float) $item['amount']);
            $paye = $data['paye_tax'] ?? 0;
            $nhif = $data['nhif_contribution'] ?? 0;
            $nssf = $data['nssf_contribution'] ?? 0;

            $record->net_salary = ($basic + $house + $transport + $earnings) - ($paye + $nhif + $nssf + $deductions);
        }
}
