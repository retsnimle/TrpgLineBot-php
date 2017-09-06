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


//主要的全域變數，只有簡易的API，覺得難過香菇
$channelAccessToken = getenv('LINE_CHANNEL_ACCESSTOKEN');
$channelSecret = getenv('LINE_CHANNEL_SECRET');

$bot = new LINEBotTiny($channelAccessToken, $channelSecret);



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


//建立複數訊息的物件
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
	
}




foreach ($bot->parseEvents() as $event) {
		
    switch ($event['type']) {
		//收到訊息的動作
        case 'message':
			$message = $event['message'];
			
			//對訊息類別做篩選
            switch ($message['type']) {
				
				//只針對文字訊息去回應
                case 'text':
                	$m_message = $message['text'];
                	if($m_message!="")
                	{
						error_log("收到訊息：".$m_message);
						error_log("replyToken：".$event['replyToken']);
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
                
            }
            break;
			
		//被加入聊天室的動作
		case 'join':
			error_log("被加入聊天室");
			$messages = new MutiMessage();
			$replyArr = Array(
				$messages->text("大家好，我是擲骰機器狗。\n請輸入「骰子狗說明」獲得使用說明～"),
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
				$messages->text("你好哦，我是擲骰機器狗。\n請輸入「骰子狗說明」獲得使用說明。"),
				$messages->sticker(1,2),
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
	$replyKeyword = '骰子狗';
	error_log("訊息【".$inputStr."】進入parseInput");

	//preg_match ( "/A/" , B)。A是要比對的關鍵字（正則），B是被比對的字串
	if (preg_match ("/dvtest/i", $inputStr)){
		return DvTest ($inputStr);
		
	}else if (preg_match ("/^cc/i", $inputStr)){
		return CoC7th($inputStr);
		
	}else if(preg_match ("/^pb/i", $inputStr)){		
		return pbta($inputStr);
		
	}else if(stristr($inputStr,$replyKeyword) != false){
		return KeyWordReply($inputStr);	

	}else if(stristr(strtolower($inputStr),".jpg") != false){
		return SendImg($inputStr);
		
	}else if(preg_match ("/d/i", $inputStr) !=false){
		return nomalDiceRoller($inputStr);
	}
	
	
	else {
	return null;
	}
}


function DvTest ($inputStr){
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
	/*
	if(preg_match ("/muti|複數|多重/i", $inputStr) !=false){
	$testMessage = new MutiMessage();
	$replyArr = Array(
		$testMessage->text('多重訊息演示'),
		$testMessage->text('test2'),
		$testMessage->img('https://i.imgur.com/k4QE5Py.png'),
		$testMessage->sticker(1,2)
		);
	
	return $testMessage->send($replyArr);
	}*/
	
	return null;
}

