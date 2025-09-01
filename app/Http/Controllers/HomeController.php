<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Ocupacione;
use App\Models\Rack;
use App\Models\Ubicacione;
use App\Models\Lote;
use App\Models\Estado;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        /*$sql = 'select racks.numero as rack, count(ocupaciones.id_productos) as libre from  ocupaciones 
        left join ubicaciones on ocupaciones.id_ubicaciones=ubicaciones.id
        left join racks on ubicaciones.id_racks=racks.id
        group by racks.numero, ocupaciones.id_productos
        having ocupaciones.id_productos=1';*/
        /*$sql='select racks.numero as rack, count(ocupaciones.id_productos) as libre, x.tr as total from ocupaciones 
        left join ubicaciones on ocupaciones.id_ubicaciones=ubicaciones.id 
        left join racks on ubicaciones.id_racks=racks.id 
        left join (select a.id_racks as id, count(a.id_racks) as tr from ubicaciones a group by a.id_racks)as x on x.id=racks.id 
        group by racks.numero, ocupaciones.id_productos, x.tr 
        having ocupaciones.id_productos=1;';*/
        
        $sql_pt='select ubicaciones.deposito, racks.numero as rack, count(ocupaciones.id_productos) as libre, x.tr as total from ocupaciones 
        left join ubicaciones on ocupaciones.id_ubicaciones=ubicaciones.id 
        left join racks on ubicaciones.id_racks=racks.id 
        left join (select a.deposito as id_d,a.id_racks as id_r, count(a.id_racks) as tr from ubicaciones a group by a.deposito, a.id_racks)as x on x.id_r=ubicaciones.id_racks and x.id_d=ubicaciones.deposito
        where ubicaciones.deposito =1 
        group by ubicaciones.deposito,racks.numero, ocupaciones.id_productos, x.tr 
        having ocupaciones.id_productos=1;';
        $sql_mp='select ubicaciones.deposito, racks.numero as rack, count(ocupaciones.id_productos) as libre, x.tr as total from ocupaciones 
        left join ubicaciones on ocupaciones.id_ubicaciones=ubicaciones.id 
        left join racks on ubicaciones.id_racks=racks.id 
        left join (select a.deposito as id_d,a.id_racks as id_r, count(a.id_racks) as tr from ubicaciones a group by a.deposito, a.id_racks)as x on x.id_r=ubicaciones.id_racks and x.id_d=ubicaciones.deposito
        where ubicaciones.deposito =2 
        group by ubicaciones.deposito,racks.numero, ocupaciones.id_productos, x.tr 
        having ocupaciones.id_productos=1;';
        $librespts = collect(DB::select($sql_pt));
        $libresmps = collect(DB::select($sql_mp));
        $racks = Rack::all();
        $sql_gp='select * from pop00101;'; 
        $sql_ticket ='select 
        ost_ticket.number,
        ost_ticket.lastupdate,
        /*ost_ticket.dept_id,*/
        /*ost_ticket.user_id,*/
        
        ost_ticket__cdata.subject,
        ost_user.name,
       
       /*ost_user.id,*/
       ost_ticket__cdata.subject
     from ost_ticket 
     left join ost_user on ost_ticket.user_id=ost_user.id
     LEFT JOIN ost_ticket__cdata on ost_ticket.ticket_id=ost_ticket__cdata.ticket_id
     where ost_user.name="Pablo Lorenzo"
     Order by ost_ticket.number;';
        $ticketroles = collect(DB::connection('osticket')->select($sql_ticket));
        $gptest = collect(DB::connection('gp')->select($sql_gp));
        return view('home',compact('librespts', 'libresmps','racks','ticketroles','gptest'));
    }
}
