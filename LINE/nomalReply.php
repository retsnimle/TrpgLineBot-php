<?php

function KeyWordReply($inputStr) { 
	$inputStr = strtolower($inputStr);
	
	//讀入manual.json
	$handle = fopen("./ReplyJson/manual.json","r");	
	$content = "";
	while (!feof($handle)) {
		$content .= fread($handle, 10000);
		}
	fclose($handle);	
	$manual = json_decode($content, true);

	//一般功能說明
	if(stristr($inputStr, '說明') != false) {
		return buildTextMessage($manual[0]['說明']);
	}
	
			
	foreach($manual as $systems){
		foreach($systems['系統縮寫'] as $chack){
	
			if(stristr($inputStr, $chack) != false){
			return buildTextMessage($systems['說明']);
			break;
			}
		}
	}	
          
    //鴨霸獸幫我選～～
	if(stristr($inputStr, '選') != false||
		stristr($inputStr, '決定') != false||
		stristr($inputStr, '挑') != false) {
		
		$rplyArr = explode(' ',$inputStr);
    
		if (count($rplyArr) == 1) {return buildTextMessage('選擇的格式不對啦！');}
    
		$Answer = $rplyArr[Dice(count($rplyArr))-1];
		
		if(stristr($Answer, '選') != false||
		stristr($Answer, '決定') != false||
		stristr($Answer, '挑') != false||
		stristr($Answer, '骰子狗') != false) {
			$rplyArr = Array(
                 '人生是掌握在自己手裡的',
                 '每個都很好哦',
                 '不要把這麼重要的事情交給骰子狗決定比較好吧');
		$Answer = $rplyArr[Dice(count($rplyArr))-1];
		}
    return buildTextMessage('我想想喔……我覺得，'.$Answer.'。');
	}
	else    
	//以下是運勢功能
	if(stristr($inputStr, '運勢') != false){
		$rplyArr=Array('超大吉','大吉','大吉','中吉','中吉','中吉','小吉','小吉','小吉','小吉','凶','凶','凶','大凶','大凶','你還是，不要知道比較好','這應該不關我的事');
		return buildTextMessage('運勢喔…我覺得，'.$rplyArr[Dice(count($rplyArr))-1].'吧。');
	} 
	
    //以下是關鍵字回覆功能，檔案在 /ReplyJson/textReply.json
	//你也可以直接把json檔案在自己的dropboox之類的地方，用外聯的方式來鏈接
	
	//讀入json
	$handle = fopen("./ReplyJson/textReply.json","r");	
	$content = "";
	while (!feof($handle)) {
		$content .= fread($handle, 10000);
	}
	fclose($handle);	
	$content = json_decode($content, true);
		
	foreach($content as $txtChack){
		foreach($txtChack['chack'] as $chack){
	
			if(stristr($inputStr, $chack) != false){
			return buildTextMessage($txtChack['text'][Dice(count($txtChack['text'])-1)]);
			break;
			}
		}
	}
	
  //沒有觸發關鍵字則是這個
	
	$rplyArr = $content[0]['text'];
	return buildTextMessage($rplyArr[Dice(count($rplyArr))-1]);
	
}

//圖片關鍵字功能
function SendImg($inputStr) {
	
	//以下是關鍵字回覆功能，檔案在 /ReplyJson/imgReply.json
	//讀入json
	$handle = fopen("./ReplyJson/imgReply.json","r");	
	$content = "";
	while (!feof($handle)) {
		$content .= fread($handle, 10000);
		}
	fclose($handle);	
	$content = json_decode($content, true);	
	
	foreach($content as $ImgChack){
		foreach($ImgChack['chack'] as $chack){
			
			if(stristr($inputStr, $chack) != false){
			return buildImgMessage($ImgChack['img'][Dice(count($ImgChack['img'])-1)]);
			break;
			}
		}
	}
	
	return null;
}

//手機才看得到的訊息。
function mobile($inputStr) { 
		error_log("手機版專用訊息 ");
		if(stristr($inputStr, '系統說明mobile') != false){
			
			$message ='
			{
  "type": "template",
  "altText": "系統說明",
  "template": {
      "type": "carousel",
      "columns": [
          {
            "title": "《CoC7th 克蘇魯的呼喚》",
            "text": "本系統相關指令，關鍵字為 CC",
            "actions": [
                {
                    "type": "message",
                    "label": "系統指令說明",
                    "text": "骰子狗CC"
                },
                {
                    "type": "message",
                    "label": "獎懲骰範例",
                    "text": "CC(2)<=50 獎勵骰示範"
                },
                {
                    "type": "message",
                    "label": "技能成長範例",
                    "text": "CC>20 技能成長示範"
                }
            ]
          },
          {
			"title": "《PBTA系統》",
			"text": "本系統相關指令，關鍵字為 pb",
			"actions": [
				{
					"type": "message",
					"label": "系統指令說明",
					"text": "骰子狗pb"
				},
				{
					"type": "message",
					"label": "一般擲骰範例",
					"text": "pb 示範"
				},
				{
					"type": "message",
					"label": "調整值範例",
					"text": "pb+1 調整值示範"
				}
						
			]
		},
		{
			"title": "《附加功能》",
			"text": "附加功能相關指令，關鍵字為「骰子狗」以及 .jpg 和 (ry",
			"actions": [
				{
					"type": "message",
					"label": "附加功能指令說明",
					"text": "骰子狗其他說明"
				},
				{
					"type": "message",
					"label": "隨機選擇範例",
					"text": "骰子狗，請幫我選宵夜要吃 鹽酥雞 滷味 吃p吃，不准吃"
				},
				{
					"type": "message",
					"label": "圖片回應範例",
					"text": "我覺得不行.jpg"
				}
						
			]
		}
      ]
  }
}';
			$message = json_decode($message , true);
			$send = new MutiMessage();
			$replyArr = Array($message);
			
			return $send->send($replyArr );
		}
}