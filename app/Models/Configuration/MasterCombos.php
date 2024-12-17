<?php

namespace App\Models\Configuration;

use Illuminate\Database\Eloquent\Model;

class MasterCombos extends Model
{
    protected $table = 'master_combos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'parent_id',
        'code',
        'name',
        'description',
        'valor1',
        'valor2',
        'is_father',
        'status',
        'orden'
    ];

    public function parent()
    {
        return $this->belongsTo(MasterCombos::class, 'parent_id');
    }

    public static function getChildrens($names)
    {
        $names = explode(',', $names);

        $responses = MasterCombos::whereIn('parent_id', function ($query) use ($names) {
            $query->select('id')
                ->from('master_combos')
                ->whereIn('code', $names);
        })
            ->where('status', true)
            ->orderBy('orden', 'asc')
            ->get()
            ->groupBy("parent_id");


        $comboResponses = [];
        foreach ($responses as $index => $value) {
            $parent = MasterCombos::find($index)->code;
            $comboResponses[$parent] = $value->toArray();
        }

        return $comboResponses;
    }

    public static function getEstadoSolicitud($status)
    {
        $estado = MasterCombos::whereRaw("parent_id = (SELECT id FROM master_combos WHERE code = 'estados_solicitud')")
            ->whereRaw("LOWER(code) = LOWER('$status')")
            ->get()
            ->first();
        return $estado ? $estado->id : null;
    }
}
