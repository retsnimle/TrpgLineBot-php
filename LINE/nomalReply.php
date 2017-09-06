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
