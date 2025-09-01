{<html>
<head>
<link rel="stylesheet" type="text/css" href="../../Lib/estilos.css">
</head>


<?PHP

// time out ilimitado para que no corte
   set_time_limit(0);
  $dir_lib_bas = "C:/xampp/htdocs/www/lib/";
  $dir_img = "C:/xampp/htdocs/www/Img";
  if(!defined("INC_UTILS"))  { include("$dir_lib_bas/utils.dynamics.php");}
  if(!defined("INC_BUSCA_DYNAMICS"))  { include("$dir_lib_bas/busca.dynamics.inc.php");}
  if(!defined("INC_SALDOS"))  { include("Saldo_Arma.php");}

$CD1 = $_GET['CD1'];
$CD2 = $_GET['CD2'];
$CH1 = $_GET['CH1'];
$CH2 = $_GET['CH2'];
if ($CH1 == "") {
if ($CD1 != "") {
$CH1 = $CD1;
$CH2 = $CD2;
}
elseif ($CD1 == "" and $CD2 == "")  {
$CD1 = "10000";
$CD2 = "000";
$CH1 = "99999";
$CH2 = "999";	
}
}

$AMDI = $_GET['MAAI'];
$AMDF = $_GET['MAAF'];

if ($_GET['EMPRESA'] == "GR") {
$Empresa = "Gerardo Ramon y Cia";
$c_odbc  = connect_SqlServer();
$MC = 10;
}
if ($_GET['EMPRESA'] == "MO") {
$Empresa = "Moromio";
$c_odbc  = connect_SqlServer_PRD02();
$MC = 7;
}

if (isset($_GET['SINCO']) and $_GET['SINCO'] == 'S') {$Sinco = 'S';}
else {$Sinco = 'N';}
if (isset($_GET['EXCEL'])) {$Excel = 'S';}
else {$Excel = 'N';}

$CD = $CD1."-".$CD2;
$CH = $CH1."-".$CH2;
$AI = substr($AMDI, 0, 4);
$AF = substr($AMDF, 0, 4);
$MI = substr($AMDI, 5, 2);
$MF = substr($AMDF, 5, 2);
$AMI = $AI.$MI;
$AMF = $AF.$MF;

if ($Excel == 'S') {
$filename = "Movimientos_Contables_" . date('Ymd') . "_" . time() . ".xls";
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Type: application/vnd.ms-excel");
}

$SySLink = "<a href='http://gr-p/www/MSDynamicsGP/Contabilidad/SumMov_Arma.php?CD1=$CD1&CD2=$CD2&CH1=$CH1&CH2=$CH2&MAAI=$AMDI&MAAF=$AMDF&SINCO=$Sinco&EMPRESA=$_GET[EMPRESA]&EXCEL=S' target='_blank'>";

echo "<body><p>";
echo "<table width=1000 border = '2' cellpadding='1' cellspacing='0' align='center'>
<tr><td width='260'>$Empresa</td>
<td width='700'><div align='center'><font face='Arial' size='3'><strong><u>MOVIMIENTOS CONTABLES<br></u></strong></font>
<font face='Verdana' size='2'>Rango de cuentas: $CD a $CH, entre meses $MI/$AI - $MF/$AF</font></div></td>";
if ($Excel != 'S') {
        echo "<td width='150' align='center' class='MiBor3'><img src='../../Img/LogoExcel.jpg' border='0' width=25 height=25><font size='2' face='Arial' color='blue'>
        $SySLink Exportar a Excel</a></font></td>";
         } else {echo "<td></td>";}
echo "</tr>";
echo "<tr><td colspan='3' align='center' height=25><font size='2' face='Arial' color='red'>En cuentas patrimoniales No se incluyen movimientos de cierre y apertura de ejercicio</font></td></tr>";
if ($AMI > $AMF) {
echo "<tr><td height='60' align='center' colspan='4'><font face='Verdana' size='2' color='red'>ATENCION: intervalo de meses incongruentes</font></td></tr>";
}
if ($CD > $CH) {
echo "<tr><td height='60' align='center' colspan='4'><font face='Verdana' size='2' color='red'>ATENCION: rango de cuentas incongruentes</font></td></tr>";
}
echo "</table> \n";


$Hoy = date("YmdHis");
$Ip = substr($_SERVER["REMOTE_ADDR"],7,3);
$TablaDD = "II_Work_Diario_".$Ip;
$TablaS = "II_SaldoContable_".$Ip."_".$Hoy;
//$TablaS = "II_SaldoContable_".$Ip."Prueba";

$XAI = 0;
$XMI = 0;
Periodos($MC,$AI,$MI,$AF,$MF,$PAI,$PMI,$PAF,$PMF,$XAI,$XMI);
//echo "2 ".$PAI."/".$PMI." - ".$PAF."/".$PMF." - ".$XAI."/".$XMI."<br>";
$PAMI = $PAI.$PMI;
$PAMF = $PAF.$PMF;
$XAMI = $XAI.$XMI;

// ultimo dia saldo de arranque
$FecSal = date("Ymd",strtotime($XAI.$XMI.'01'));


// TIPO_REG
// 1S=saldo
// 2M=movimiento
// 3M=movimiento en asientos de cierre ejercicio
// 4M=movimiento no contabilizado

//Series (in GL tables):
//1 – Todas
//2 – Financial
//3 – Sales
//4 – Purchasing
//5 – Inventory
//6 – Payroll – USA
//7 – Project
//10 – 3rd Party

$result = true;
$arch=odbc_exec($c_odbc,"IF EXISTS (SELECT * FROM sysobjects WHERE type = 'U' AND name = '$TablaDD')
        BEGIN
        DELETE FROM $TablaDD
        END
        ELSE
        BEGIN
        CREATE TABLE $TablaDD (
        [TIPO_REG] [char](2) NOT NULL,
        [INDICE] [int] NOT NULL,
        [CUENTA] [char](129) NOT NULL,
        [AÑO] [smallint] NOT NULL,
        [PERIODO] [smallint] NOT NULL,
        [AÑOMES]  [numeric](6, 0) NOT NULL,
        [FECHA] [datetime] NOT NULL,
        [MOVIMIENTO] [numeric](19, 5) NOT NULL,
        [NAS] [int] NOT NULL,
        [ORIGEN] [char](11) NOT NULL,
        [REFERENCE] [char](31) NOT NULL,
        [DSCRIPTN] [char](31) NOT NULL,
        [TRXSORCE] [char](13) NOT NULL,
        [USUARIO] [char](15) NOT NULL,
        [USU_ULT] [char](15) NOT NULL,
        [USU_EDT] [datetime] NOT NULL,
        [LOTE_CONT] [char](15) NOT NULL,
        [SERIES] [smallint] NOT NULL,
        [NUMERO_D] [char](21) NOT NULL,
        [CODIGO_3RO] [char](31) NOT NULL,
        [NOMBRE_3RO] [char](65) NOT NULL,
        [ORDOCNUM] [char](21) NOT NULL,
        [ORTRXSRC] [char](13) NOT NULL,
        [OrigSeqNum] [int] NOT NULL,
        [SEQNUMBR] [int] NOT NULL,
        [MONEDA_TRX] [char](15) NOT NULL,
        [MOV_DEBITO] [numeric](19, 5) NOT NULL,
        [MOV_CREDITO] [numeric](19, 5) NOT NULL,
        [MONEDA_DEBITO] [numeric](19, 5) NOT NULL,
        [MONEDA_CREDITO] [numeric](19, 5) NOT NULL,
        [CONTRACUENTA] [int] NOT NULL,
        [CCMAS] [char](1) NOT NULL,
        [DOC_ORIG] [char](30) NOT NULL,
        [DOCMAS] [char](1) NOT NULL,
        [DEX_ROW_ID] [int] IDENTITY(1,1) NOT NULL)
        END") or die(ODBC_error());
if (!$arch){ echo odbc_error();
        $result = false; }


// --- Obtiene saldo ------------------------------------------------------------------------

$FecSalI = $AI."-".$MI."-01";
$AñoMesSalI = date('Ym', strtotime("$FecSalI - 1 month"));
$FecSalI = date('d/m/Y', strtotime("$FecSalI - 1 day"));
$Saldo = 0;
$Nas = 0;
$Cero = 0;
$Saldo = 0;
$Blanco = "s/d";

// Busqueda de cuentas desde el plan de cuentas
$result = true;
$arch=odbc_exec($c_odbc,"SELECT * FROM GL00105
where ACTNUMST BETWEEN '$CD' AND '$CH'
order by ACTNUMST") or die(ODBC_error());

if (!$arch){ echo odbc_error();
        $result = false; }
        if ($result){
        while($reg=odbc_fetch_array($arch)){
        
$Indice = $reg['ACTINDX'];
$Cuenta = $reg['ACTNUMST'];
// Funcion para armar saldos
FnSaldo($c_odbc,$MC,$TablaS,$Indice);

// Busca Saldo
$resS = true;
$archS=odbc_exec($c_odbc,"SELECT * FROM $TablaS
where EJERC = '$XAI' and PERIODO = '$XMI' and ACTINDX = '$Indice'") or die(ODBC_error());

if (!$archS){ echo odbc_error();
        $resS = false; }
        if ($resS){
        while($regS=odbc_fetch_array($archS)){
// Monto al cierre, solo tomar cuando son cuentas de resultado.
if ($regS['PSTNGTYP'] == '1') {
$Saldo = $regS['SALDO'] -  $regS['MONTOCIERRE'];
}
else {
$Saldo = $regS['SALDO'];
}
}
}
// reserva saldo inicial
$SI = $Saldo;
$Referencia = "Saldo inicial";
agrega($c_odbc,$TablaDD,'1S',$Indice,$Cuenta,$XAI,$XMI,$AñoMesSalI,$FecSalI,$Saldo,$Nas,$Blanco,$Referencia,$Referencia,$Blanco,$Blanco,$Blanco,$FecSalI,$Blanco,$Cero,$Blanco,$Blanco,$Blanco,$Cero,$Cero,'PESO',
$Cero,$Blanco,$Blanco,$Cero,'N',$Blanco,'N');

// Si es cuenta de resultados Busca monto contabilizado en asiento de cierre de ejercicio de los meses seleccionados
$Saldo = 0;
$resS = true;
$archS=odbc_exec($c_odbc,"SELECT * FROM $TablaS
where PSTNGTYP = 1 and EJERC >= '$XAI' and ACTINDX = '$Indice'") or die(ODBC_error());

if (!$archS){ echo odbc_error();
        $resS = false; }
        if ($resS){
        while($regS=odbc_fetch_array($archS)){
$EPer_actual = $regS['EJERC'] * 100 + $regS['PERIODO'];
if ($EPer_actual >= $PAMI and $EPer_actual <= $PAMF) {
$Saldo = $Saldo - $regS['MONTOCIERRE'];
}

}
}

if ($Saldo !== 0) {
$Referencia = "Asiento Cierre Ejercicio";
agrega($c_odbc,$TablaDD,'3M',$Indice,$Cuenta,$XAI,$XMI,$AñoMesSalI,$FecSalI,$Saldo,$Nas,$Blanco,$Referencia,$Referencia,$Blanco,$Blanco,$Blanco,$FecSalI,$Blanco,$Cero,$Blanco,$Blanco,$Blanco,$Cero,$Cero,'PESO',
$Cero,$Blanco,$Blanco,$Cero,'N',$Blanco,'N');
}

}
}  // Fin lectura GL00105

// Borra tabla de armado de saldos
$result = true;
$arch=odbc_exec($c_odbc,"IF EXISTS (SELECT * FROM sysobjects WHERE name = '$TablaS')
        BEGIN
         DROP TABLE $TablaS
        END") or die(ODBC_error());
if (!$arch){ echo odbc_error();
  $result = false; }

// -- MOVIMIENTOS -------------------------------------------------------------------------------------------------------------------------------

$Monto = 0;
$Nas = 0;

// ATENCION, NO SE INCLUYEN LOS REGISTROS SOURCDOC = 'BBF' p 'P/L' son los asientos de cierre, porque no hay asiento de apertura.
// se quito la restriccion BBF en caso de incorporarla = SOURCDOC not in ('BBF','P/L')
$result = true;
$arch=odbc_exec($c_odbc,"SELECT a.ACTINDX, ACTNUMST, OPENYEAR, JRNENTRY, SOURCDOC, REFRENCE, DSCRIPTN, TRXDATE, TRXSORCE,
LASTUSER, LSTDTEDT, USWHPSTD, ORGNTSRC, SERIES, ORCTRNUM, ORMSTRID, ORMSTRNM, ORDOCNUM, ORTRXSRC, OrigSeqNum, SEQNUMBR,
CURNCYID, PERIODID, DEBITAMT, CRDTAMNT, ORDBTAMT, ORCRDAMT, VOIDED FROM GL20000 as a
inner join GL00105 as b on a.ACTINDX = b.ACTINDX
where ACTNUMST BETWEEN '$CD' AND '$CH' and SOURCDOC not in ('BBF', 'P/L') and VOIDED = 0
union all
SELECT a.ACTINDX, ACTNUMST, HSTYEAR as OPENYEAR, JRNENTRY, SOURCDOC, REFRENCE, DSCRIPTN, TRXDATE, TRXSORCE,
LASTUSER, LSTDTEDT, USWHPSTD, ORGNTSRC, SERIES, ORCTRNUM, ORMSTRID, ORMSTRNM, ORDOCNUM, ORTRXSRC, OrigSeqNum, SEQNUMBR,
CURNCYID, PERIODID, DEBITAMT, CRDTAMNT, ORDBTAMT, ORCRDAMT, VOIDED FROM GL30000 as a
inner join GL00105 as b on a.ACTINDX = b.ACTINDX
where ACTNUMST BETWEEN '$CD' AND '$CH' and  SOURCDOC not in ('BBF', 'P/L') and VOIDED = 0
order by ACTINDX, OPENYEAR, PERIODID") or die(ODBC_error());

if (!$arch){ echo odbc_error();
        $result = false; }
        if ($result){
        while($reg=odbc_fetch_array($arch)){

$CDig = strlen($reg['PERIODID']);
if ($CDig == 1) {
$AMPer = $reg['OPENYEAR']."0".$reg['PERIODID'];
}
else {
$AMPer = $reg['OPENYEAR'].$reg['PERIODID'];
}
if ($AMPer >= $PAMI and $AMPer <= $PAMF) {

$Indice = $reg['ACTINDX'];
$Cuenta = $reg['ACTNUMST'];
$Movimiento = $reg['DEBITAMT'] - $reg['CRDTAMNT'];
$Mov_Moneda = $reg['ORDBTAMT'] - $reg['ORCRDAMT'];

// si es asiento de cierre de ejercicio deja parciales en cero para que no sume. Y le agrega el monto a comentario
// NO opera. No estamos incluyendo info de cierre
/*
if ($reg['SOURCDOC'] == 'BBF') {
$reg['DEBITAMT'] = $reg['DEBITAMT']." Asiento de CIERRE por ".$Movimiento;
$Movimiento = 0;
$Mov_Moneda = 0;
}
*/
$AA = 0;
$M = 0;
AñoMes($reg['OPENYEAR'],$reg['PERIODID'],$MC,$AA,$M);
$FecDoc = substr($reg['TRXDATE'], 0, 10);
$FecDoc = date("d/m/Y", strtotime($FecDoc));
$DocFuente = trim($reg['ORDOCNUM']);
$TrsFuente = trim($reg['ORTRXSRC']);

// Busca descripcion movmiento fuente

// Busca contra cuenta, si hay una sola o mas de una
$NAS = $reg['JRNENTRY'];
$SeqN = $reg['SEQNUMBR'];
if ($Movimiento > 0) {
$TipM = 'D';
}
else {$TipM = 'C';}

$Tot_CC = 0;
$Contra_CC = 0;
$Mas_CC = "N";
$res_CC = true;

$arch_CC=odbc_exec($c_odbc,"SELECT a.ACTINDX, ACTNUMST, JRNENTRY, DSCRIPTN, SEQNUMBR, DEBITAMT, CRDTAMNT, b.ACTINDX FROM GL20000 as a
inner join GL00105 as b on a.ACTINDX = b.ACTINDX
where JRNENTRY = '$NAS' and  SEQNUMBR <> '$SeqN' and DSCRIPTN <> 'Impuesto'
union all
SELECT a.ACTINDX, ACTNUMST, JRNENTRY, DSCRIPTN, SEQNUMBR, DEBITAMT, CRDTAMNT, b.ACTINDX FROM GL30000 as a
inner join GL00105 as b on a.ACTINDX = b.ACTINDX
where JRNENTRY = '$NAS' and  SEQNUMBR <> '$SeqN' and DSCRIPTN <> 'Impuesto'") or die(ODBC_error());
if (!$arch_CC){ echo odbc_error();
        $res_CC = false; }
        if ($res_CC){
        while($reg_CC=odbc_fetch_array($arch_CC)){

/*
EnPartes($reg_CC['DEBITAMT'],2,$Resultante);
$reg_CC['DEBITAMT'] = $Resultante;
EnPartes($reg_CC['CRDTAMNT'],2,$Resultante);
$reg_CC['CRDTAMNT'] = $Resultante;
*/
if ($reg_CC['DEBITAMT'] > 0 and $TipM == 'C' or $reg_CC['CRDTAMNT'] > 0 and $TipM == 'D' ) {
$Tot_CC = $Tot_CC + 1;
if ($Tot_CC == 1) {
$Contra_CC = $reg_CC['ACTINDX'];
}
else {
$Mas_CC = "S";
}
}
        }
        }



// ACCESO A MODULOS
$DocO = "N";
$Mas_D = "N";
$CantR = 0;
$DocAnt = "";
$res_M = true;
// -- Recepciones
if (trim($reg['SOURCDOC']) == "RECVG" or trim($reg['SOURCDOC']) == "POIVC") {
$arch_M=odbc_exec($c_odbc,"SELECT * FROM POP30310
where POPRCTNM = '$DocFuente'") or die(ODBC_error());
if (!$arch_M){ echo odbc_error();
        $res_M = false; }
        if ($res_M){
        while($reg_M=odbc_fetch_array($arch_M)){
$CantR = $CantR + 1;
if ($CantR == 1) {
$DocO = $reg_M['PONUMBER'];
//echo $DocFuente." / ".$DocO;
}
elseif ($DocO != $DocAnt){
//echo $DocO." / ".$DocAnt."<br>";
$Mas_D = "S";
}
$DocAnt = $reg_M['PONUMBER'];
        }
        }
}
// -- Proveedores, facturacion
if (trim($reg['SOURCDOC']) == "PMTRX") {
$arch_M=odbc_exec($c_odbc,"SELECT * FROM PM30200
where TRXSORCE = '$TrsFuente'") or die(ODBC_error());
if (!$arch_M){ echo odbc_error();
        $res_M = false; }
        if ($res_M){
        while($reg_M=odbc_fetch_array($arch_M)){
$CantR = $CantR + 1;
if ($CantR == 1) {
$DocO = $reg_M['PORDNMBR'];
//echo $DocFuente." / ".$DocO;
}
elseif ($DocO != $DocAnt){
//echo $DocO." / ".$DocAnt."<br>";
$Mas_D = "S";
}
$DocAnt = $reg_M['PORDNMBR'];
        }
        }
}

// --------------

agrega($c_odbc,$TablaDD,'2M',$Indice,$Cuenta,$reg['OPENYEAR'],$reg['PERIODID'],$AA.$M,$FecDoc,$Movimiento,$reg['JRNENTRY'],$reg['SOURCDOC'],$reg['REFRENCE'],$reg['DSCRIPTN'],$reg['TRXSORCE'],$reg['USWHPSTD'],
$reg['LASTUSER'],$FecDoc,$reg['ORGNTSRC'],$reg['SERIES'],$reg['ORCTRNUM'],$reg['ORMSTRID'],$reg['ORMSTRNM'],$reg['OrigSeqNum'],$reg['SEQNUMBR'],$reg['CURNCYID'],$Mov_Moneda,$reg['ORDOCNUM'],
$reg['ORTRXSRC'],$Contra_CC,$Mas_CC,$DocO,$Mas_D);

}
}
}

// ----------------------------------------------------------------------------------------------------------------
// -- MOVIMIENTOS NO CONTABILIZADOS

$MovS = 0;
$MovP = 0;
$Decimales = 2;

if ($Sinco == 'S') {

// buscara en GL10000 PSTGSTUS = '1' que son los registro en work. O en su defecto machear por si con el GL10001
$res = true;
$arch=odbc_exec($c_odbc,"SELECT * FROM GL10000 as a
inner join GL10001 as b on a.BACHNUMB = b.BACHNUMB and a.JRNENTRY = b.JRNENTRY
INNER JOIN GL00105 as C on b.ACTINDX = c.ACTINDX
where c.ACTNUMST BETWEEN '$CD' AND '$CH'
order by c.ACTNUMST,a.TRXDATE") or die(ODBC_error());
if (!$arch){ echo odbc_error();
        $res = false; }
        if ($res){
        while($reg=odbc_fetch_array($arch)){

$AAM_Sin = date("Ym", strtotime($reg['TRXDATE']));

if ($AAM_Sin >= $AMI and $AAM_Sin <= $AMF) {
$Indice = $reg['ACTINDX'];
$Cuenta = $reg['ACTNUMST'];

$MovS = $reg['DEBITAMT'] - $reg['CRDTAMNT'];
$Resultante = 0;
EnPartes($MovS,$Decimales,$Resultante);
$MovS = $Resultante;
$Mov_Moneda = $reg['ORDBTAMT'] - $reg['ORCRDAMT'];

$AA = substr($reg['TRXDATE'], 0, 4);
$M = substr($reg['TRXDATE'], 5, 2);
PerMes($AA,$M,$MC,$PAA,$PM);
$FecDoc = substr($reg['TRXDATE'], 0, 10);
$FecDoc = date("d/m/Y", strtotime($FecDoc));
$DocFuente = trim($reg['ORDOCNUM']);

$DocO = "N";
$Mas_D = "N";
// ACCESO A MODULOS NO CONTABILIZADOS
// -- Recepciones
if (trim($reg['SOURCDOC']) == "RECVG" or trim($reg['SOURCDOC']) == "POIVC") {
$CantR = 0;
$DocAnt = "";
$res_M = true;
$arch_M=odbc_exec($c_odbc,"SELECT * FROM POP10310
where POPRCTNM = '$DocFuente'") or die(ODBC_error());
if (!$arch_M){ echo odbc_error();
        $res_M = false; }
        if ($res_M){
        while($reg_M=odbc_fetch_array($arch_M)){
$CantR = $CantR + 1;
if ($CantR == 1) {
$DocO = $reg_M['PONUMBER'];
//echo $DocFuente." / ".$DocO;
}
elseif ($DocO != $DocAnt){
//echo $DocO." / ".$DocAnt."<br>";
$Mas_D = "S";
}
$DocAnt = $reg_M['PONUMBER'];
        }
        }
}

agrega($c_odbc,$TablaDD,'4M',$Indice,$Cuenta,$PAA,$PM,$AA.$M,$FecDoc,$MovS,$reg['JRNENTRY'],$reg['SOURCDOC'],$reg['REFRENCE'],$reg['DSCRIPTN'],$reg['TRXSORCE'],$reg['USWHPSTD'],
$reg['LASTUSER'],$FecDoc,$reg['DTAControlNum'],$reg['SERIES'],$reg['ORCTRNUM'],$reg['ORMSTRID'],$reg['ORMSTRNM'],$reg['OrigSeqNum'],$reg['OrigSeqNum'],$reg['CURNCYID'],$Mov_Moneda,$reg['ORDOCNUM'],
$reg['ORTRXSRC'],$Cero,'N',$DocO,$Mas_D);
}

}
}
}


// ----------------------------------------------------------------------------------------------------------------
// -- LISTADO
// --- FORMATOS --------------------
$colBg="#FEFBD3";
$colLin="";
$Encab="height='30' class='miBor2'";
$Encabx="height='18' class='miBor2'";
$EncabT="class='miBor1T'";
$EncabTL="class='miBor1TL'";
$DetCod0 = "<td  bgcolor='#FFFFFF'><div align='right'><font face='Courier' size='1'><b>";
$DetCod1 = "<td  bgcolor='#D7D7D7'><font face='Courier' size='2'><b>";
$FinCod = "</b></font></div></td>";
$DetRestoL0 = "<td bgcolor='#FFFFFF' class='miBor1L'><font face='Verdana' size='1'>";
$DetRestoR0 = "<td bgcolor='#FFFFFF' class='miBor1L'><div align='right'><font face='Verdana' size='1'>";
$DetRestoC0 = "<td bgcolor='#FFFFFF' class='miBor1L'><div align='center'><font face='Verdana' size='1'>";
$DetRestoL1 = "<td bgcolor='#F4EEED' class='miBor1L'><font face='Verdana' size='1'>";
$DetRestoR1 = "<td bgcolor='#F4EEED' class='miBor1L'><div align='right'><font face='Verdana' size='1'>";
$DetRestoC1 = "<td bgcolor='#F4EEED' class='miBor1L'><div align='center'><font face='Verdana' size='1'>";
$DetRestoL2 = "<td bgcolor='#EFF3F1' class='miBor1L'><font face='Verdana' size='1'>";
$DetRestoR2 = "<td bgcolor='#EFF3F1' class='miBor1L'><div align='right'><font face='Verdana' size='1'>";
$DetRestoC2 = "<td bgcolor='#EFF3F1' class='miBor1L'><div align='center'><font face='Verdana' size='1'>";


   #FEF3E6
$FinRestodiv = "</font></div></td>";
$FinResto = "</font></td>";
$Link1 = "<font color='blue'><i><u>";
$FinLink = "</i></u></font>";

$TSalD = 0;
$TSalC = 0;
$TMovD = 0;
$TMovC = 0;
$TMovS = 0;
$TMovP = 0;
$Saldo = 0;
$SalModulo = array();

// *******************************************************************************

echo "<br>";
echo "<table border = '0' cellpadding='2' cellspacing='0' width=2000 align='center' class='miBor3' bgcolor='#F9FCD3'>\n";
echo "<tr>
<td align='center' $Encab><font size='1' face='Verdana'>IND</font></td>
<td align='center' $Encab><font size='1' face='Verdana'>CUENTA</font></td>
<td align='center' $Encab><font size='1' face='Verdana'>DESCRIPCION</font></td>
<td align='center' $Encab><font size='1' face='Verdana'>MES Y AÑO</font></td>
<td align='center' $Encab><font size='1' face='Verdana'>FECHA</font></td>
<td align='center' $Encab><font size='1' face='Verdana'>REFERENCIA</font></td>
<td align='center' $Encab><font size='1' face='Verdana'>COMENTARIO</font></td>
<td align='center' $Encab><font size='1' face='Verdana'>MODULO<br>ORIGEN</font></td>
<td align='center' $Encab><font size='1' face='Verdana'>ASIENTO</font></td>
<td align='center' $Encab><font size='1' face='Verdana'>LOTE<br>CONTABILIDAD</font></td>
<td align='center' $Encab><font size='1' face='Verdana'>TRANSACCION</font></td>
<td align='center' $Encab><font size='1' face='Verdana'>DOCUMENTO</font></td>
<td align='center' $Encab><font size='1' face='Verdana'>INTERVINIENTE</font></td>
<td align='center' $Encab><font size='1' face='Verdana'>DEBITO</font></td>
<td align='center' $Encab><font size='1' face='Verdana'>CREDITO</font></td>
<td align='center' $Encab><font size='1' face='Verdana'>SALDO</font></td>
<td align='center' $Encab><font size='1' face='Verdana'>MONEDA<br>ORIGEN</font></td>
<td align='center' $Encab><font size='1' face='Verdana'>MONTO<br>ORIGEN</font></td>
<td align='center' $Encab><font size='1' face='Verdana'>CONTRA<br>CUENTA</font></td>
<td align='center' $Encab><font size='1' face='Verdana'>MAS</font></td>
<td align='center' $Encab><font size='1' face='Verdana'>DOCUM.<br>BASE</font></td>
<td align='center' $Encab><font size='1' face='Verdana'>MAS</font></td>
<td align='center' $Encab><font size='1' face='Verdana'>USUARIO</font></td>
</tr>\n";

$BkColor = 0;
$Corte_CTR = "";
$Corte_CTA = "";
$Total_MovD = 0;
$Total_MovC = 0;
$result = true;
$arch=odbc_exec($c_odbc,"SELECT * FROM $TablaDD as a inner join GL00100 as b
on a.INDICE = b.ACTINDX
order by  CUENTA, TIPO_REG, AÑOMES, FECHA") or die(ODBC_error());
if (!$arch){ echo odbc_error();
        $result = false; }
        if ($result){
        while($reg=odbc_fetch_array($arch)){

$Indice = rtrim($reg['INDICE']);
$Cuenta = rtrim($reg['CUENTA']);
$CDes = $reg['ACTDESCR'];
$SeqDim = $reg['SEQNUMBR'];

if ($Cuenta != $Corte_CTA) {
if ($Corte_CTA != "") {
SALDOFINAL($Total_MovD,$Total_MovC,$SI,$DetRestoR,$FinResto,$DetCod1,$MF,$AF);
}

echo "<tr>";
echo $DetCod1.$Indice.$FinResto;
echo $DetCod1.$Cuenta."</b>".$FinResto;
echo $DetCod1.$CDes."</b>".$FinResto;
echo "</tr>";
$Total_MovD = 0;
$Total_MovC = 0;
$Saldo = 0;
}
$Corte_CTA = $Cuenta;

$CTR = $Cuenta.$reg['TIPO_REG'];
if ($Corte_CTR != $CTR and $reg['TIPO_REG'] == "4M") {
echo "<tr><td colspan='8'><font size='2' face='Verdana'>Movimientos NO CONTABILIZADOS</font></td></tr>";
}

// Codigo y Nombre contra cuenta
$ContraCuenta = rtrim($reg['CONTRACUENTA']);
$resC = true;
$archC=odbc_exec($c_odbc,"SELECT * FROM GL00100
where ACTINDX = '$ContraCuenta'") or die(ODBC_error());
if (!$archC){ echo odbc_error();
        $resC = false; }
        if ($resC){
        while($regC=odbc_fetch_array($archC)){
$ContraCuenta = $regC['ACTNUMBR_1']."-".$regC['ACTNUMBR_2']." / ".$regC['ACTDESCR'];
}
}

// fuente origen
BFnSrc($reg['ORIGEN'],$c_odbc,$DFnSrc);
$FnSrc = $reg['ORIGEN']." - ".$DFnSrc;
$FnSrcSD = trim($reg['ORIGEN']);

$Corte_CTR = $CTR;

$Mas_CC = rtrim($reg['CCMAS']);

$Año = substr($reg['AÑOMES'], 0, 4);
$Mes = substr($reg['AÑOMES'], 4, 2);
$Fecha = date("d/m/Y", strtotime($reg['FECHA']));
$Ejerc =  $reg['AÑO'];
$Referencia =  $reg['REFERENCE'];
$Comentario = $reg['DSCRIPTN'];
$Modulo =  $FnSrc;
$NAS =  $reg['NAS'];
$Transac =  $reg['ORTRXSRC'];
$LoteC =  $reg['TRXSORCE'];
$Documento =  $reg['ORDOCNUM'];
$Tercero =  $reg['CODIGO_3RO']."-".$reg['NOMBRE_3RO'];
$Moneda_Ori =  trim($reg['MONEDA_TRX']);
$DocO =  $reg['DOC_ORIG'];
$Doc_M = rtrim($reg['DOCMAS']);
$Usuario =  $reg['USUARIO'];

$MovD = $reg['MOV_DEBITO'];
$MovC = $reg['MOV_CREDITO'];
// Acumula en matriz por cuenta
if ($reg['TIPO_REG'] == "2M" or $reg['TIPO_REG'] == "4M") {
if (array_key_exists($FnSrcSD, $SalModulo)) {
$SalModulo[$FnSrcSD] = $SalModulo[$FnSrcSD] + $MovD - $MovC;
}
else {
$SalModulo[$FnSrcSD] =  $MovD - $MovC;
}
}
$Total_MovD = $Total_MovD + $reg['MOV_DEBITO'];
$Total_MovC = $Total_MovC + $reg['MOV_CREDITO'];
$Saldo = $Total_MovD - $Total_MovC;

$Monto_Ori = "";
if ($Moneda_Ori != 'PESO') {
if ($reg['MONEDA_DEBITO'] > 0) {
$Monto_Ori = $reg['MONEDA_DEBITO'];
}
else {
$Monto_Ori = $reg['MONEDA_CREDITO'];
}
}


FFORM($Monto_Ori,2,$Monto_Ori);
FFORM($MovD,2,$MovD);
FFORM($MovC,2,$MovC);
FFORM($Saldo,2,$Saldo);

if ($BkColor == 0) {
$DetRestoL = $DetRestoL0;
$DetRestoC = $DetRestoC0;
$DetRestoR = $DetRestoR0;
$BkColor = 1;
}
else {
$DetRestoL = $DetRestoL1;
$DetRestoC = $DetRestoC1;
$DetRestoR = $DetRestoR1;
$BkColor = 0;
}

echo "<tr>";
if ($reg['TIPO_REG'] == "1S") {
echo $DetRestoL2.$Indice.$FinResto;
echo $DetRestoL2.$Cuenta.$FinResto;
echo $DetRestoL2.$CDes.$FinResto;
echo $DetRestoR2.$Mes."/".$Año.$FinResto;
echo $DetRestoR2.$Fecha.$FinResto;
echo $DetRestoL2."<b>".$Referencia."</b>".$FinResto;
echo $DetRestoL2.$FinResto;
echo $DetRestoR2.$FinResto;
echo $DetRestoC2.$FinResto;
echo $DetRestoL2.$FinResto;
echo $DetRestoR2.$FinResto;
echo $DetRestoR2.$FinResto;
echo $DetRestoL2.$FinResto;
echo $DetRestoR2."<font color='blue'><b>".$MovD."</b></font>".$FinResto;
echo $DetRestoR2."<font color='blue'><b>".$MovC."</b></font>".$FinResto;
echo $DetRestoR2.$FinResto;
echo $DetRestoR2.$FinResto;
echo $DetRestoR2.$FinResto;
echo $DetRestoR2.$FinResto;
echo $DetRestoR2.$FinResto;
echo $DetRestoR2.$FinResto;
echo $DetRestoR2.$FinResto;
echo $DetRestoR2.$FinResto;
}
else {
if ($reg['TIPO_REG'] == "2M" or $reg['TIPO_REG'] == "3M") {
$Cont = 'S';
}
else {
$Cont = 'N';
}
$SySLink = "<a href='http://gr-p/www/MSDynamicsGP/Contabilidad/SumMov_Asiento.php?NAS=$NAS&CONT=$Cont&EMPRESA=$_GET[EMPRESA]&MODULO=$FnSrcSD&EJERC=$Ejerc' target='_blank' class='miLink8'>";

if ($FnSrcSD == "RECVG" or $FnSrcSD == "POIVC") {
$DetLink = "<a href='http://gr-p/www/MSDynamicsGP/Compras/OC_recep.php?RECEPT=$Documento&ASIENTO=$NAS&SECUENCIA=$SeqDim' target='_blank' class='miLink8'>$Documento</a>";
}
else if ($FnSrcSD == "PMTRX") {
	//  or $FnSrcSD == "PMVVR"
$DetLink = "<a href='http://gr-p/www/MSDynamicsGP/Compras/OC_PM.php?DOCUM=$Documento&ASIENTO=$NAS&MOVIM=$Transac&SECUENCIA=$SeqDim' target='_blank' class='miLink8'>$Documento</a>";	
}
else if ($FnSrcSD == "PMPAY") {
$DetLink = "<a href='http://gr-p/www/MSDynamicsGP/Compras/OC_PAY.php?DOCUM=$Documento&ASIENTO=$NAS&MOVIM=$Transac&COMEN=$Comentario&SECUENCIA=$SeqDim' target='_blank' class='miLink8'>$Documento</a>";	
}
else if ($FnSrcSD == "IVADJ") {
$DetLink = "<a href='http://gr-p/www/MSDynamicsGP/Stock/MStock_Detalle.php?DOCUM=$Documento&MOVIM=$Transac&ASIENTO=$NAS&SECUENCIA=$SeqDim' target='_blank' class='miLink8'>$Documento</a>";	
}
else {
$DetLink = $Documento;
}


if ($reg['TIPO_REG'] == "2M" or $reg['TIPO_REG'] == "4M") {
echo $DetRestoL.$Indice.$FinResto;
echo $DetRestoL.$Cuenta.$FinResto;
echo $DetRestoL.$CDes.$FinResto;
echo $DetRestoR.$Mes."/".$Año.$FinResto;
echo $DetRestoR.$Fecha.$FinResto;
echo $DetRestoL.$Referencia.$FinResto;
echo $DetRestoL.$Comentario.$FinResto;
echo $DetRestoR.$Modulo.$FinResto;
echo $DetRestoC.$SySLink.$NAS."</a>".$FinResto;
echo $DetRestoL.$LoteC.$FinResto;
echo $DetRestoR.$Transac.$FinResto;
echo $DetRestoR.$DetLink.$FinResto;
echo $DetRestoL.$Tercero.$FinResto;
echo $DetRestoR."<font color='blue'>".$MovD."</font>".$FinResto;
echo $DetRestoR."<font color='blue'>".$MovC."</font>".$FinResto;
echo $DetRestoR.$Saldo.$FinResto;
echo $DetRestoR.$Moneda_Ori.$FinResto;
echo $DetRestoR.$Monto_Ori.$FinResto;
echo $DetRestoL.$ContraCuenta.$FinResto;
echo $DetRestoC.$Mas_CC.$FinResto;
echo $DetRestoR.$DocO.$FinResto;
echo $DetRestoC.$Doc_M.$FinResto;
echo $DetRestoR.$Usuario.$FinResto;
}
if ($reg['TIPO_REG'] == "3M") {
echo $DetRestoL.$Indice.$FinResto;
echo $DetRestoL.$Cuenta.$FinResto;
echo $DetRestoL.$CDes.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoL."<b>".$Referencia."</b>".$FinResto;
echo $DetRestoL.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoC.$FinResto;
echo $DetRestoL.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoL.$FinResto;
echo $DetRestoR."<font color='brown'>".$MovD."</font>".$FinResto;
echo $DetRestoR."<font color='brown'>".$MovC."</font>".$FinResto;
echo $DetRestoR.$Saldo.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoL.$FinResto;
echo $DetRestoC.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoC.$FinResto;
echo $DetRestoR.$FinResto;
}
}
echo "</tr>";

  }
  }

if ($Corte_CTA != "") {
SALDOFINAL($Total_MovD,$Total_MovC,$SI,$DetRestoR,$FinResto,$DetCod1,$MF,$AF);
}


// Cuadro resumen por modulo - no en excel --------------------------------------------------------------------------
if ($Excel == 'N') {
echo "<br>";
echo "<table width=450 cellpadding='1' cellspacing='0' align='center' bgcolor='#FBD4CB'>
<tr><td align='center' colspan='3'><font face='Arial' size='3'><u>RESUMEN POR MODULO ORIGEN</u></font>
</td></tr>\n";
echo "<tr>
<td align='center' $Encab><font size='1' face='Verdana'>MODULO</font></td>
<td align='center' $Encab><font size='1' face='Verdana'>DESCRIPCION</font></td>
<td align='center' $Encab><font size='1' face='Verdana'>MOVIMIENTO<br>del PERIODO</font></td>
</tr>\n";

$MovFin = 0;
ksort($SalModulo);
foreach($SalModulo as $FnSrcSD=>$Mov)
        {
// Toma nombre 
BFnSrc($FnSrcSD,$c_odbc,$DFnSrc);		
		
$MovFin = $MovFin + $Mov;
$Mov_prt = number_format($Mov,2,',','');
echo "<tr>";
echo $DetRestoC0.$FnSrcSD.$FinResto;
echo $DetRestoL0.$DFnSrc.$FinResto;
echo $DetRestoR0.$Mov_prt."</font>".$FinRestodiv;
echo "</tr>";
        }
        
        
$MovFin_prt = number_format($MovFin,2,',','');
echo "<tr><td align='right' $Encab colspan='2'><font size='1' face='Verdana'>
SALDO FINAL MODULOS</font></td>
<td align='right'><font size='2' face='Verdana'>$MovFin_prt</font></font></td>
</tr>\n";
echo "</table>";
}

// ---- FUNCIONES -----------------------------------------------------------------------------------------


function agrega($c_odbc,$TablaDD,$TIPO_REG,$INDICE,$CUENTA,$AÑO,$PERIODO,$AÑOMES,$FECHA,$MOVIMIENTO,$NAS,$ORIGEN,$REFERENCE,$DSCRIPTN,$TRXSORCE,$USUARIO,$USU_ULT,$USU_EDT,$LOTE_CONT,$SERIES,$NUMERO_D,$CODIGO_3RO,
        $NOMBRE_3RO,$OrigSeqNum,$SEQNUMBR,$MONEDA_TRX,$MOV_MONEDA,$ORDOCNUM,$ORTRXSRC,$Contra_CC,$Mas_CC,$DocO,$Mas_D) {

if ($MOVIMIENTO > 0) {
$MOV_DEBITO = $MOVIMIENTO;
$MOV_CREDITO = 0;
}
else {
$MOV_CREDITO = $MOVIMIENTO * -1;
$MOV_DEBITO = 0;
}
if ($MOV_MONEDA > 0) {
$MONEDA_DEBITO = $MOV_MONEDA;
$MONEDA_CREDITO = 0;
}
else {
$MONEDA_CREDITO = $MOV_MONEDA * -1;
$MONEDA_DEBITO = 0;
}

// reemplaza los nombre con apostrofo
$NOMBRE_3RO = str_replace ("'",".", $NOMBRE_3RO);

/*
echo "INSERT INTO $TablaDD
         (TIPO_REG,INDICE,CUENTA,AÑO,PERIODO,AÑOMES,FECHA,MOVIMIENTO,NAS,ORIGEN,REFERENCE,DSCRIPTN,TRXSORCE,USUARIO,USU_ULT,USU_EDT,LOTE_CONT,SERIES,NUMERO_D,CODIGO_3RO,NOMBRE_3RO,ORDOCNUM,ORTRXSRC,OrigSeqNum,
         SEQNUMBR,MONEDA_TRX,MOV_DEBITO,MOV_CREDITO,MONEDA_DEBITO,MONEDA_CREDITO,CONTRACUENTA,CCMAS,DOC_ORIG,DOCMAS)
         VALUES ('$TIPO_REG','$INDICE','$CUENTA','$AÑO','$PERIODO','$AÑOMES','$FECHA','$MOVIMIENTO','$NAS','$ORIGEN','$REFERENCE','$DSCRIPTN','$TRXSORCE','$USUARIO','$USU_ULT',
         '$FECHA','$LOTE_CONT','$SERIES','$NUMERO_D','$CODIGO_3RO','$NOMBRE_3RO','$ORDOCNUM','$ORTRXSRC','$OrigSeqNum','$SEQNUMBR','$MONEDA_TRX','$MOV_DEBITO','$MOV_CREDITO','$MONEDA_DEBITO','$MONEDA_CREDITO','$Contra_CC','$Mas_CC','$DocO','$Mas_D')<br>";
*/

$archmen=odbc_exec($c_odbc,"INSERT INTO $TablaDD
         (TIPO_REG,INDICE,CUENTA,AÑO,PERIODO,AÑOMES,FECHA,MOVIMIENTO,NAS,ORIGEN,REFERENCE,DSCRIPTN,TRXSORCE,USUARIO,USU_ULT,USU_EDT,LOTE_CONT,SERIES,NUMERO_D,CODIGO_3RO,NOMBRE_3RO,ORDOCNUM,ORTRXSRC,OrigSeqNum,
         SEQNUMBR,MONEDA_TRX,MOV_DEBITO,MOV_CREDITO,MONEDA_DEBITO,MONEDA_CREDITO,CONTRACUENTA,CCMAS,DOC_ORIG,DOCMAS)
         VALUES ('$TIPO_REG','$INDICE','$CUENTA','$AÑO','$PERIODO','$AÑOMES','$FECHA','$MOVIMIENTO','$NAS','$ORIGEN','$REFERENCE','$DSCRIPTN','$TRXSORCE','$USUARIO','$USU_ULT',
         '$FECHA','$LOTE_CONT','$SERIES','$NUMERO_D','$CODIGO_3RO','$NOMBRE_3RO','$ORDOCNUM','$ORTRXSRC','$OrigSeqNum','$SEQNUMBR','$MONEDA_TRX','$MOV_DEBITO','$MOV_CREDITO','$MONEDA_DEBITO','$MONEDA_CREDITO','$Contra_CC','$Mas_CC','$DocO','$Mas_D')")
           or die(ODBC_error());

}

// Funcion para formato numero
function FFORM($ValOri,$Dec,&$Val) {
if ($ValOri == 0) {
$Val = "";
}
else {
$Val = number_format(($ValOri),$Dec,',','.');
}
}


// Impresion Saldo Final
function SALDOFINAL($Total_MovD,$Total_MovC,$SI,$DetRestoR,$FinResto,$DetCod1,$MF,$AF) {
if ($Total_MovD > 0 and $Total_MovC > 0) {
$Neto = $Total_MovD - $Total_MovC;
$Movim = $Neto - $SI;
$PrtNeto = 1;
}
else {
$PrtNeto = 0;
}
FFORM($Total_MovD,2,$Total_MovD);
FFORM($Total_MovC,2,$Total_MovC);
echo "<tr>";
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$MF."/".$AF.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetCod1."SALDO FINAL".$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR."<font color='darkblue'><b>".$Total_MovD."</b></font>".$FinResto;
echo $DetRestoR."<font color='darkblue'><b>".$Total_MovC."</b></font>".$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo "</tr>";

if ($PrtNeto == 1) {
if ($Neto >= 0) {
FFORM($Neto,2,$Total_MovD);
$Total_MovC = "";
}
else {
$Neto = $Neto * -1;
FFORM($Neto,2,$Total_MovC);
$Total_MovD = "";
}

echo "<tr>";
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$MF."/".$AF.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetCod1."SALDO NETO".$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR."<font color='darkblue'><b>".$Total_MovD."</b></font>".$FinResto;
echo $DetRestoR."<font color='darkblue'><b>".$Total_MovC."</b></font>".$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo "</tr>";

if ($Movim >= 0) {
FFORM($Movim,2,$Total_MovD);
$Total_MovC = "";
}
else {
$Movim = $Movim * -1;
FFORM($Movim,2,$Total_MovC);
$Total_MovD = "";
}

echo "<tr>";
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$MF."/".$AF.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetCod1."MOVIMIENTOS".$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR."<font color='darkblue'><b>".$Total_MovD."</b></font>".$FinResto;
echo $DetRestoR."<font color='darkblue'><b>".$Total_MovC."</b></font>".$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo $DetRestoR.$FinResto;
echo "</tr>";
}


}




?>

