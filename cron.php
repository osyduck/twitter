<?php
ini_set('max_execution_time',0);
function save($filename, $content)
	{
	    $save = fopen($filename, "a");
	    fputs($save, "$content\r\n");
	    fclose($save);
	}
function curl($target){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://itsjanu.com/twitter/retweet.php?target=".$target."&file=tweetid.txt");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$ex = curl_exec($ch);
		return $ex;
		}
$target = "";
$target = explode(",", $target);
//echo "BountyHive Special Bounty".PHP_EOL;
for($i=0; $i<count($target); $i++){
    $ekse = curl($target[$i]);
    
    save("loghivespecial.txt", $ekse);
    
    echo $ekse.PHP_EOL;
    sleep(2);
}

?>
