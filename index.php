<?php
$link=$_SERVER["HTTP_HOST"]."/s";
$gu="";$gn="";$info="";
class MyDB extends SQLite3
{
    function __construct()
    {
        $this->open('t.db');
    }
}

$db = new MyDB();








if (isset($_GET["i"])) {
  $surl=$_SERVER["REQUEST_URI"];
  $a=explode("/",$surl);$a=$a[count($a)-1];
  $a=rawurldecode($a);
  $result = $db->query("SELECT * FROM `url` where `n` = '$a'");

  while($r=$result->fetchArray()){

$db->query("INSERT INTO ip (`ip`,`time`,`code`) VALUES ('{$_SERVER["REMOTE_ADDR"]}','".time()."','$a')");
header("location:".$r["url"]);

$isserved=true;
}
if(!isset($isserved)){$info.="<span style='color:red'>Sorry, I'm either unable to find your desired short code or something went wrong!!!<br>Requested: <i>$a</i></span><br>";}

//echo $r["url"]."<br>".$a."<br>".$_GET["i"];
}


if(isset($_GET["url"])){

$db->query("INSERT INTO url (`url`,`n`,`time`) VALUES ('{$_GET["url"]}','{$_GET["n"]}','".time()."')");
if($db->lastErrorMsg()=="UNIQUE constraint failed: url.n"){

  $gu=$_GET["url"];$gn=$_GET["n"];
  $info.= "sorry <i>{$_GET["n"]}</i> already exist...";
}else{
$info.= "your short url= <a href='{$_GET["n"]}'>$link/{$_GET["n"]}</a><br>
your long url: {$_GET["url"]}
";

}
}


?>
<!DOCTYPE html>
<html><head>
<style>


  #mySidenav {
    height: 100%;
     width: 0;
     position: fixed;
     z-index: 1;
     top: 0;
     left: 0;
     background-color: #111;
     color: cyan;
     overflow-x: hidden;
     overflow-y: auto;

  }
  #main{
    margin-left:0;
  }
  .navbo{
    display:block;
  }

@media only screen and (min-width: 768px) {
  #mySidenav {

    height: 100%;
     width: 250px;
     position: fixed;
     z-index: 1;
     top: 0;
     left: 0;
     background-color: #111;
     overflow-x: hidden;
overflow-y: auto;

  }
#main{
  margin-left: 250px;
}
.navbo{
  display:none;
}
}

.form{
  margin-top: 10%;
  margin-left:10%;
  margin-right:10%;
  border: 5px solid green;
  text-align: center;
}
.form input{
  background: white;
  color:black;
}



</style></head>
<body>

<div id="mySidenav" id="mySidenav">
<form method="get">
<input type="text" name="iso" ><input type="submit" value="Search">
</form>
<?php
date_default_timezone_set('Asia/Dhaka');
if (isset($_GET["iso"])) {
  $iso=$_GET["iso"];
  echo "For <i>$iso</i> :<hr>";
  $unqv=[];
  $result = $db->query("SELECT ip FROM `ip` where `code` = '$iso'");

  while($r=$result->fetchArray()){

     array_push($unqv,$r["ip"]);


  }
  echo "Saved unique visitor: ".count(array_unique($unqv))."<hr>";

  $result = $db->query("SELECT count(*) FROM `ip` where `code` = '$iso'");
$r=$result->fetchArray();

echo "Saved total served: $r[0]";

$result2 = $db->query("SELECT * FROM `url` where `n` = '$iso'");
$rr=$result2->fetchArray();
echo "<hr>Long url: {$rr["url"]}<hr>";
if($rr["time"]!=""){echo "Created on: ".date("F j, Y, g:i a",$rr["time"])."<br><hr>";}
if ($r[0]>0) {
echo "<table border='1'><tr><th>ip</th><th>time</th></tr>";
  $result = $db->query("SELECT * FROM `ip` where `code` = '$iso' ORDER BY `time` DESC");
$ipcount=0;
  while($r=$result->fetchArray()){
    $ipcount++;
    if($ipcount>400){echo "After 400 records there may no display here.";break;}

  echo "<tr><td>{$r["ip"]}</td><td>".date("F j, Y, g:i a",$r["time"])."</td></tr>";


  }
echo "</table>";

}

}
?>
</div>
<div id="main">
<span class="navbo" style="font-size:30px;cursor:pointer" onclick="openNav()">☰</span>
<?php echo $info;?>
<form method="get" class="form">
  Your long url:<br>
  <input type="url" name="url" value="<?php echo $gu;?>" >
  <br><br>Your short code:<br>
  <input type="text" name="n" value="<?php echo $gn;?>">
  <br><br><input type="submit"><br><br>
</form>
<pre><?php
//print_r($_SERVER);
 ?></pre>
</div>
<script>
  var isopenn=false;
function openNav() {
  if(!isopenn){
    document.getElementById("mySidenav").style.width = "250px";
    document.getElementById("main").style.marginLeft = "250px";
    isopenn=true; console.log("open");
}
else{
  document.getElementById("mySidenav").style.width = "0";
  document.getElementById("main").style.marginLeft= "0";
  isopenn=false; console.log("closed");

}
}


</script>

</body>

</html>
