<?php

function bDice($inputStr){
	error_log("是【B骰】啦，媽ㄉ發科！");
	
	//先定義要輸出的Str
//先把這個打出來，然後在過程中一點一點把它補上去，大部分的思路是這樣的。
	$finalStr = "擴充擲骰：\n[";
	$inputStr = strtolower((string)$inputStr);
	
	if(preg_match ("/\d+b\d+/i", $inputStr) == false||
		preg_match ("/\./", $inputStr) != false){
		error_log("不符合骰子格式");
		return null;
	}
	
	preg_match ("/\S+/i", $inputStr , $matches);
	$DiceToRoll = $matches[0];
	error_log("擷取第一部分");
	
	if(preg_match ("/\d+b\d+/i", $DiceToRoll) == false||
			preg_match ("/\Db|b\D/i", $DiceToRoll) != false||
			preg_match ("/[^0-9bB+\-*\/()=><]/", $DiceToRoll) != false){
			error_log("取出值不符合骰子格式");
			return null;
		}
	
	$equa = null;
	$succesCum = 0;
	
	if(preg_match ("/[><]?[><=]\d+$/", $DiceToRoll, $matches) != false){
			$equa = $matches[0];
			if( stristr($equa,"=") != false && preg_match ("/>|</",$equa) == false){
					$equa = preg_replace("/=/" , "==" , $equa,1);
				}
		}
	
	
	while(preg_match ("/\d+b\d+/i", $DiceToRoll ,$matches) != false) {
		$tempMatch = (String)$matches[0];    
	
		$diceNum = explode('b',$tempMatch)[0];
		$diceSid = explode('b',$tempMatch)[1];
	
		for ($i = 1; $i <= $diceNum; $i++) {
			
			$diceEnd = Dice($diceSid);
			if ($equa != null){				
				
				error_log("$diceEnd"."$equa");			
				$answer = eval("return $diceEnd.$equa;");
			
				if($answer == true){$succesCum++;}
			}
			$finalStr = $finalStr.$diceEnd.'、';	
			
		}
	
		$DiceToRoll = preg_replace("/\d+b\d+/i" , 'Done' , $DiceToRoll,1);	
	}
	
	$finalStr = chop($finalStr,'、')."]";
	
	if($equa != null){
		$finalStr = $finalStr."\n→成功數：".$succesCum ;		
		}
	
	return buildTextMessage($finalStr);

}