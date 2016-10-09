<?php
//
// 响应用户消息
// 微信公众账号响应给用户的不同消息类型
//

define("TOKEN", "weixin");

$wechatObj = new wechatCallbackapiTest();
if (!isset($_GET['echostr'])) {
	$wechatObj->responseMsg();
}else{
    $wechatObj->valid();
}

class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if($tmpStr == $signature){
            return true;
        }else{
            return false;
        }
    }

    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);

            //用户发送的消息类型判断
            switch ($RX_TYPE)
            {
                case "text":
                    $result = $this->receiveText($postObj);
                    break;
                case "image":
                    $result = $this->receiveImage($postObj);
                    break;
                case "voice":
                    $result = $this->receiveVoice($postObj);
                    break;
                case "video":
                    $result = $this->receiveVideo($postObj);
                    break;
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;
                default:
                    $result = "unknow msg type: ".$RX_TYPE;
                    break;
            }
            echo $result;
        }else {
            echo "";
            exit;
        }
    }
    
    private function receiveText($object)
    {
        $keyword = trim($object->Content);

        if($keyword == "文本"){
            //回复文本消息
            $content = "这是个文本消息";
            $result = $this->transmitText($object, $content);
        }
        else if($keyword == "图文" || $keyword == "单图文"){
            //回复单图文消息
            $content = array();
            $content[] = array("Title"=>"单图文标题", 
                                "Description"=>"单图文内容", 
                                "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", 
                                "Url" =>"http://m.cnblogs.com/?u=txw1958");
            $result = $this->transmitNews($object, $content);
        }
        else if($keyword == "多图文"){
            //回复多图文消息
            $content = array();
            $content[] = array("Title"=>"多图文1标题", "Description"=>"", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
            $content[] = array("Title"=>"多图文2标题", "Description"=>"", "PicUrl"=>"http://d.hiphotos.bdimg.com/wisegame/pic/item/f3529822720e0cf3ac9f1ada0846f21fbe09aaa3.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
            $content[] = array("Title"=>"多图文3标题", "Description"=>"", "PicUrl"=>"http://g.hiphotos.bdimg.com/wisegame/pic/item/18cb0a46f21fbe090d338acc6a600c338644adfd.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
            $result = $this->transmitNews($object, $content);
           
        }
        else if($keyword == "音乐"){
            //回复音乐消息
            $content = array("Title"=>"最炫民族风", 
            "Description"=>"歌手：凤凰传奇", 
            "MusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3",
            "HQMusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3");
            $result = $this->transmitMusic($object, $content);
        }
        
        return $result;
    }

    private function receiveImage($object)
    {
        //回复图片消息 
        $content = array("MediaId"=>$object->MediaId);
        $result = $this->transmitImage($object, $content);;
        return $result;
    }

    private function receiveVoice($object)
    {
        //回复语音消息 
        $content = array("MediaId"=>$object->MediaId);
        $result = $this->transmitVoice($object, $content);;
        return $result;
    }

    private function receiveVideo($object)
    {
        //回复视频消息 
        $content = array("MediaId"=>$object->MediaId, "ThumbMediaId"=>$object->ThumbMediaId, "Title"=>"", "Description"=>"");
        $result = $this->transmitVideo($object, $content);;
        return $result;
    }  
    
    /*
     * 回复文本消息
     */
    private function transmitText($object, $content)
    {
        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }
    
    /*
     * 回复图片消息
     */
    private function transmitImage($object, $imageArray)
    {
        $itemTpl = "<Image>
    <MediaId><![CDATA[%s]]></MediaId>
</Image>";

        $item_str = sprintf($itemTpl, $imageArray['MediaId']);

        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[image]]></MsgType>
$item_str
</xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    
    /*
     * 回复语音消息
     */
    private function transmitVoice($object, $voiceArray)
    {
        $itemTpl = "<Voice>
    <MediaId><![CDATA[%s]]></MediaId>
</Voice>";

        $item_str = sprintf($itemTpl, $voiceArray['MediaId']);

        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[voice]]></MsgType>
$item_str
</xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    
    /*
     * 回复视频消息
     */
    private function transmitVideo($object, $videoArray)
    {
        $itemTpl = "<Video>
    <MediaId><![CDATA[%s]]></MediaId>
    <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
</Video>";

        $item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['ThumbMediaId'], $videoArray['Title'], $videoArray['Description']);

        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[video]]></MsgType>
$item_str
</xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    
    /*
     * 回复图文消息
     */
    private function transmitNews($object, $arr_item)
    {
        if(!is_array($arr_item))
            return;

        $itemTpl = "    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>
";
        $item_str = "";
        foreach ($arr_item as $item)
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);

        $newsTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<Content><![CDATA[]]></Content>
<ArticleCount>%s</ArticleCount>
<Articles>
$item_str</Articles>
</xml>";

        $result = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($arr_item));
        return $result;
    }
    
    /*
     * 回复音乐消息
     */
    private function transmitMusic($object, $musicArray)
    {
        $itemTpl = "<Music>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
    <MusicUrl><![CDATA[%s]]></MusicUrl>
    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
</Music>";

        $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);

        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[music]]></MsgType>
$item_str
</xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    private function receiveEvent($object)
    {
        $content = "";
        switch ($object->Event)
        {
            case "subscribe":   //关注事件
                $content = "欢迎关注方倍工作室 ";
                $content .= (!empty($object->EventKey)) ? ("\n来自二维码厂家 ".str_replace("qrscene_", ""replace"", $object->EventKey)) : "";
                break;
            case "unsubscribe": //取消关注事件
                $content = "取消关注";
                break;

            case 'CLICK':
                switch ($object->EventKey) {
                    case 'COMPANY':
                        $content = array();
                        $content[] = array("Title"=>"方倍工作室", "Description"=>"", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url"=>"http://m.cnblogs.com/?u=txw1958");
                        break;                    
                    default:
                        $content = "单击菜单".$object->EventKey;
                        break;
                }
                break;
            case 'VIEW':
                $content = "跳转链接".$object->EventKey;
                break;
            case 'SCAN':
                $content = "扫描场景".$object->EventKey;
                break;
            case 'LOCATION':
                $content = "上传位置：纬度".$object->Latitude."；经度".$object->Longitude;
                break;
            case 'scancode_waitmsg':
                $content = "扫码带提示：类型".$object->ScanCodeInfo->ScanType." 结果：".$object->ScanCodeInfo->ScanResult;
                break;
            case 'scancode_push':
                $content = "扫码推事件";
                break;
            case 'pic_sysphoto':
                $content = "系统拍照";
                break;
            case 'pic_weixin':
                $content = "相册发图：数量".$object->SendPicsInfo->Count;
                break;
            case 'pic_photo_or_album':
                $content = "拍照或者相册：数量".$object->SendPicsInfo->Count;
                break;
            case 'location_select':
                $content = "发送位置：标签".$object->SendLocationInfo->Label;
                break;
            default:
                $content = "Receive a new event: ".$object->Event;
        }

        if (is_array($content)) {
            if (isset($content[0]['PicUrl'])) {
                $result = $this->transmitNews($object, $content);
            } else if (isset($content['MusicUrl'])) {
                $result = $this->transmitMusic($object, $content);
            }
        } else {
            $result = $this->transmitText($object, $content);
        }

        return $result;
    }

}
?>