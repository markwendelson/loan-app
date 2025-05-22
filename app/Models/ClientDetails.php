<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientDetails extends Model
{
    protected $appends = ['fullname','present_full_address','permanent_full_address'];

    // protected $fillable = [];

    protected $guarded = [];

    public function scopeFilter($query, $search)
    {
        if (!empty($search)) {
            $query->where('id', 'LIKE', '%'.$search.'%')
                ->orWhere('name', 'LIKE', '%'.$search.'%')
                ->orWhere('address', 'LIKE', '%'.$search.'%');
        }

        return $query;
    }

    public function getFullNameAttribute()
    {
        return trim(collect([
            $this->firstname,
            $this->middlename,
            $this->lastname,
        ])->filter()->implode(' '));
    }

    public function getPresentFullAddressAttribute()
    {
        return trim(collect([
            $this->present_address,
            $this->presentCity->name ?? '',
            $this->presentProvince->name ?? '',
        ])->filter()->implode(' '));
    }

    public function getPermanentFullAddressAttribute()
    {
        return trim(collect([
            $this->permanent_address,
            $this->permanentCity->name ?? '',
            $this->permanentProvince->name ?? '',
        ])->filter()->implode(' '));
    }

    public function presentProvince()
    {
        return $this->belongsTo(Province::class, 'present_province_id');
    }

    public function presentCity()
    {
        return $this->belongsTo(City::class, 'present_city_id');
    }

    public function permanentProvince()
    {
        return $this->belongsTo(Province::class, 'permanent_province_id');
    }

    public function permanentCity()
    {
        return $this->belongsTo(City::class, 'permanent_city_id');
    }
}
