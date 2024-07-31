<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EstrategiaWms extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tb_estrategia_wms';
    protected $primaryKey = 'cd_estrategia_wms';

    protected $fillable = [
        'ds_estrategia_wms',
        'nr_prioridade'
    ];

    public function horarios()
    {
        return $this->hasMany(EstrategiaWmsHorarioPrioridade::class, 'cd_estrategia_wms', 'cd_estrategia_wms');
    }
}
