<?php
$id =$_GET["id"];
//１．PHP

include("funcs.php");
$pdo = db_conn();

//２．データ登録SQL作成
$stmt = $pdo->prepare("DELETE FROM jd_an_table2 WHERE id=:id");
$stmt->bindValue(':id',  $id, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
$status = $stmt->execute();

//３．データ表示
$values = "";
if($status==false) {
  sql_error($stmt);
}

//４．データ登録処理後
if($status==false){
    sql_error($stmt);
  }else{
    redirect("select4.php");
  }
  ?>
