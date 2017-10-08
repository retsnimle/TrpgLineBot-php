<?php

function Dice($diceSided){
	return rand(1,$diceSided);
}

function nomalDiceRoller($inputStr){
	error_log("是【一般擲骰】啦，媽ㄉ發科！");
	
//先定義要輸出的Str
//先把這個打出來，然後在過程中一點一點把它補上去，大部分的思路是這樣的。
	$finalStr = '';
	$inputStr = strtolower((string)$inputStr);
	
	if(preg_match ("/\d+d\d+/i", $inputStr) == false||
		preg_match ("/\./", $inputStr) != false){
		error_log("不符合骰子格式");
		return null;
	}
	
	//抓第一部分出來
	preg_match ("/\S+/i", $inputStr , $matches);
	$mutiOrNot = $matches[0];
	error_log("擷取第一部分");
	
	//如果沒有非整數，就是複數擲骰。
	if(preg_match ("/\D/", $mutiOrNot) == false)  {		
		$finalStr= '複數擲骰';
		if((int)$mutiOrNot>20) return '不支援20次以上的複數擲骰。';
		
		//拆開第二部份
		$DiceToRoll  = explode(' ',$inputStr)[1];
		
		$finalStr= $finalStr."（".$DiceToRoll."）：";
		
		if(preg_match ("/\d+d\d+/i", $DiceToRoll) == false||
			preg_match ("/\Dd|d\D/i", $DiceToRoll) != false||
			preg_match ("/[^0-9dD+\-*\/()=><]/", $DiceToRoll) != false){
				
			error_log("取出值不符合骰子格式");
			return null;
		}
		
		for ($i=1 ; $i<=$mutiOrNot ;$i++){
			$finalStr = $finalStr."\n→".$i.'# '.DiceCal($DiceToRoll)['eqStr'];
		}
	
		 //報錯，不解釋。
		if(preg_match ("/200D/", $finalStr) != false){$finalStr = "複數擲骰：\n欸欸，不支援200D以上擲骰；哪個時候會骰到兩百次以上？想被淨灘嗎？";}
		if(preg_match ("/D500/", $finalStr) != false){$finalStr = "複數擲骰：\n不支援D1和超過D500的擲骰；想被淨灘嗎？";}
		
	}
	else {
		$DiceToRoll = $mutiOrNot;
		if(preg_match ("/\d+d\d+/i", $DiceToRoll) == false||
			preg_match ("/\Dd|d\D/i", $DiceToRoll) != false||
			preg_match ("/[^0-9dD+\-*\/()=><]/", $DiceToRoll) != false){
			error_log("取出值不符合骰子格式");
			return null;
		}
	
		$finalStr = "基本擲骰（".$mutiOrNot."）：\n→".DiceCal($mutiOrNot)['eqStr'];
	
	}
	
	return buildTextMessage($finalStr);	
}

//這就是作計算的函數，負責把骰子算出來。
function DiceCal($inputStr){
  
  //首先判斷是否是誤啟動（檢查是否有符合骰子格式）
  //你可能會想說上面不是檢查過了，但是因為在別的地方還機會呼叫所以不能省
	if(preg_match ("/\d+d\d+/i", $inputStr) == false||
		preg_match ("/\./", $inputStr) != false){
		error_log("不符合骰子格式");
		return null;
	}
  //一樣先定義要輸出的Str
	$equationStr = '' ;  
  
  //一般單次擲骰，先把字串讀進來轉小寫
	$DiceToRoll = strtolower((string)$inputStr);
  
  //再檢查一次
	if(preg_match ("/\d+d\d+/i", $DiceToRoll) == false||
		preg_match ("/\Dd|d\D/i", $DiceToRoll) != false||
		preg_match ("/[^0-9dD+\-*\/()=><]/", $DiceToRoll) != false){
		error_log("取出值不符合骰子格式");
		return null;
	}
  
  //寫出算式，這裡使用while將所有「幾d幾」的骰子找出來，一個一個帶入RollDice並取代原有的部分
  while(preg_match ("/\d+d\d+/i", $DiceToRoll ,$matches) != false) {
    $tempMatch = (String)$matches[0];    
    if (explode('d',$tempMatch)[0]>200){return Array('eqStr'=>'欸欸，不支援200D以上擲骰；哪個時候會骰到兩百次以上？想被淨灘嗎？');}
    if (explode('d',$tempMatch)[1]==1 || explode('d',$tempMatch)[1]>500){return Array('不支援D1和超過D500的擲骰；想被淨灘嗎？');}
    $DiceToRoll = preg_replace("/\d+d\d+/i" , RollDice($tempMatch) , $DiceToRoll,1);
  }
  
    //計算算式
  $answer = eval("return $DiceToRoll;");
  if(gettype($answer) == "boolean"){
	  if($answer == true){$answer ="true";}
	  else{$answer ="false";}
	  $equationStr= $DiceToRoll.'→'.$answer;
	}  
	else{  
	$equationStr= $DiceToRoll.' = '.$answer;
	}
  $Final =Array(
  'eq'=> $DiceToRoll,
  'eqStr'=>$equationStr
  );

  return $Final;
}

//用來把d給展開成算式的函數
function RollDice($inputStr){
  //先把inputStr變成字串（不知道為什麼非這樣不可）
  $comStr=strtolower((string)$inputStr);
  
  $finalArr = Array();
  $finalStr = '(';
  $diceNum = explode('d',$comStr)[0];
  $diceSid = explode('d',$comStr)[1];
  
  //接下來就是看有幾d幾，就要骰幾次骰，像是 3d6 就要做 3 次 Dice(6)
  for ($i = 1; $i <= $diceNum; $i++) {	  
	array_push($finalArr,Dice($diceSid));
   
  }
  
  $finalStr = "(".implode("+",$finalArr).")";
  
  return $finalStr;
}