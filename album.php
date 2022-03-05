<html>
<body>
<form action="album.php" method="post" enctype="multipart/form-data">
  Submit this file:
  <input type="file" name="fileToUpload" id="fileToUpload">
  <input type="submit" value="Send File" name="submit">
</form>
<pre>
<?php
$target_dir = "uploads/";
$auth_token = 'FlrtRmkUTJ4AAAAAAAAAARSdIAlWbz-7xWxqs6vrXubbeO317esj8YkS1aPWf_CZ';
// set it to true to display debugging info
$debug = true;
function download ( $path, $target_path ) {
   global $auth_token, $debug;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $auth_token,
      		    'Content-Type:', 'Dropbox-API-Arg: {"path":"/'.$path.'"}'));
   curl_setopt($ch, CURLOPT_URL, 'https://content.dropboxapi.com/2/files/download');
   try {
     $result = curl_exec($ch);
   } catch (Exception $e) {
     echo 'Error: ', $e->getMessage(), "\n";
   }
   file_put_contents($target_path,$result);
   curl_close($ch);
}
function upload ( $path ) {
   global $auth_token, $debug;
   $args = array("path" => $path, "mode" => "add");
   $fp = fopen("uploads/".$path, 'rb');
   $size = filesize("uploads/".$path);
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_PUT, true);
   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $auth_token,
   		     'Content-Type: application/octet-stream',
		     'Dropbox-API-Arg: {"path":"/'.$path.'", "mode":"add"}'));
   curl_setopt($ch, CURLOPT_URL, 'https://content.dropboxapi.com/2/files/upload');
   curl_setopt($ch, CURLOPT_INFILE, $fp);
   curl_setopt($ch, CURLOPT_INFILESIZE, $size);
   try {
     $result = curl_exec($ch);
   } catch (Exception $e) {
     echo 'Error: ', $e->getMessage(), "\n";
   }
   if ($debug)
      //print_r($result);
   curl_close($ch);
   fclose($fp);
}
function directoryList ( $path ) {
   global $auth_token, $debug;
   $args = array("path" => $path);
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $auth_token,
   		    'Content-Type: application/json'));
   curl_setopt($ch, CURLOPT_URL, 'https://api.dropboxapi.com/2/files/list_folder');
   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($args));
   try {
     $result = curl_exec($ch);
   } catch (Exception $e) {
     echo 'Error: ', $e->getMessage(), "\n";
   }
   if ($debug)
      //print_r($result);
   $array = json_decode(trim($result), TRUE);
   if ($debug)
      //print_r($array);
   curl_close($ch);
   return $array;
}
function delete ( $path ) {
   global $auth_token, $debug;
   $args = array("path" => "/".$path);
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $auth_token,
      		    'Content-Type: application/json'));
   curl_setopt($ch, CURLOPT_URL, 'https://api.dropboxapi.com/2/files/delete_v2');
   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($args));
   try {
      echo "inside delete method";
      echo $args;
      echo json_encode($args);
     $result = curl_exec($ch);
   } catch (Exception $e) {
     echo 'Error: ', $e->getMessage(), "\n";
   }
   if ($debug)
      //print_r($result);
   curl_close($ch);
}
// upload a file
if(isset($_POST["submit"])) {
   $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
   move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
   $nameOfFile = $_FILES["fileToUpload"]["name"];
   upload($nameOfFile);
}
// list top directory
$result = directoryList("");
foreach ($result['entries'] as $x) {
?>
<a href ='album.php?input=<?php echo $x['name'];?>'><?php echo $x['name'];?></a>
<a href='album.php?remove=<?php echo $x['name'];?>'><button>Delete</button></a><br/>
<?php
}
// download a file
if (isset($_GET['input'])) {
   //$imdDir = "images/" . $_GET['input'];
   $imdDir = "images/tmp.jpg";
   $imgvalue = "/".$_GET['input'];
   download($_GET['input'], $imdDir);
   echo "<img src =$imdDir width=100px height=100px/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";	
 }
if (isset($_GET['remove'])) {
   delete($_GET['remove']);
   header("Location: album.php");
 }
?>
</pre>
</body>
</html>
