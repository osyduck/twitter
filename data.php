<?php
$config = explode("\n", file_get_contents("config.txt"));
?>
<html>
<body><center>
<h1>Twitter Data Report</h1>
<h3>This is a data report of twitter <?php echo $config[0]; ?></h3>
<h1>Data:</h1>
<table border="1">
<tr><td>No</td><td>Tweet</td><td>Username<td>Date</td></tr>
<?php
$data = file_get_contents("alllogretweet.txt");
$pilah = explode("\n", $data);
for($i=0; $i<count($pilah)-1; $i++){
$anu = explode(" ", $pilah[$i]);
$user = explode("https://twitter.com/", $pilah[$i]);
$user = explode("/", $user[1]);
$date = $anu[2]." ".$anu[3]." ".$anu[4]." ".$anu[5]." ".$anu[6];
//print_r($anu).PHP_EOL;
$a = $i+1;
	echo '<tr><td>'.$a.'<td><a href="'.$anu[0].'">'.$anu[0].'</a></td><td>@'.$user[0].'<td>'.$date.'</td></tr>';
	

}
?>
</center>
</table>
</body>
</html>
