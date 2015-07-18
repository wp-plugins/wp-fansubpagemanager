<?php
require_once('globals.php');
if(!current_user_can('wpfansubpagemanager')) {
	die('Acesso Negado');
}
?>

<div class="wrap">
	<h2><?php _e('WP-FansubPageManager - Scrape', 'wpfansubpagemanager' ); ?></h2>
<?php
$rows = $wpdb->get_results("SELECT crc FROM ".$wpdb->prefix."releases ORDER by id ASC;", ARRAY_A);
foreach($rows as $r) {
	$crc[] = $r['crc'];
}

if ( get_option('fnsb_nyaa') ) {
	print("Scraping nyaa...<br />");
	$nids = explode(",", get_option('fnsb_nyaa_id'));
	foreach($nids as $nid) {
		$data .= file_get_contents("http://www.nyaa.se/?page=separate&user=".$nid);
	}
	preg_match_all("'<td class=\"center\">([0-9a-fA-F]{8})</td>\s*<td class=\"number_?[a-zA-Z]*\">([0-9]*)</td>\s*<td class=\"number_?[a-zA-Z]*\">([0-9]*)</td>\s*<td class=\"number_?[a-zA-Z]*\">([0-9]*)</td>'", $data, $matches);
	unset($matches['0']);
	print("A identificar dados...<br />");
	for($i=0;$i<count($matches['1']);$i++) {
		if ( in_array($matches['1'][$i], $crc) ) {
			$inserts[] = "('".$matches['1'][$i]."','".$matches['2'][$i]."','".$matches['3'][$i]."','".$matches['4'][$i]."')";
		}
	}
	print("A apagar dados antigos...<br />");
	$wpdb->query("DELETE FROM ".$wpdb->prefix."nyaa;");
	print("A inserir dados novos...<br />");
	$wpdb->query("INSERT INTO ".$wpdb->prefix."nyaa (`crc`,`seeders`,`leechers`,`completed`) VALUES ".implode(",", $inserts).";");
	print("Terminado.<br />");
} else {
	print("Nyaa scraping desactivado.<br />");
}
if ( get_option('fnsb_xdcc') ) {
	print("A ler lançamentos...<br />");
	$crc = implode("|", $crc);
	print("A ler bots...<br />");
	$rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."xdcc_bot ORDER by id ASC;", ARRAY_A);
	print("A ler dados novos...<br />");
	foreach($rows as $r) {
		$ch = curl_init();
		curl_setopt_array($ch, array( CURLOPT_URL => $r['url'], CURLOPT_RETURNTRANSFER => TRUE, CURLOPT_TIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => FALSE ));
		if(!($xdcc[$r['nick']] = curl_exec($ch))) {
			print("ERRO: Incapaz de tratar de ficheiros remotos {$r['url']}<br />\n");
			print(curl_error($ch)."<br />\n");
			continue;
		}
	}
	print("A tratar de dados novos...<br />");
	foreach($xdcc as $nick=>$packlist) {
		preg_match_all("/#(\d+)\s+\d+x\s+\[.*?\d+\.?\d+?\D\]\s+.*\[(".$crc.")\].*\W/mi",$packlist,$packs[$nick]);
	}
	print("A colectar dados novos...<br />");
	foreach($packs as $nick=>$pack) {
		for($i=0;$i<count($pack['1']);$i++) {
			$insert[] = "('".$pack['1'][$i]."','".$nick."','".$pack['2'][$i]."')";
		}
	}
	print("A apagar dados antigos...<br />");
	$wpdb->query("DELETE FROM ".$wpdb->prefix."xdcc_list;");
	print("A inserir dados novos...<br />");
	$wpdb->query("INSERT INTO ".$wpdb->prefix."xdcc_list (`id`,`nick`,`crc`) VALUES ".implode(",", $insert).";");
} else {
	print("XDCC scraping desactivado.<br />");
}
	print("Feito!");
?>
</div>