<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ConsultaStockGpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $sql_gp="SELECT b.ITEMNMBR as CODIGO,
        d.ITEMDESC as NOMBRE,
        d.ITMCLSCD as CLASE,
        b.LOTNUMBR AS LOTE,
        c.LOTATRB2 AS LOTEPROV,
        c.LOTATRB3 AS NUMANALISIS,
        e.calificacionID as CALIFICACION,
        format(b.EXPNDATE,'dd-MM-yy') AS VENCIMIENTO,
        format(c.LOTATRB4,'dd-MM-yy') AS REANALISIS,
        cast((b.QTYRECVD-b.QTYSOLD) as varchar) as CANTIDAD,
        b.LOCNCODE as SITIO,
        d.UOMSCHDL AS UN
        FROM IV00300 as b 
        inner join IV00301 as c on  b.ITEMNMBR = c.ITEMNMBR and b.LOTNUMBR = c.LOTNUMBR
        left join iv00101 as d on d.ITEMNMBR = c.ITEMNMBR
        left join II_Lot_Calificacion e on b.ITEMNMBR = e.ITEMNMBR and b.LOTNUMBR = e.LOTNUMBR  
        order by b.ITEMNMBR,b.LOTNUMBR, b.LOCNCODE;"; 
        $gpstock = collect(DB::connection('gp')->select($sql_gp));

        return view('stockgp.consultastock',compact('gpstock'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
   
        public function show(Request $request )
        {
            //
            $lote=request(key: 'lote');
            $producto=request(key:'producto');
            $sitio=request(key:'sitio');
            $sql="SELECT [CODIGO_Articulo]
      ,[DESCRIPCION]
      ,[DESCR_Breve]
      ,[Propietario]
      ,[Lote]
      ,[Sitio]
      ,[Clase]
      ,cast((StockTotal) as varchar) as StockTotal
      ,[Calificacion]
      ,cast((Asignado) as varchar) as Asignado
      ,cast((StockTotal - Asignado) as varchar) as Disponible
      ,[UNIDAD]
      ,[LoteProveedor]
      ,[NºAnalisis]
      ,format([Vencimiento],'dd/MM/yyyy') as Vencimiento
      ,[UnidadCompras]
      ,format([FechaAnalisis],'dd/MM/yyyy') as FechaAnalisis
      ,format([FechaReanalisis],'dd/MM/yyyy') as FechaReanalisis
      ,[Analista]
      ,[VidaUtilAnalisis]
      ,[Controlado]
      ,[Fact_Conver]
      ,[Costo_Standard]
      ,[Costo_Corriente]
      ,[FechaRecepcion]
      ,[Tipo/estado] FROM II_IV_LOTES_2  where lote='". $lote ."' and codigo_articulo='". $producto ."' and sitio='". $sitio ."';";

            $encabezado = DB::connection('gp')->select($sql);
      
            $movimientos=DB::connection('gp')->select('SET NOCOUNT ON ; exec GR_INTRANET_DETALLE_LOTE ?, ?;', array($producto, $lote));
           // dd($lote, $producto,$encabezado,$movimientos);
            return view('stockgp.detallestock', compact('encabezado', 'movimientos'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
