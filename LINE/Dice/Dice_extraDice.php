<?php

function bDice($inputStr){
	error_log("是【B骰】啦，媽ㄉ發科！");
	
	//先定義要輸出的Str
//先把這個打出來，然後在過程中一點一點把它補上去，大部分的思路是這樣的。
	$finalStr = "擴充擲骰（";
	$inputStr = strtolower((string)$inputStr);
	
	if(preg_match ("/\d+b\d+/i", $inputStr) == false||
		preg_match ("/\./", $inputStr) != false){
		error_log("不符合骰子格式");
		return null;
	}
	
	preg_match ("/\S+/i", $inputStr , $matches);
	$DiceToRoll = $matches[0];
	error_log("擷取第一部分");
	
	$finalStr = $finalStr.$DiceToRoll."）：\n→";
	
	//處理加骰符號
	preg_match ("/\[(.*?)]/i", $DiceToRoll , $matches);
	$bonusEqra = $matches[0];
	$DiceToRoll = preg_replace("/\[(.*?)]/i" , "" , $DiceToRoll);
	$bonusEqra = preg_replace("/\[/" , "" , $bonusEqra);
	$bonusEqra = preg_replace("/]/" , "" , $bonusEqra);
		
	if(preg_match ("/[^0-9=><]/i", $bonusEqra) != false){$bonusEqra='';}
	if( stristr($bonusEqra,"=") != false && preg_match ("/>|</",$bonusEqra) == false){
		$bonusEqra = preg_replace("/=/" , "==" , $bonusEqra,1);
	}
	if( preg_match ("/>|<|=/",$bonusEqra) == false && $bonusEqra != null){
		$bonusEqra = "==".$bonusEqra;
	}
	
	error_log("加骰值：".$bonusEqra);	
	
	
	
	
	if(preg_match ("/\d+b\d+/i", $DiceToRoll) == false||
			preg_match ("/\Db|b\D/i", $DiceToRoll) != false||
			preg_match ("/[^0-9bB+\-*\/()=><]/", $DiceToRoll) != false){
			error_log("取出值不符合骰子格式：".$DiceToRoll);
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
		
		
	//開始骰第一次
	$countArr = rollBDice($DiceToRoll,$equa,$bonusEqra);
	error_log("最後加骰值：".$countArr[2]);
	$finalStr = $finalStr.$countArr[0];
	$succesCum = $succesCum + $countArr[1];
	
	$reDice = 0;
	//準備第二次以上
	while($countArr[3]!= 0) {
		$finalStr = $finalStr."[加骰".$countArr[3]."次]";
		$countArr = rollBDice($countArr[2],$equa,$bonusEqra);
		$finalStr = $finalStr."\n→".$countArr[0];
		$succesCum = $succesCum + $countArr[1];	
		
		if($reDice>=100){
			$finalStr = "迴圈執行數到達100，可能為無限迴圈，強制停止。";
			$equa = null;
			$bonusEqra = null;
			break;}
		
		$reDice++;
			
	}
	
	
	if($equa != null ||$bonusEqra != null){
		$finalStr = $finalStr."\n→總成功數：".$succesCum ;		
		}
	
	return buildTextMessage($finalStr);

}


//骰B骰用的
function rollBDice($DiceToRoll,$equa,$bonusEqra){
	$succesCum = 0;
	$bouns = '';
	$bounsCum = 0;
	
	if ($equa == null && $bonusEqra != null){
		$equa = $bonusEqra;
	}
	
	//$finalStr = $finalStr."[";
	while(preg_match ("/\d+b\d+/i", $DiceToRoll ,$matches) != false) {
		$tempMatch = (String)$matches[0];    
	
		$diceNum = explode('b',$tempMatch)[0];
		$diceSid = explode('b',$tempMatch)[1];
		
		if($diceNum >= 200 ){return Array("不支援200顆以上的擲骰。",0,null,0);}

		
		for ($i = 1; $i <= $diceNum; $i++) {
			
			$diceEnd = Dice($diceSid);
			
			//計算成功數
			if ($equa != null){				
				//error_log("$diceEnd"."$equa");			
				$answer = eval("return $diceEnd.$equa;");			
				if($answer == true){$succesCum++;}
			}
			
			//計算加骰條件
			if ($bonusEqra != null){				
				//error_log("$diceEnd"."$equa");
				$answer = eval("return $diceEnd.$bonusEqra;");			
				if($answer == true){				
				$bouns = $bouns."1b".$diceSid."+";	
				$bounsCum++;
				}
			}		
			
			$finalStr = $finalStr.$diceEnd.'、';	
			
		}
	
		$DiceToRoll = preg_replace("/\d+b\d+/i" , 'Done' , $DiceToRoll,1);	
	}
	
	$finalStr = chop($finalStr,'、');
	$finalArr = explode('、',$finalStr);
	rsort($finalArr);
	
	$finalStr = "[";
	
	foreach ($finalArr as $i){
		$finalStr = $finalStr.$i."、";
	}
	
	$finalStr = chop($finalStr,'、')."]";
	
	$bouns = chop($bouns,'+');
	
	return Array($finalStr,$succesCum,$bouns,$bounsCum);
}


