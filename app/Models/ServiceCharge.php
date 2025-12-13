
<?php
// app/Models/ServiceCharge.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCharge extends Model
{
    protected $table = 'service_charges';

    protected $fillable = [
        'percentage',
        'fee_type',
        'is_active',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
