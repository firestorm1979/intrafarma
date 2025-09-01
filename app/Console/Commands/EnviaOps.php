<?php

namespace App\Console\Commands;

use App\Mail\EnviaOpsMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


class EnviaOps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'registered:ops';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia Ordenes de Produccion';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        //$sql_gp = 'select distinct a.MANUFACTUREORDER_I, b.ITEMNMBR, a.CHANGEDATE_I, cast(TIME1 as time) hora,cast(DATEADD(hour, -1, getdate()) as time) from MOP10213 a inner join WO010032 b on a.MANUFACTUREORDER_I = b.MANUFACTUREORDER_Iwhere mo_activity_reason_i = 31 and a.manufactureorderst_i = 3 and a.CHANGEDATE_I = CAST(getdate() as date )and cast(a.TIME1 as time) > cast(DATEADD(hour, -1, getdate()) as time) order by a.MANUFACTUREORDER_I asc;';
        $recipient =['pablo.lorenzo@farmagram.com.ar'];
        $sql_gp ='select distinct a.MANUFACTUREORDER_I OP, b.ITEMNMBR CODIGO, C.ITEMDESC descripcion, b.LOTNUMBR as LOTE from MOP10213 a inner join WO010032 b on a.MANUFACTUREORDER_I = b.MANUFACTUREORDER_I inner join IV00101 c on b.ITEMNMBR=C.ITEMNMBR where mo_activity_reason_i = 31 and a.manufactureorderst_i = 3 and a.CHANGEDATE_I = CAST(getdate() as date ) and cast(a.TIME1 as time) > cast(DATEADD(hour, -1, getdate()) as time) order by a.MANUFACTUREORDER_I asc;';
        $ordenes = collect(DB::connection('gp')->select($sql_gp));
       // if (count($ordenes)>0){
        
        Mail::to($recipient)->send(new EnviaOpsMail($ordenes));
       // }
    }
}
