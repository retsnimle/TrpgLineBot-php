<?php

function KeyWordReply($inputStr) { 
	$inputStr = strtolower($inputStr);
	
	//讀入manual.json
	$handle = fopen("./manual.json","r");	
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
    
		if (count($rplyArr) == 1) {return buildTextMessage('靠腰喔要我選也把選項格式打好好不好，真的想被淨灘嗎？');}
    
		$Answer = $rplyArr[Dice(count($rplyArr))-1];
		
		if(stristr($Answer, '選') != false||
		stristr($Answer, '決定') != false||
		stristr($Answer, '挑') != false||
		stristr($Answer, '鴨霸獸') != false) {
			$rplyArr = Array('幹，你不會自己決定嗎',
                 '人生是掌握在自己手裡的',
                 '隨便哪個都好啦',
                 '連這種東西都不能決定，是不是不太應該啊',
                 '沒事別叫我選東西好嗎，難道你們都是天秤座嗎（戰）',
                 '不要把這種東西交給機器人決定比較好吧');
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
	
    //以下是幫眾限定的垃圾話
	
	//讀入json
	$handle = fopen("https://www.dropbox.com/s/yfja6psf977i7hi/textReply.json?dl=1","r");	
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

function SendImg($inputStr) {
	
	//讀入json
	$handle = fopen("https://www.dropbox.com/s/mccm8n29ul5xkr5/Imgs.json?dl=1","r");	
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

function Yababang($inputStr) {
	$rplyArr = explode(' ',$inputStr);
	$pl = $rplyArr[1];
	if (count($rplyArr) == 1) {return buildTextMessage('想要挑戰入幫測驗，就把格式打好啊幹！');}
	if(stristr($pl, 'yabaso') != false||
		stristr($pl, '巴獸') != false||
		stristr($pl, '鴨巴') != false||
		stristr($pl, '幫主') != false||
		stristr($pl, '泰瑞') != false||
		stristr($pl, '鴨霸獸') != false||
		stristr($pl, '鴨嘴獸') != false) 
		{return buildTextMessage('幫主好！幹，那邊那個菜比巴，看到幫主不會敬禮啊，想被淨灘是不是？！');}
  
	//關卡設定
  	//讀入json
	$handle = fopen("https://www.dropbox.com/s/cd6u3ljpsril7if/yababang.json?dl=1","r");	
	$content = "";
	while (!feof($handle)) {
		$content .= fread($handle, 10000);
		}
	fclose($handle);	
	
	$challenge = json_decode($content, true);

  
  
  //開始迴圈部分
  
	$stage = 1;
	$DeadOrNot = 0;
	$pinch = $challenge[0]['pinch'] + $challenge[0]['pinchRan'];
	$reply = '本次入幫測驗挑戰者是【'.$pl.'】，鴨霸幫萬歲！';
  
	for (; $DeadOrNot == 0; $stage++){
		$reply = $reply."\n\n================\n【".$pl.'挑戰第'.$stage."關】\n" ;
    
		if(Dice(100) <= $pinch){
		$reply = $reply.$challenge[$stage]['good'][Dice( count($challenge[$stage]['good']))-1];
		$pinch = $pinch - Dice($challenge[0]['pinchDe']);
		}
		else {
		$reply = $reply.$challenge[$stage]['bad'][Dice( count($challenge[$stage]['bad']))-1];
		$DeadOrNot = 1;
		$reply = $reply."\n\n================\n勝敗乃兵家常事，大俠請重新來過吧。\n或者你可以直接月付1999加入白銀幫眾。";
		}
    
    if ($stage ==5 && $DeadOrNot == 0) {
    $DeadOrNot = 2 ;    
    }
  }
  
  if ($DeadOrNot == 2) $reply = $reply."\n\n================\n恭喜【".$pl."】成功存活，成為新一代的鴨霸幫幫眾。\n請到隔壁的櫃檯繳納會費，然後期待下一次淨灘的時候你還可以存活下來。";
      
  return buildTextMessage($reply);
}