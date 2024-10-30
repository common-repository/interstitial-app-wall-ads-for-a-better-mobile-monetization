<?php

    require(dirname(__FILE__).'/config.php');

    $request = $_POST+$_GET;
    

    $userAgent = '[android]';
    if(strstr($_SERVER['HTTP_USER_AGENT'],'iPhone'))
    {
        $userAgent = '[iphone]';
    }
    $url = 'http://ads.appia.com/v2/getAds?id=401&siteId=3438&totalCampaignsRequested=5&password=JOQ8M4HQU9SFLSD5KT2S1OAQ4H&userAgentHeader=' . urldecode($userAgent)
            . '&sessionId=' . urldecode('[session_id]') . '&ipAddress=' . urldecode($_SERVER['REMOTE_ADDR']) ;
    
    if (isset($request['wiziapp_app_id']))
    {
        $url = $url . '&optionalParams=wiziapp_app_id%3D' . $request['wiziapp_app_id'];
    }

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch,CURLOPT_URL,$url);
                    
                    
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
        
    //print_r($result);
    $resultXml = new SimpleXMLElement($result);
    
    //$itemsXml = $resultXml->ads;
    $ads = array();
    $adsCount = 0 ;
    foreach ($resultXml->children() as $item) {
        if ($item->getName() == 'ad')
        {
            $adsCount++;
            $ads[] = array(
                'clickurl' => $item->clickProxyURL->__tostring(),
                'title' => $item->productName->__tostring(),
                'description' => $item->productDescription->__tostring(),
                'tumbnail' => $item->productThumbnail->__tostring(),
                'rating' => $item->averageRatingImageURL->__tostring()
            );               
        }
        if ($adsCount == 5)
        {
            break;
        }
    }
    
    if (count($ads) > 0)
    {        
        if (isset($request['wiziapp_app_id']))
        {
                $dbCh = curl_init();

                curl_setopt($dbCh, CURLOPT_HEADER, FALSE);
                $dbUrl= $config['api_wall_db_address'] . '?wiziapp_app_id=' . $request['wiziapp_app_id'] . '&wiziapp_remote_addr=' . urldecode($_SERVER['REMOTE_ADDR']) ;
                curl_setopt($dbCh,CURLOPT_URL,$dbUrl);
                    
                    
                curl_setopt($dbCh,CURLOPT_RETURNTRANSFER, 1);
                $dbResult = curl_exec($dbCh);
        }
    }
?>

<!DOCTYPE html>
<html>
	<head>
                <!--Digital window verification 001 -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0" />
                <meta name="description" content="AppWall">
		<title>Apps Wall</title>
                <link rel="stylesheet" type="text/css" href="appwall.css" />
	</head>
	<body class="app-body" onload="parent.wiziappappwall_autoIframe();">

<?php
    foreach($ads as $item)
    {
       
        echo '<a class=app-item-link target=_blank href=' . $config['api_wall_click_address'] . '?wiziapp_app_id=' .$request['wiziapp_app_id'] . '&click='. urlencode($item['clickurl']) . '>';
        echo '<div class=app-item>';
        echo '<div  class=app-title>' . $item['title'] . '</div>' ;
        echo '<div class=app-description>' . $internalInfo . $item['description'] . '</div>';
        echo '<img class=app-rating src="' . $item['rating'] . '">';
        echo '<img class=app-thumbnail src="' . $item['tumbnail'] . '" height="72" width="72">';
        echo '<div class=app-free> Free </div>';
        echo '</div>';
        echo '</a>';
        
    }
?>
	</body>
</html>
