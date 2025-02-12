<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'position',
        'department',
        'contract_type',
        'basic_salary',
        'bank_name',
        'bank_account_number',
        'bank_branch',
        'id_document',
        'nssf_document',
        'nhif_document',
        'passport_photo',
        'birth_certificate',
    ];
}
