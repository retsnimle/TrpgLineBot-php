<?php
function CoC7th($inputStr){
	error_log("是【CoC7th】啦，媽ㄉ發科！");
	$inputStr = strtolower((string)$inputStr);
  
  //先判斷是不是要創角

  if (preg_match ("/創角|crt/", $inputStr) != false ){return ccCreate($inputStr);}
  
  //隨機產生角色背景
  if (preg_match ("/bg/", $inputStr) != false ){return ccbg();}
  
  
  
  //接下來就是主要的擲骰部分啦！
  //如果不是正確的格式，直接跳出
  
	if(preg_match ("/<=/", $inputStr) == false && preg_match ("/cc>/", $inputStr) == false){
		error_log("取出值不符合骰子格式");
		return null;
	}	
  
  
  //記錄檢定要求值，簡單來說就是取 = 後的「整數」部分，parseInt就是強制取整
	$chack = (int)explode("=",$inputStr)[1];
		
  
  //設定回傳訊息
	$ReStr = "CoC7th擲骰：\n(1D100<=".$chack.") → ";

  //先骰兩次十面骰作為起始值。為什麼要這樣呢，因為獎懲骰的部分十面骰需要重骰，這樣到時候會簡單一點。
	$TenRoll = Dice(10) ;
	$OneRoll = Dice(10) - 1;

  //把剛剛的十面骰組合成百面
	$firstRoll = $TenRoll*10 + $OneRoll;
	if ($firstRoll > 100) {$firstRoll = $firstRoll - 100;}

	
	//先設定最終結果等於第一次擲骰
	$finalRoll = $firstRoll;
          

  //判斷是否為成長骰
  if(preg_match ("/^cc>\d+/", $inputStr) != false){
	error_log("是成長骰啦，媽ㄉ發科！");	  
    $chack = (int)explode(">",$inputStr)[1];
	
    if ($finalRoll>$chack||$finalRoll>95) {
      $plus =  Dice(10);
      $ReStr = "CoC7th擲骰【技能成長】：\n(1D100>".$chack.") → ".$finalRoll." → 成功成長".$plus."點\n最終值為：".$chack."+".$plus."=".($chack + $plus);
      return buildTextMessage($ReStr);
    }
    else if ($finalRoll<=$chack) {
      $ReStr = "CoC7th擲骰【技能成長】：\n(1D100>".$chack.") → ".$finalRoll." → 沒有成長";
      return buildTextMessage($ReStr);
    }
    else return null;
  }


  //判斷是否為獎懲骰
  $BPDice = null;
  
  //if(inputStr.match(/^cc\(-?[12]\)/)!=null) BPDice = parseInt(inputStr.split("(",2)[1]) ;
	if(preg_match ("/^cc\(-?\d+\)/", $inputStr) != false){$BPDice = (int)explode("(",$inputStr)[1];}		
	if(abs($BPDice) != 1 && abs($BPDice) != 2 && $BPDice != null) {return buildTextMessage("CoC7th的獎懲骰，允許的範圍是一到兩顆哦。");}
  
  
  //如果是獎勵骰
	if($BPDice != null){  
		$tempStr = $firstRoll;
		
		for ( $i = 1; $i <= abs($BPDice); $i++ ){
			$OtherTenRoll = Dice(10);
			$OtherRoll = $OtherTenRoll.$OneRoll;
      		if ((int)$OtherRoll > 100) {$OtherRoll = (int)$OtherRoll - 100;}
      
		$tempStr = $tempStr."、".$OtherRoll;
		}
		
		$countArr = explode("、",$tempStr);
	
		if ($BPDice>0){
			$finalRoll = min($countArr);
			$ReStr = "CoC7th擲骰【獎勵骰取低】：\n(1D100<=".$chack.") → ";
		}
		if ($BPDice<0) {
			$finalRoll = max($countArr);
			$ReStr = "CoC7th擲骰【懲罰骰取高】：\n(1D100<=".$chack.") → ";
		}
		$ReStr = $ReStr.$tempStr." \n→ ";      
	}  

    //結果判定
	if ($finalRoll == 1){$ReStr = $ReStr.$finalRoll." → 恭喜！大成功！";}
	else
	if ($finalRoll == 100){$ReStr = $ReStr.$finalRoll." → 啊！大失敗！";}
	else
	if ($finalRoll <= 99 && $finalRoll > 95 && $chack < 50){$ReStr = $ReStr.$finalRoll." → 啊！大失敗！";}
    else
	if ($finalRoll <= $chack/5){$ReStr = $ReStr.$finalRoll." → 極限成功";}
	else
	if ($finalRoll <= $chack/2){$ReStr = $ReStr.$finalRoll." → 困難成功";}
	else
	if ($finalRoll <= $chack){$ReStr = $ReStr.$finalRoll." → 通常成功";}
	else  
	{$ReStr = $ReStr.$finalRoll." → 失敗" ;}

	//浮動大失敗運算
	if ($finalRoll <= 99 && $finalRoll > 95 && $chack >= 50 ){
		if($chack/2 < 50){$ReStr = $ReStr + "\n（若要求困難成功則為大失敗）";}
            else
              if(chack/5 < 50){$ReStr = $ReStr + "\n（若要求極限成功則為大失敗）";}
          }  
		  
		  
	if(stristr(strtolower($ReStr),"啊！大失敗") != false){
		$fumbleImgArr =Array(
			"https://i.imgur.com/ju9UQzA.png",
			"https://i.imgur.com/nWxGZyz.png",
			"https://i.imgur.com/cq0WGxH.png");
			
		$messages = new MutiMessage();
		
		$replyArr = Array(
			$messages->text($ReStr),
			$messages->img($fumbleImgArr[Dice(count($fumbleImgArr))-1])
		);
		
		return $messages->send($replyArr);
	}
	
	if(stristr(strtolower($ReStr),"恭喜！大成功") != false){
		$CriImgArr =Array(
			"https://i.imgur.com/jevHZqa.png");
			
		$messages = new MutiMessage();
		
		$replyArr = Array(
			$messages->text($ReStr),
			$messages->img($CriImgArr[Dice(count($CriImgArr))-1])
		);
		
		return $messages->send($replyArr);
	}

	
          return buildTextMessage($ReStr);	
}

function ccbg(){
	error_log("是ccbg啦，媽ㄉ發科！");
	
	$bg = '{
    "PD" :["結實的", "英俊的", "粗鄙的", "機靈的", "迷人的", "娃娃臉的", "聰明的", "蓬頭垢面的", "愚鈍的", "骯髒的", "耀眼的", "有書卷氣的","青春洋溢的","感覺疲憊的","豐滿的","粗壯的","毛髮茂盛的","苗條的","優雅的","邋遢的","敦實的","蒼白的","陰沉的","平庸的","臉色紅潤的","皮膚黝黑色","滿臉皺紋的","古板的","有狐臭的","狡猾的","健壯的","嬌俏的","筋肉發達的","魁梧的","遲鈍的", "虛弱的"],
    
    "IB" :["虔誠信仰著某個神祈","覺得人類不需要依靠宗教也可以好好生活","覺得科學可以解釋所有事，並對某種科學領域有獨特的興趣","相信因果循環與命運","是一個政黨、社群或秘密結社的成員","覺得這個社會已經病了，而其中某些病灶需要被剷除","是神秘學的信徒","是積極參與政治的人，有特定的政治立場","覺得金錢至上，且為了金錢不擇手段","是一個激進主義分子，活躍於社會運動"],

    "SP" :["他的父母", "他的祖父母", "他的兄弟姐妹", "他的孩子", "他的另一半", "那位曾經教導調查員最擅長的技能（點數最高的職業技能）的人","他的兒時好友", "他心目中的偶像或是英雄", "在遊戲中的另一位調查員", "一個由KP指定的NPC"],
    
    "SPW" :["調查員在某種程度上受了他的幫助，欠了人情","調查員從他那裡學到了些什麼重要的東西","他給了調查員生活的意義","調查員曾經傷害過他，尋求他的原諒","和他曾有過無可磨滅的經驗與回憶","調查員想要對他證明自己","調查員崇拜著他","調查員對他有著某些使調查員後悔的過往","調查員試圖證明自己和他不同，比他更出色","他讓調查員的人生變得亂七八糟，因此調查員試圖復仇"],
    
    "ML" :["過去就讀的學校","他的故鄉","與他的初戀之人相遇之處","某個可以安靜沉思的地方","某個類似酒吧或是熟人的家那樣的社交場所","與他的信念息息相關的地方","埋葬著某個對調查員別具意義的人的墓地","他從小長大的那個家","他生命中最快樂時的所在","他的工作場所"],
    
    "TP" :["一個與他最擅長的技能（點數最高的職業技能）相關的物品","一件他的在工作上需要用到的必需品","一個從他童年時就保存至今的寶物","一樣由調查員最重要的人給予他的物品","一件調查員珍視的蒐藏品","一件調查員無意間發現，但不知道到底是什麼的東西，調查員正努力尋找答案","某種體育用品","一把特別的武器","他的寵物"],
    
    "T" :["慷慨大方的人","對動物很友善的人","善於夢想的人","享樂主義者","甘冒風險的賭徒或冒險者", "善於料理的人", "萬人迷","忠心耿耿的人","有好名聲的人","充滿野心的人"]
    
	}';
	
	$bg = json_decode($bg, true);
	
	return buildTextMessage(
		"CoC7th背景描述生成器
（僅供娛樂用，不具實際參考價值）
==
調查員是一個".$bg["PD"][Dice(count($bg["PD"]))-1]."人。
【信念】：說到這個人，他".$bg["IB"][Dice(count($bg["IB"]))-1]."。
【重要之人】：對他來說，最重要的人是".$bg["SP"][Dice(count($bg["SP"]))-1]."，這個人對他來說之所以重要，是因為".$bg["SPW"][Dice(count($bg["SPW"]))-1]."。
【意義非凡之地】：對他而言，最重要的地點是".$bg["ML"][Dice(count($bg["ML"]))-1]."。
【寶貴之物】：他最寶貴的東西就是".$bg["TP"][Dice(count($bg["TP"]))-1]."。
【特徵】：總括來說，調查員是一個".$bg["T"][Dice(count($bg["T"]))-1]."。"
		);

	
}


function ccCreate($inputStr){
  //大致上的精神是，後面有數字就當作是有年齡調整的傳統創角，沒有的話就是常見的房規創角
  //如果最後面不是數字，就當作是常見的房規創角
	if (preg_match ("/\d+$/", $inputStr) == false ){
  
		$finalStr = "《悠子、冷嵐房規創角擲骰》\n==\n骰七次3D6取五次，\n決定STR、CON、DEX、APP、POW。\n";

		//DiceCal又被拿出來用了
		for ($i=1 ; $i<=7 ;$i++){
			$finalStr = $finalStr."\n".$i.'# '.DiceCal('3d6*5')['eqStr'];
		}

		$finalStr = $finalStr."\n==\n骰四次2D6+6取三次，\n決定SIZ、INT、EDU。\n";

		for ($i=1 ; $i<=4 ;$i++){
			$finalStr = $finalStr."\n".$i.'# '.DiceCal('(2d6+6)*5')['eqStr'];
		}

		$finalStr = $finalStr."\n==\n骰兩次3D6取一次，\n決定LUK。\n";
		for ($i=1 ; $i<=2 ;$i++){
			$finalStr = $finalStr."\n".$i.'# '.DiceCal('3d6*5')['eqStr'];
		} 

		return buildTextMessage($finalStr);
	}

	//這是傳統創角，要抓年齡出來做年齡調整值的
	if (preg_match ("/\d+$/", $inputStr, $matches) != false ){

    //讀取年齡

	$old = $matches[0];
	
    
    $ReStr = "《CoC7版核心規則創角擲骰》\n調查員年齡設為：".$old."\n";
    if ($old < 15){ return buildTextMessage($ReStr.'等等，核心規則不允許小於15歲的人物哦。');}    
    if ($old >= 90){ return buildTextMessage($ReStr.'等等，核心規則不允許90歲以上的人物哦。');} 
    

    //設定 因年齡減少的點數 和 EDU加骰次數，預設為零
    $AdjustValue = Array(
      'Debuff' => 0,
      'AppDebuff' => 0,
      'EDUinc' => 0
    );
        
    //這裡是不同年齡的資料
    $AdjustData = Array(
      'old' => Array(15,20,40,50,60,70,80),
      'Debuff' => Array(5,0,5,10,20,40,80),
      'AppDebuff' => Array(0,0,5,10,15,20,25),
      'EDUinc' => Array(0,1,2,3,4,4,4)
    );  

    for ( $i=0 ; $old >= $AdjustData['old'][$i] ; $i ++){    
      $AdjustValue['Debuff'] = $AdjustData['Debuff'][$i];
      $AdjustValue['AppDebuff'] = $AdjustData['AppDebuff'][$i];
      $AdjustValue['EDUinc'] = $AdjustData['EDUinc'][$i];      
    }

    $ReStr = $ReStr."==\n年齡調整：";
    
    if ($old < 20) {
      $ReStr = $ReStr."從STR、SIZ擇一減去".$AdjustValue['Debuff']."點
（請自行手動選擇計算）。
將EDU減去5點。LUK可擲兩次取高。" ;
    }
    else if ($old >= 40) {
	$ReStr = $ReStr.'從STR、CON或DEX中「總共」減去'.$AdjustValue['Debuff']."點
（請自行手動選擇計算）。
將APP減去".$AdjustValue['AppDebuff'].'點。可做'.$AdjustValue['EDUinc'].'次EDU的成長擲骰。' ;
    }
    else {
      $ReStr = $ReStr.'可做'.$AdjustValue['EDUinc'].'次EDU的成長擲骰。' ;
    }
    
    $ReStr = $ReStr."\n==";
    
    if ($old>=40){ $ReStr = $ReStr."\n（以下箭號三項，自選共減".$AdjustValue['Debuff'].'點。）' ;}
    if ($old<20){ $ReStr = $ReStr."\n（以下箭號兩項，擇一減去".$AdjustValue['Debuff'].'點。）' ;}    
    $ReStr = $ReStr."\nＳＴＲ：".DiceCal('3d6*5')['eqStr'];
    if ($old>=40){$ReStr = $ReStr.' ← 共減'.$AdjustValue['Debuff'] ;}
    if ($old<20){ $ReStr = $ReStr.' ←擇一減'.$AdjustValue['Debuff'] ;}
    $ReStr = $ReStr."\nＣＯＮ：".DiceCal('3d6*5')['eqStr'];
    if ($old>=40){ $ReStr = $ReStr.' ← 共減'.$AdjustValue['Debuff'];}
    $ReStr = $ReStr. "\nＤＥＸ：".DiceCal('3d6*5')['eqStr'];
    if ($old>=40){ $ReStr = $ReStr.' ← 共減'.$AdjustValue['Debuff'] ;}
    if ($old>=40){ $ReStr = $ReStr."\nＡＰＰ：".DiceCal('3d6*5-'.$AdjustValue['AppDebuff'])['eqStr'];}
    else{ $ReStr = $ReStr."\nＡＰＰ：".DiceCal('3d6*5')['eqStr'];}
    $ReStr = $ReStr."\nＰＯＷ：".DiceCal('3d6*5')['eqStr'];
    $ReStr = $ReStr."\nＳＩＺ：".DiceCal('(2d6+6)*5')['eqStr'];
    if ($old<20){ $ReStr = $ReStr.' ←擇一減'.$AdjustValue['Debuff'] ;}
    $ReStr = $ReStr."\nＩＮＴ：".DiceCal('(2d6+6)*5')['eqStr'];         
    if ($old<20){ $ReStr = $ReStr."\nＥＤＵ：".DiceCal('(2d6+6)*5-5')['eqStr'];}
    else {
		$ReStr = $ReStr."\n==";
      
		$firstEDU = DiceCal('(2d6+6)*5')['eq'];
		$tempEDU = eval("return $firstEDU;");
      
		$ReStr = $ReStr."\nＥＤＵ初始值：".$firstEDU.' = '.$tempEDU;

      for ($i = 1 ; $i <= $AdjustValue['EDUinc'] ; $i++){
        $EDURoll = Dice(100);
        $ReStr = $ReStr."\n第".$i.'次EDU成長 → '.$EDURoll;


        if ($EDURoll>$tempEDU) {
          $EDUplus = Dice(10);
          $ReStr = $ReStr.' → 成長'.$EDUplus.'點';
          $tempEDU = $tempEDU.$EDUplus;
        }
        else{
          $ReStr = $ReStr.' → 沒有成長';       
        }
      }
      $ReStr = $ReStr."\n";
      $ReStr = $ReStr."\nＥＤＵ最終值：".$tempEDU;
    }
    $ReStr = $ReStr."\n==";

    $ReStr = $ReStr."\nＬＵＫ：".DiceCal('3d6*5')['eqStr'];    
    if ($old<20){ $ReStr = $ReStr."\nＬＵＫ加骰：".DiceCal('3D6*5')['eqStr'];}


    return buildTextMessage($ReStr);
  } 
  
}