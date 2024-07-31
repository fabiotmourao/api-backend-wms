<?php

namespace App\Models;

use App\Models\EstrategiaWms;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EstrategiaWmsHorarioPrioridade extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tb_estrategia_wms_horario_prioridade';
    protected $primaryKey = 'cd_estrategia_wms_horario_prioridade';

    protected $fillable = [
        'cd_estrategia_wms',
        'ds_horario_inicio',
        'ds_horario_final',
        'nr_prioridade'
    ];

    public function estrategia()
    {
        return $this->belongsTo(EstrategiaWms::class, 'cd_estrategia_wms', 'cd_estrategia_wms');
    }
}
