<?php     
//phpѹ��Ŀ¼��zip��     
//����:С��       
$button=$_POST['button'];     
if($button=="��ʼ���")     
{     
    $zip = new ZipArchive();     
    $filename = "./".date("Y-m-d")."_".md5(time())."_jackfeng.zip";     
    if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {     
        exit("�޷����� <$filename>\n");     
        }     
    $files = listdir();     
    foreach($files as $path)     
    {     
        $zip->addFile($path,str_replace("./","",str_replace("\\","/",$path)));    
    }    
    echo "ѹ����ɣ���ѹ����: " . $zip->numFiles . "���ļ�\n";    
    $zip->close();    
}    
Function listdir($start_dir='.') {    
  $files = array();    
  if (is_dir($start_dir)) {    
   $fh = opendir($start_dir);    
   while (($file = readdir($fh)) !== false) {    
     if (strcmp($file, '.')==0 || strcmp($file, '..')==0 || strpos($file, '.jpg')!=0 ) continue;    
     $filepath = $start_dir . '/' . $file;    
     if ( is_dir($filepath) )    
       $files = array_merge($files, listdir($filepath));    
     else    
       array_push($files, $filepath);    
   }    
   closedir($fh);    
  } else {    
   $files = false;    
  }    
 return $files;    
}    
?>    
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >     
<html>     
    <head>     
        <title>���ߴ������</title>     
        <meta http-equiv="Content-Type" content="text/html; charset=gb2312">    
    </head>    
    <body>    
        <form name="form1" method="post" action="">    
            <hr size="1">    
            <h3><a href="?">���ߴ������</a></h3>    
            <P> <input type="submit" name="button" value="��ʼ���" /></P>     
            <P>˵�����㿪ʼ�����֮�󣬾������ĵȴ��������ˣ�������վ�ļ����٣���Ҫ��ʱ����ܻ�ܳ���������֮��ѹ����������Ҫ�����վ��Ŀ¼�£���<span style='color:red;'>���ʱ��+����������ַ���+jackfeng.zip</span>�������������½ftp�����ء�</P>     
        </form>     
    </body>     
</html>