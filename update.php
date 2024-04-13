<?php

// 1. 削除ボタンが押されたかどうかを検出
if (isset($_POST['delete_image_button'])) {
  // 2. image_pathをデータベースから削除
  $id = $_GET["id"];
  include("funcs.php");
  $pdo = db_conn();

  // 画像パスをデータベースから削除するSQL文を作成
  $sql_delete_image = "UPDATE jd_an_table2 SET image_path = NULL WHERE id=:id";
  $stmt = $pdo->prepare($sql_delete_image);
  $stmt->bindValue(':id', $id, PDO::PARAM_INT);
  $stmt->execute();
  
  // 3. ユーザーに削除が完了したことを通知するメッセージを表示
  echo "画像が削除されました。";
  
  
 // 4. detail.phpにリダイレクト
 header("Location: detail.php?id=" . $id);
 exit; // スクリプトの実行を終了
}

// 更新処理
if (isset($_POST['update_button'])) {
  // 画像削除フラグがセットされているかを確認し、セットされていない場合は元の画像パスを保持
  if (!isset($_POST['delete_image_flag'])) {
    // 元の画像パスを取得
    $id = $_POST["id"];
    include("funcs.php");
    $pdo = db_conn();
    $sql_get_image_path = "SELECT image_path FROM jd_an_table2 WHERE id=:id";
    $stmt = $pdo->prepare($sql_get_image_path);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $original_image_path = $stmt->fetchColumn();
    // 画像パスがセットされていない場合は、フォームからの値を代入
    $_POST['image_path'] = empty($_POST['image_path']) ? $original_image_path : $_POST['image_path'];
}



//1. POSTデータ取得
$date = $_POST["date"];
$memo = $_POST["memo"];
$emotion1 =$_POST["emotion1"];
$emotion2 =$_POST["emotion2"];
$emotion3 =$_POST["emotion3"];

// 画像をアップロードする
$upload_dir = "images/"; // 画像を保存するディレクトリ
$image_path = $upload_dir . $image_name; // アップロードされた画像の保存先パス

// 画像を指定のディレクトリに移動します
if (move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
    echo "画像のアップロードが完了しました。";
} else {
    echo "画像のアップロードに失敗しました。";
}





// //2. DB接続します
// //*** function化する！  *****************
// include("funcs.php");
// $pdo=db_conn();



//３．データ登録SQL作成
$sql ="UPDATE jd_an_table2 SET memo=:memo,emotion1=:emotion1,emotion2=:emotion2,emotion3=:emotion3,date=:date,image_path=:image_path WHERE id=:id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':memo', $memo, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':emotion1', $emotion1, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':emotion2', $emotion2, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':emotion3', $emotion3, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':date', $date, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':image_path', $upload_path, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':id',     $id,     PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)

$status = $stmt->execute();


//４．データ登録処理後
if($status==false){
    //*** function化する！*****************
  
    sql_error($stmt);
}else{
    //*** function化する！*****************
   redirect("select4.php");
} 
}
?>