<?php
set_time_limit(0);
date_default_timezone_set("Asia/Bangkok");
ignore_user_abort(1);
ini_set('max_execution_time',0);
class janbot{
	public $consumer_key;
	public $consumer_secret;
	public $oauth_token;
	public $oauth_token_secret;
	public $type;
	public $jumlah;
	public $target;
	public $file;
	function __construct($c_key, $c_secret, $o_token, $o_token_secret) {
	$this->consumer_key=$c_key;
	$this->consumer_secret=$c_secret;
	$this->oauth_token=$o_token;
	$this->oauth_token_secret=$o_token_secret;}
	public function save($filename, $content)
	{
	    $save = fopen($filename, "a");
	    fputs($save, "$content\r\n");
	    fclose($save);
	}
	
	public function tanggal($jam=false)
	{
		switch (ceil(date('m'))) {
			case 1:
				$bln = "January";
				break;
			case 2:
				$bln = "February";
				break;
			case 3:
				$bln = "March";
				break;
			case 4:
				$bln = "April";
				break;
			case 5:
				$bln = "May";
				break;
			case 6:
				$bln = "June";
				break;
			case 7:
				$bln = "July";
				break;
			case 8:
				$bln = "August";
				break;
			case 9:
				$bln = "September";
				break;
			case 10:
				$bln = "October";
				break;
			case 11:
				$bln = "November";
				break;
			case 12:
				$bln = "December";
				break;
		}
		$seminggu = array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
		$hari = $seminggu[date('w')];
		$tglnow = date('d'); $tahun = date('Y');
		if($jam === true){
			$returnkan = $hari.", ".$tglnow." ".$bln." ".$tahun." , ".date('H:i:s');
		}else{ 
			$returnkan = $hari.", ".$tglnow." ".$bln." ".$tahun;
		}
		return date("G:i:s ").$returnkan;
	}
	
    public function sambung(){
        require_once("../include/twitteroauth.php");
        $con = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $this->oauth_token, $this->oauth_token_secret);
        return $con;
    }
	public function target(){
		$koneksi = $this->sambung();
        $hasil = $koneksi->get('users/lookup', array('screen_name'=> $this->target));
        return $hasil[0]->id_str;
	}
	
	public function dapetin_hasil(){
        $koneksi = $this->sambung();
		$ids = $this->target();
        $hasil = $koneksi->kntl('statuses/user_timeline', array('user_id'=> $ids, 'count'=> $this->jumlah));
        return $hasil;
    }
	
	public function retweet(){
	   $koneksi = $this->sambung();
		$list_cari = $this->dapetin_hasil();
		//print_r($list_cari);
		$id0 = $list_cari[0]['id_str'];
		$id1 = $list_cari[1]['id_str'];
	if(preg_match("/$id0/", file_get_contents($this->file)) != false)
	{
	    if(preg_match("/$id1/", file_get_contents($this->file)) != false)
	    {
	        echo $id1." Already Retweeted".PHP_EOL;
	    }
	    elseif(preg_match("/$id1/", file_get_contents($this->file)) == false)
	    {
	        $x=$koneksi->post('statuses/retweet/'.$list_cari[1]['id_str']);
	        $y=$koneksi->post('favorites/create', array('id' => $list_cari[1]['id_str']));
    		$tweetid = $x->id_str;
    		$text = $x->text;
    		$this->save($this->file, "https://twitter.com/".$this->target."/status/".$list_cari[1]['id_str']." on ".$this->tanggal());
    		echo "https://twitter.com/".$this->target."/status/".$list_cari[1]['id_str']." on ".$this->tanggal();
	    }
	    echo $id0." Already Retweeted".PHP_EOL;
	}
	else
	{
	    $x=$koneksi->post('statuses/retweet/'.$list_cari[0]['id_str']);
		$y=$koneksi->post('favorites/create', array('id' => $list_cari[0]['id_str']));
		$tweetid = $x->id_str;
		$text = $x->text;
	    $this->save($this->file, "https://twitter.com/".$this->target."/status/".$list_cari[0]['id_str']." on ".$this->tanggal());
		echo "https://twitter.com/".$this->target."/status/".$list_cari[0]['id_str']." on ".$this->tanggal();
	}
	
	}    
	
	public function post_retweet(){
		$koneksi = $this->sambung();
		$list_cari = $this->dapetin_hasil();
		//print_r($list_cari);
		if(in_array(0, array_keys($list_cari)) != false){
		    $id = $list_cari[1]['id_str'];
		    if(preg_match("/$id/", file_get_contents($this->file))){
		        echo "Already Retweeted ".$id;
		    }else{
		$x=$koneksi->post('statuses/retweet/'.$list_cari[1]['id_str']);
		$y=$koneksi->post('favorites/create', array('id' => $list_cari[1]['id_str']));
		$tweetid = $x->id_str;
		$text = $x->text;
		$this->save("tweetid.txt", $list_cari[1]['id_str']." on ".date("G:i:s d-m-Y"));
		echo $tweetid." ".$text;	
		    }	
		}else{
		    $id = $list_cari[0]['id_str'];
		    if(preg_match("/$id/", file_get_contents("tweetid.txt"))){
		        echo "Already Retweeted ".$id;
		    }else{
		$x=$koneksi->post('statuses/retweet/'.$list_cari[0]['id_str']);
		$y=$koneksi->post('favorites/create', array('id' => $list_cari[0]['id_str']));
		$tweetid = $x->id_str;
		$text = $x->text;
		$this->save("tweetid.txt", $list_cari[0]['id_str']." on ".date("G:i:s d-m-Y"));
		echo $tweetid." ".$text;
		}
		}
	}
}
if(empty($_GET['target'])){
    echo "Target is Empty";
}else{
$data = explode("\n", file_get_contents("config.txt"));

$haq=new janbot('RZjE8JmQ3FTmGN6xA2OqtaEem','OGXyXkbnu6vwpH04ajqjxzdif9dw6Qidies76ELLquyAb3PCtN',$data[1],$data[2]); //di ganti
$haq->target= $_GET['target'];
$haq->jumlah= "2";
$haq->file = "alllogretweet.txt";
$haq->retweet();
}
