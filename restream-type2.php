<?php 
$str = "http://127.0.0.1:9000".$_SERVER['REQUEST_URI'];
//header('Content-type:image/png');
#header('Content-type: video/mp4');
    //header("Accept-Ranges: 0-$length");
//header("Accept-Ranges: bytes");
#echo file_get_contents($str);

$remoteFile = $str;
//play($remoteFile);

function play($url){
    ini_set('memory_limit', '1024M');
    set_time_limit(3600);
    ob_start();
    if (isset($_SERVER['HTTP_RANGE'])) $opts['http']['header'] = "Range: " . $_SERVER['HTTP_RANGE'];
    $opts['http']['method'] = "HEAD";
    $conh = stream_context_create($opts);
    $opts['http']['method'] = "GET";
    $cong = stream_context_create($opts);
    $out[] = file_get_contents($url, false, $conh);
    $out[] = $httap_response_header;
    ob_end_clean();
    array_map("header", $http_response_header);
    readfile($url, false, $cong);
}

$writefn = function($ch, $chunk) {
    echo $chunk;
    return strlen($chunk);
};
	
function reStream($dlink,  $filesize = 0, $fname){

    global $writefn;

    $size = $filesize;
    $seek_start = 0;
    $seek_end = $size-1;

    //check if http_range is sent by browser (or download manager)
    if(isset($_SERVER['HTTP_RANGE']))
    {
        list($size_unit, $range_orig) = explode('=', $_SERVER['HTTP_RANGE'], 2);

        if ($size_unit == 'bytes')
        {
            list($range, $extra_ranges) = explode(',', $range_orig, 2);
            list($seek_start, $seek_end) = explode('-', $range, 2);
            $seek_end = (empty($seek_end)) ? ($size - 1) : min(abs(intval($seek_end)),($size - 1));
            $seek_start = (empty($seek_start) || $seek_end < abs(intval($seek_start))) ? 0 : max(abs(intval($seek_start)),0);
            if ($seek_start > 0 || $seek_end < ($size - 1))
            {
                header('HTTP/1.1 206 Partial Content');
                header('Content-Range: bytes '.$seek_start.'-'.$seek_end.'/'.$filesize);
                header('Content-Length: '.($seek_end - $seek_start + 1));
                header('Accept-Ranges: bytes');
            }
        }
        else
        {
            $range = '';
        }
    }
    else
    {
        $range = '';
    }

    if(!$range)
        header('Content-Length: '.$filesize);

    //header("Content-Type: ".CFile::getMimeRemote($dlink));

    header('Content-Disposition: attachment; filename="'.$fname.'"');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $dlink);
    curl_setopt($ch, CURLOPT_RANGE, "$seek_start-$seek_end");
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
    curl_setopt($ch, CURLOPT_WRITEFUNCTION, $writefn);
    $result = curl_exec($ch);
    curl_close($ch);
}

$url = $str;

$data = get_headers($url, true);
$filesize = isset($data['Content-Length'])?(int) $data['Content-Length']:0;

//echo $filesize;
ob_clean();
//header('Content-type: video/mp4');
reStream($url, $filesize, 'abc.mp4');

?>
