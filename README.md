# restreaming-remote-video-file
Restreaming mp4 file from an url, allow fast forward, backward...  
It may be need to improve to limit speed client ?

```
$remoteFile = "https://galaxycloud.vn/abc.mp4";
#header('Content-type: video/mp4');
#echo file_get_contents($remoteFile);

play($remoteFile);
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
```
