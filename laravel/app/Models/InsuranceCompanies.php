<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class InsuranceCompanies extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;
    protected $table = 'insurers';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public $sortable = ['code', 'insurance_name_th', 'contact_name', 'contact_email', 'contact_tel'];
    // public $appends = [
    //     'car_brands.name'
    // ];
//    public $sortableAs = ['car_brand_name', 'car_category_name', 'car_group_name'];

    public function scopegetInsuranceCompaniesList($query)
    {
       $InsuranceCompaniesData = $query->select('insurance_name_th as id','insurance_name_th as name','insurance_name_th as value')
           ->groupby('insurance_name_th')
           ->get();

        if(!empty($InsuranceCompaniesData)){
            return $InsuranceCompaniesData;
        }else{
            return [];
        }
    }

    public function scopegetInsuranceCompaniesListAll($query)
    {
        $InsuranceCompaniesData = $query->select('id','insurance_name_th as name','insurance_name_th as value')
            ->where('status',STATUS_ACTIVE)
            ->get();

        if(!empty($InsuranceCompaniesData)){
            return $InsuranceCompaniesData;
        }else{
            return [];
        }
    }

    public function scopegetinsuranceCompaniesDataList($query,$s){
        $InsuranceCompaniesDataList = $query->select('id','code','insurance_name_th','contact_name','contact_email','contact_tel')
            ->when(!empty($s), function ($query) use ($s) {
                return $query->where(function ($q) use ($s) {
                    $q->where('insurance_name_th', 'like', '%' . $s . '%');
                });
            })->sortable('code')->paginate(PER_PAGE);;

        if(!empty($InsuranceCompaniesDataList)){
            return $InsuranceCompaniesDataList;
        }else{
            return [];
        }
    }

}
