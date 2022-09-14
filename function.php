<?php
include_once 'xlsxwriter.class.php';
$try = 1;

function get_all_links($keywords)
{
    $API_KEY = "API KEY";
    $multiCurl = array();
    // data to be returned
    $result = array();

    $mh = curl_multi_init();
    $i = 1;
    foreach ($keywords as $keyword) {
        $searchTerm = urlencode($keyword);
        $url = "https://zutrix.com/api/serp?q=" . $searchTerm . "&gl=vn&html=false&key=" . $API_KEY;
        $multiCurl[$i] = curl_init();
        curl_setopt($multiCurl[$i], CURLOPT_URL, $url);
        curl_setopt($multiCurl[$i], CURLOPT_HEADER, 0);
        curl_setopt($multiCurl[$i], CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($multiCurl[$i], CURLOPT_SSL_VERIFYPEER, false);
        curl_multi_add_handle($mh, $multiCurl[$i]);
        $i++;
    }
    $index = null;
    do {
        curl_multi_exec($mh, $index);
    } while ($index > 0);
    // get content and remove handles
    foreach ($multiCurl as $k => $ch) {
        $result[$k] = curl_multi_getcontent($ch);
        curl_multi_remove_handle($mh, $ch);
    }
    // close
    curl_multi_close($mh);

    // decode json of the result array
    $links = array();
    foreach ($result as $key => $value) {
        $result[$key] = json_decode($value, true);

        if(!empty($result[$key]['organic_results'])){
            $count = 0;
            foreach($result[$key]['organic_results'] as $data){
                if ($count<10){
                    $links[$key][$count] = $data['link'];
                    $count++;
                }
            }
        }
    }


    return $links;
}


function get_ratio($mainLinks, $subLinks)
{
    $ratio = 0;
    foreach ($mainLinks as $mainLink) {
        foreach ($subLinks as $subLink) {
            if ($mainLink == $subLink) {
                $ratio++;
            }
        }
    }
    return $ratio;
}

function write_to_xlsx($keywords, $ratio)
{
    if (!isset($_SESSION['error'])) {
        $writer = new XLSXWriter();
        foreach ($keywords as $keyword) {
            $writer->writeSheetRow('Sheet1', array($keyword, $ratio[$keyword]));
        }
        $writer->writeToFile('Keyword Ratio.xlsx');
    }
}
