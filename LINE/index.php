<?php

/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

require_once('./LINEBotTiny.php');
require_once('./nomalReply.php');
require_once('./Dice/Dice_CoC7th.php');
require_once('./Dice/Dice_nomalDice.php');
require_once('./Dice/Dice_pbta.php');
require_once('./Dice/Dice_extraDice.php');
require_once('./Dice/Dice_test.php');

//主要的全域變數，只有簡易的API，覺得難過香菇
//試著手動加入了getProfile的功能…不知道是否用得到
$channelAccessToken = getenv('LINE_CHANNEL_ACCESSTOKEN');
$channelSecret = getenv('LINE_CHANNEL_SECRET');
$keyWord = getenv('KEY_WORD');
$manualUrl = getenv('MANUAL_URL');
$textReplyUrl = getenv('TEXT_REPLY_URL');
$imgsReplyUrl = getenv('IMGS_REPLY_URL');
$yababangUrl = getenv('YABABANG_URL');

$bot = new LINEBotTiny($channelAccessToken, $channelSecret);
$userName = '你';

//建立文字訊息的函數
function buildTextMessage($inputStr){	
	settype($inputStr, "string");
	error_log("訊息【".$inputStr."】準備以文字訊息回傳");
	$message = array
		(
		array(
            'type' => 'text',
            'text' => $inputStr
            )
        );
	return $message;
}

//建立圖片訊息的函數
function buildImgMessage($inputStr){	
	settype($inputStr, "string");
	error_log("訊息【".$inputStr."】準備以圖片訊息回傳");
	$message = array
		(
		array(
			'type' => "image", 
            'originalContentUrl' => $inputStr, 
            'previewImageUrl' => $inputStr
            )
        );
	return $message;
}

//建立貼圖訊息的函數
function buildStickerMessage($packageId, $stickerId){	
	error_log("準備回傳".$packageId."之".$stickerId."貼圖");
	$message = array
		(
		array(
			'type' => "sticker", 
            'packageId' => $packageId, 
            'stickerId' => $stickerId
            )
        );
	return $message;
}


//建立複數訊息，的物件
class MutiMessage{

	public function send($inputArr){	
		//settype($inputStr, "string");
		error_log("回傳複數訊息");
		$message = $inputArr;
		return $message;
	}
	
	//建立文字訊息的函數
	public function text($inputStr){
		settype($inputStr, "string");
		error_log("訊息【".$inputStr."】準備以文字訊息回傳");
		$message = array(
            'type' => 'text',
            'text' => $inputStr
            );
		return $message;
	}
	
	//建立圖片訊息的函數
	public function img($inputStr){
		settype($inputStr, "string");
		error_log("訊息【".$inputStr."】準備以圖片訊息回傳");
		$message = array(
			'type' => "image", 
            'originalContentUrl' => $inputStr, 
            'previewImageUrl' => $inputStr
            );
		return $message;
	}
	
	//建立貼圖訊息的函數
	public function sticker($packageId, $stickerId){	
		error_log("準備回傳".$packageId."之".$stickerId."貼圖");
		$message = array(
			'type' => "sticker", 
            'packageId' => $packageId, 
            'stickerId' => $stickerId
            );
	return $message;
	}
	
	//建立旋轉木馬訊息(???)的函數
	public function carousel($altText, $columns){	
		error_log("準備回傳旋轉木馬訊息（殺小啦wwww");
		$message = array(
			'type'=> "template",
			'altText'=> $altText,
			'template'=> array(
				'type'=> "carousel",
				'columns'=> $columns
            )
		);
	return $message;
	}
	
}




foreach ($bot->parseEvents() as $event) {
		
    switch ($event['type']) {
		//收到訊息的動作
        case 'message':
			$message = $event['message'];			
			$source = $event['source'];
			if($source['type'] == "group"){		
				
				$groupId = $source['groupId'];
				$userId = $source['userId'];
				error_log("群組ID：".$groupId);
				if($userId != null){
								
					$userName = $bot->getGroupProfile($groupId,$userId)['displayName'];
					error_log("訊息發送人：".$userName);
					}
				else{
					error_log("訊息發送人：不明");
				}
				}
			if($source['type'] == "user"){
				$userName = $bot->getProfile($source['userId'])['displayName'];
				error_log("訊息發送人：".$userName);
				}
			
			
			//對訊息類別做篩選
            switch ($message['type']) {				
				
				//只針對文字訊息去回應
                case 'text':
                	$m_message = $message['text'];
					
                	if($m_message!="")
                	{
											
						error_log("收到訊息：".$m_message);
						$messages = parseInput($m_message);
						
						if ($messages == null) {
							error_log("無觸發");
							break;
						}
						
						$bot->replyMessage(
							array(
							'replyToken' => $event['replyToken'],
							'messages' => $messages
							)
						);	

                	}
                    break;
                
				case 'image':
				error_log("傳送了圖片。");
				break;
				
				case 'video':
				error_log("傳送了影片。");
				break;
				
				case 'sticker':
				error_log("傳送了貼圖。");
				break;
            }
            break;
			
		//被加入聊天室的動作
		case 'join':
			error_log("被加入聊天室");
			$messages = new MutiMessage();
			$replyArr = Array(
				$messages->text("大家好，我是擲骰機器人".$keyWord."。\n請輸入「".$keyWord."說明」獲得使用說明。"),
				$messages->sticker(1,2)
			);
			
			$bot->replyMessage(
				array(
				'replyToken' => $event['replyToken'],
				'messages' => $replyArr
				)
			);		
			break;
			
			//被加入好友的動作
		case 'follow':
			error_log("被加入好友");
			$messages = new MutiMessage();
			$replyArr = Array(
				$messages->text("你好哦，我是擲骰機器人".$keyWord."。\n請輸入「".$keyWord."說明」獲得使用說明。"),
				$messages->sticker(4,631),
				$messages->text("建議使用手機界面，可以更簡單的選取說明哦。")
			);
			
			$bot->replyMessage(
				array(
				'replyToken' => $event['replyToken'],
				'messages' => $replyArr
				)
			);		
			break;
			
        default:
            error_log("不支援的訊息: " . $event['type']);
            break;
    }
};

//這是基本判斷式
function parseInput ($inputStr){
	global $userName;
	global $keyWord;
	global $manualUrl;
	global $textReplyUrl;
	global $imgsReplyUrl;
	global $yababangUrl;
	

	//error_log("訊息【".$inputStr."】進入parseInput");
	$inputStr = strtolower($inputStr);

	//preg_match ( "/A/" , B)。A是要比對的關鍵字（正則），B是被比對的字串
	if (preg_match ("/dvtest/i", $inputStr)){
		return DvTest ($inputStr,$userName,$textReplyUrl,$imgsReplyUrl);
		
	}else if (preg_match ("/^cc/i", $inputStr)){
		return CoC7th($inputStr);
		
	}else if(preg_match ("/^pb/i", $inputStr)){		
		return pbta($inputStr);
		
	}else if(stristr($inputStr,$keyWord) != false){ //$keyWord
		return KeyWordReply($inputStr,$keyWord,$manualUrl,$textReplyUrl,$userName);
				
	}else if(stristr($inputStr,".jpg") != false || stristr($inputStr,"ry") != false){
		return SendImg($inputStr,$imgsReplyUrl);
		
	}else if(preg_match ("/d/i", $inputStr) !=false){
		return nomalDiceRoller($inputStr);
		
	}else if(preg_match ("/b/i", $inputStr) !=false){
		return bDice($inputStr);
	}
	
	
	else {
	return null;
	}
}


function DvTest ($inputStr,$userName,$textReplyUrl,$imgsReplyUrl){


	error_log("進入DvTest");
	
	if(preg_match ("/muti|複數|多重/i", $inputStr) !=false){
	$testMessage = new MutiMessage();
	$replyArr = Array(
		$testMessage->text('多重訊息演示'),
		$testMessage->text('test2'),
		$testMessage->img('https://i.imgur.com/k4QE5Py.png'),
		$testMessage->sticker(1,2)
		);
	
	return $testMessage->send($replyArr);
	}
	
	if(preg_match ("/key|關鍵/i", $inputStr) !=false){
	
	//抓文字關鍵字
	$reply = "《文字關鍵字列表》\n";
	//讀入文字回應變數
	$content = file_get_contents($textReplyUrl);
	
	//如果失敗就調用預設值
	if ($content === false) {
		$content = file_get_contents('./exampleJson/textReply.json');
	}
	
	//userName會回傳為使用者名稱，如果有辦法取得的話。
	$content = preg_replace("/userName/" , $userName , $content);
	//keyWord會回傳為設定的關鍵字，通常就是機器人的名字。
	$content = preg_replace("/keyWord/" , $keyWord , $content);
		
	$text = json_decode($content, true);
	
	
	$count = 0;
	foreach($text as $txtChack){
		$countIn = 0;
		$tempStr = "[";
		foreach($txtChack['chack'] as $chack){
			$tempStr = $tempStr . $chack ."、";
			$count++;
			$countIn++;
		}
		$tempStr = chop($tempStr,'、').']';
		if( $count >= 4){
			$reply = chop($reply ,'；');
			$tempStr = "\n".$tempStr."；";
			$count = $countIn;
		}
		else{
			$tempStr = $tempStr."；";
		}
		$reply = $reply.$tempStr;
	}	
	
	$reply = chop($reply ,'；');
	

	//抓圖片關鍵字
	$reply = $reply."\n\n《圖片關鍵字列表》\n";
	
	//讀入圖片回應變數
	$content = file_get_contents($imgsReplyUrl);
	//如果失敗就調用預設值
	if ($content === false) {
		$content = file_get_contents('./exampleJson/imgReply.json');
	}
	$img = json_decode($content, true);
	
	$count = 0;
	foreach($img as $imgChack){
		$countIn = 0;
		$tempStr = "[";
		foreach($imgChack['chack'] as $chack){
			$tempStr = $tempStr . $chack ."、";
			$count++;
			$countIn++;
		}
		$tempStr = chop($tempStr,'、').']';
		if( $count >= 4){
			$reply = chop($reply ,'；');
			$tempStr = "\n".$tempStr."；";
			$count = $countIn;
		}
		else{
			$tempStr = $tempStr."；";
		}
		$reply = $reply.$tempStr;
	}		
	$reply = chop($reply ,'；');
	
	return buildTextMessage($reply);	
	
	}
	
	
	//應聲蟲功能哦
	$input = str_replace("dvtest ","",$inputStr);
	$finalStr = "input:\n".$input."\nstrlen:".strlen($input);
	return buildTextMessage($finalStr);
}
