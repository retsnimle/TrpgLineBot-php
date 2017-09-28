<?php
   
//PBTA判定在這裡
function pbta($inputStr){
  	error_log("是【PBTA】啦，媽ㄉ發科！");
	$inputStr = strtolower((string)$inputStr);
  
  //先把句首前面的一段拆出來。
	preg_match ("/\S+/i", $inputStr , $matchs);
	$input = $matchs[0];
	error_log("擷取第一部分：$input");
  //explode(' ',$input)[0]
  
  //同樣先處理報錯，先確定pb後面只有加或減
	if(preg_match ("/^pb[^+\-]/", $input) != false||
	preg_match ("/[^0-9pb+\-]/", $input) != false){

	error_log("取出值不符合骰子格式");
	return null;
	}	

  //把pb去掉，留下後面的+-值，處理報錯
	$bonus = str_ireplace("pb","",$input);
	if($bonus != '' && preg_match ("/-\d|\+\d/", $bonus) == false){return null;}
  
  //開始算咯，你看我們用到DiceCal.eq了吧
	$CalStr = DiceCal('2d6'.$bonus)['eq'];
    
	
    if (eval("return (String)$CalStr;") >= 10){      
      $finalStr="pbta擲骰（2D6".$bonus."）：\n".$CalStr.'='.eval("return (String)$CalStr;").'，成功！';
      }
    else if (eval("return (String)$CalStr;") <= 6){
      $finalStr="pbta擲骰（2D6".$bonus."）：\n".$CalStr.'='.eval("return (String)$CalStr;").'，失敗。';
      }    
    else {
      $finalStr="pbta擲骰（2D6".$bonus."）：\n".$CalStr.'='.eval("return (String)$CalStr;").'，部分成功。';
      }
 
	return buildTextMessage($finalStr);	
}