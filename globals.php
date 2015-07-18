<?php
define("FNSB_NAME", "Fansub");
define('FNSB_FILEPATH', dirname(__FILE__));
define("FNSB_DIRNAME", basename(FNSB_FILEPATH));
define("FNSB_BASE", FNSB_DIRNAME.'/settings.php');
define('FNSB_FOLDER', dirname(plugin_basename(__FILE__)));
define('FNSB_URL', plugins_url(FNSB_FOLDER, dirname(__FILE__)));

define("FNSB_SETTINGS", "Configurações");
define("FNSB_PROJECTS", "Projectos");
define("FNSB_RELEASES", "Lançamentos");
define("FNSB_XDCC", "XDCC");
define("FNSB_SCRAPE", "Scrape");
define("FNSB_EDIT", "Editar");

define("FNSB_STATUS_ONGOING", "Em Andamento");
define("FNSB_STATUS_STALLED", "Parado");
define("FNSB_STATUS_DROPPED", "Abandonado");
define("FNSB_STATUS_COMPLETED", "Terminado");
define("FNSB_STATUS_LICENSED", "Licenciado");

$fnsb_status = array("1"=>FNSB_STATUS_ONGOING,"2"=>FNSB_STATUS_STALLED,"3"=>FNSB_STATUS_LICENSED,"4"=>FNSB_STATUS_DROPPED,"5"=>FNSB_STATUS_COMPLETED);
$fnsb_scolor = array("1"=>"#008000","2"=>"","3"=>"#FF0000","4"=>"#FF0000","5"=>"#0000FF");

function fnsb_status_opt($sel=null) {
	global $fnsb_status, $fnsb_scolor;
	$fnsb_status_opt = "<select name='status'>";
	foreach($fnsb_status as $key => $val) {
		$fnsb_status_opt .= "<option value='".$key."' style='color:".$fnsb_scolor[$key]."'".($sel == $key ? " selected" : "").">".$val."</option>";
	}
	$fnsb_status_opt .= "</select>";
	return $fnsb_status_opt;
}
function fnsbrel2opts($rows, $sel=null) {
	$out .= "<option value=''></option>";
	foreach($rows as $row) {
		$out .= "<option value='".$row['rid']."'".($row['rid'] == $sel ? " selected" : "").">".stripslashes($row['title'])." - ".str_pad($row['epi'], strlen($row['episodes']), "0", STR_PAD_LEFT).($row['ver'] == "1" ? "" : "v".$row['ver'])." - [".$row['crc']."]</option>";
	}
	return $out;
}
?>