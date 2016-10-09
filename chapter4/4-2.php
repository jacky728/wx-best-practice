<?php
require('4-1.php');

$jsonmenu = '{
	"button": [{
		"name": "扫码",
		"sub_button": [{
			"type": "scancode_waitmsg",
			"name": "扫码带提示",
			"key": "rselfmenu_0_0"
		}, {
			"type": "scancode_push",
			"name": "扫码推事件",
			"key": "rselfmenu_1_1"
		}]
	}, {
		"name": "发图",
		"sub_button": [{
			"type": "pic_sysphoto",
			"name": "系统拍照发图",
			"key": "rselfmenu_1_0",
		}, {
			"type": "pic_photo_or_album",
			"name": "拍照或者相册发图",
			"key": "rselfmenu_1_1"
		}, {
                        "type": "pic_weixin",
                        "name": "微信相册发图",
                        "key": "rselfmenu_1_2"			
		}]
	}, {
		"name": "其它",
		"sub_button": [{
                        "type": "location_select",
                        "name": "发送位置",
                        "key": "rselfmenu_2_0"
		}, {
                        "type": "click",
                        "name": "今日歌曲",
                        "key": "V1001_TODAY_MUSIC"
		}, {
                        "type": "view",
                        "name": "搜索",
                        "url": "http://www.baidu.com/"
		}]
	}]
}';

$url_create = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
$url_get = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=".$access_token;
$url_delete = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$access_token;

$url = $url_get;
$result = https_request($url, $jsonmenu);
var_dump($result);

function https_request($url,$data = null){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

?>
