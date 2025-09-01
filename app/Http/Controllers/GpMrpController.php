<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class GpMrpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $mrp = DB::connection('gp')->select('SET NOCOUNT ON ; exec GR_INTRANET_mrp;');

           

       // dd($mrp, $ultcons);
        return view('stockgp.mrp', compact('mrp'));
       
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
    public function show(Request $request)
    {
        $producto = request(key: 'producto');
        //cabecera
        $sqlcabecera = "SELECT a.ITEMNMBR as CODIGO,
                        A.ITEMDESC AS NOMBRE,
                        CONCAT(rtrim(A.UOMSCHDL), ' - ',B.UMSCHDSC) AS UM,
                        A.ITMCLSCD as CLASE
                        FROM IV00101 AS a LEFT JOIN
                        IV40201 AS b ON a.UOMSCHDL = b.UOMSCHDL
                        where ITEMNMBR = '" . $producto . "';";

        $encabezado = DB::connection('gp')->select($sqlcabecera);
        //INVENTARIO
        $sqlinventario = "SELECT B.LOTNUMBR AS LOTE,
                        C.LOTATRB2 AS LOTEPROV,
                        C.LOTATRB3 AS NUMEROANALISIS,
                        D.CalificacionID AS calif,
                        format(C.LOTATRB4,'dd/MM/yyyy') AS FVENCIMIENTO,
                        format(C.LOTATRB5,'dd/MM/yyyy') AS FREANALISIS,
                        B.LOCNCODE AS SITIO,
                        cast((COALESCE(B.QTYRECVD,0) -COALESCE(B.QTYSOLD,0)) as varchar) AS CANTIDAD,
                        cast(B.ATYALLOC as varchar) AS COMPROMETIDO
                        FROM IV00300 as b inner join IV00301 as c
                        on  b.ITEMNMBR = c.ITEMNMBR and b.LOTNUMBR = c.LOTNUMBR
                        left join  II_Lot_Calificacion d on b.ITEMNMBR=d.ITEMNMBR and b.LOTNUMBR=d.LOTNUMBR 
                        where 
                        b.ITEMNMBR = '" . $producto . "';";
        $inventario = DB::connection('gp')->select($sqlinventario);
        //COMPRAS
        $sqlcompras = "SELECT A.PONUMBER AS OC,
                        format(C.DOCDATE,'dd/MM/yyyy') AS FECHAOC,
                        format(A.REQDATE,'dd/MM/yyyy') AS FREQUERIDA,
                        CONCAT(RTRIM(C.VENDORID),' - ',C.VENDNAME) AS PROVEEDOR,
                        D.SOPNUMBE AS SIC,
                        cast(A.QTYORDER as varchar)AS CANTPEDIDA,
                        cast(b.QTYSHPPD as varchar)AS RECIBIDO,
                        cast(b.QTYREPLACED as varchar) AS DEVUELTA,
                        cast(B.QTYREJ as varchar) AS RECHAZADO,
                        cast((COALESCE(A.QTYORDER,0)-COALESCE(b.QTYSHPPD,0)+COALESCE(b.QTYREPLACED,0)) as varchar) AS NETO,
                        format(B.DATERECD,'dd/MM/yyyy') AS FDOC
                        FROM POP10110 a
                        LEFT JOIN POP10100 C ON A.PONUMBER=C.PONUMBER
                        left join  POP10500 b on a.PONUMBER=b.PONUMBER and a.ORD=b.POLNENUM and b.POPTYPE in (1,3)
                        LEFT JOIN SOP60100 D ON A.PONUMBER=D.PONUMBER AND A.ORD=D.ORD
                        where
                        POLNESTA <= '3' 
                        and a.ITEMNMBR='" . $producto . "';";
        $compras = DB::connection('gp')->select($sqlcompras);

        //sicS   
        $sqlsic = "SELECT  A.POPRequisitionNumber AS SOL, 
                    format(A.DOCDATE,'dd/MM/yyyy') as DOCDATE, 
                    format(A.REQDATE,'dd/MM/yyyy') AS FECHAREQ, 
                    dbo.DYN_FUNC_Workflow_Approval_Status(A.RequisitionStatus) AS ESTADO, 
                    dbo.DYN_FUNC_Workflow_Approval_Status(A.Workflow_Status) AS AUTORIZACION,  
                    cast(B.QTYORDER as varchar) AS CANTPEDIDA,
                    cast(C.QTYRECVD as varchar) AS CANTCOMPRADA,
                    cast((COALESCE(B.QTYORDER,0)-COALESCE(C.QTYRECVD,0))as varchar) AS NETO,
                    format(C.RQSTFFDATE,'dd/MM/yyyy') AS FECHAOC
                    FROM POP10200 a
                    inner join POP10210 b
                    on a.POPRequisitionNumber = b.POPRequisitionNumber
                    LEFT JOIN  SOP60100 C ON A.POPRequisitionNumber=C.SOPNUMBE AND B.ORD=C.LNITMSEQ
                    where ITEMNMBR = '" . $producto . "' and RequisitionStatus <= '3' and RequisitionLineStatus <= '3';";
        $sics = DB::connection('gp')->select($sqlsic);

        $consumos = DB::connection('gp')->select('SET NOCOUNT ON ; exec GR_INTRANET_ULTIMOS_CONSUMOS ?;', array($producto));
        $datosconsumos = DB::connection('gp')->select('SET NOCOUNT ON ; exec GR_INTRANET_DETALLE_LOTE_2 ?;', array($producto));
        //dd($datosconsumos);
        // dd($lote, $producto,$encabezado,$movimientos);
        //
        return view('stockgp.mrp_detalle', compact('encabezado','inventario','compras','sics','consumos','datosconsumos'));
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
