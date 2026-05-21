<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AgregarMenuReporteArticulosVendidos extends Migration
{
    const MENU_REPORTES_VENTAS_ID = 69;

    /**
     * @return void
     */
    public function up()
    {
        $menuId = DB::table('menu')->insertGetId([
            'menu_id' => self::MENU_REPORTES_VENTAS_ID,
            'nombre' => 'Artículos vendidos',
            'url' => 'ventas/reparticulovendido',
            'orden' => 7,
            'icono' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $rolIds = DB::table('menu_rol')
            ->whereIn('menu_id', function ($query) {
                $query->select('id')
                    ->from('menu')
                    ->where('menu_id', self::MENU_REPORTES_VENTAS_ID);
            })
            ->distinct()
            ->pluck('rol_id');

        foreach ($rolIds as $rolId) {
            DB::table('menu_rol')->insert([
                'rol_id' => $rolId,
                'menu_id' => $menuId,
            ]);
        }
    }

    /**
     * @return void
     */
    public function down()
    {
        $menu = DB::table('menu')
            ->where('menu_id', self::MENU_REPORTES_VENTAS_ID)
            ->where('url', 'ventas/reparticulovendido')
            ->first();

        if ($menu) {
            DB::table('menu_rol')->where('menu_id', $menu->id)->delete();
            DB::table('menu')->where('id', $menu->id)->delete();
        }
    }
}
