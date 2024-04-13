<?php
//1. POSTデータ取得
$date = $_POST["date"];
$memo = $_POST["memo"];
$emotion1 =$_POST["emotion1"];
$emotion2 =$_POST["emotion2"];
$emotion3 =$_POST["emotion3"];


// 画像ファイルの取得
$image_path = $_FILES["image"]["tmp_name"]; // 一時ファイルのパス
$image_name = $_FILES["image"]["name"]; // アップロードされたファイルの名前
$upload_path = "images/" . $image_name; // 画像を保存するパス（適宜変更してください）

// 画像を指定のパスに移動します
move_uploaded_file($image_path, $upload_path);


// 2. DB接続します
include("funcs.php");
$pdo=db_conn();



//３．データ登録SQL作成
$sql = "INSERT INTO jd_an_table2(memo,emotion1,emotion2,emotion3,date,image_path)VALUES(:memo,:emotion1,:emotion2,:emotion3, :date,  :image_path);";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':memo', $memo, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':emotion1', $emotion1, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':emotion2', $emotion2, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':emotion3', $emotion3, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':date', $date, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':image_path', $upload_path, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$status = $stmt->execute();

//４．データ登録処理後
if($status==false){
  //*** function化する！*****************

  sql_error($stmt);
}else{
  //*** function化する！*****************
 redirect("index5.php");
  
}
?>
