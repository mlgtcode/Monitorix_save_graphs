<?php
// CONFIG
$monitorix_url = "http://192.168.1.216:8583/monitorix-cgi/monitorix.cgi?mode=localhost&graph=all&when=1week&color=white";
$save_dir = "/root/graphs/";
$save = "true";

// SCIRPT
function httpreq($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Cache-Control: no-cache"]);
    $result = curl_exec($ch);
    curl_close($ch);
    sleep(1);
    return $result;
}

function savegraph($imgs, $imgf)
{
    $fp = fopen($imgf, "w");
    fwrite($fp, $imgs);
    fclose($fp);
}

$cts = date("Y-m-d-H-i-s");
$html = httpreq($monitorix_url);
$check_hash = preg_match_all(
    "/ *\<a href=\"javascript:void\(window\.open\(\'(htt(p|ps):\/\/.*\.(svg|png))\',/",
    $html,
    $graphs
);

if ($save == "true") {
    echo "Found following graphs:\n";
}

foreach ($graphs[1] as $myimg) {
    if ($save == "true") {
        echo "* " . basename($myimg) . "\n";
        savegraph(
            httpreq($myimg . "?=" . $cts),
            $save_dir . $cts . "-" . basename($myimg)
        );
    } else {
        echo '<div class ="graph">';
        echo httpreq($myimg . "?=" . $cts);
        echo "</div>";
    }
}
?>
