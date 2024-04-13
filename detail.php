<?php
$id =$_GET["id"];
//１．PHP

include("funcs.php");
$pdo = db_conn();

//２．データ登録SQL作成
$sql = "SELECT * FROM jd_an_table2 WHERE id=:id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id',  $id, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
$status = $stmt->execute();

//３．データ表示
$values = "";
if($status==false) {
  sql_error($stmt);
}

//1行のデータ取得
$v    =  $stmt->fetch(); //PDO::FETCH_ASSOC[カラム名のみで取得できるモード]
// var_dump($v);
// $json = json_encode($values,JSON_UNESCAPED_UNICODE);


?>
<!--
２．HTML
以下にindex.phpのHTMLをまるっと貼り付ける！
理由：入力項目は「登録/更新」はほぼ同じになるからです。
※form要素 input type="hidden" name="id" を１項目追加（非表示項目）
※form要素 action="update.php"に変更
※input要素 value="ここに変数埋め込み"
-->
<!doctype html>
<html lang="ja">

<head>
    <meta charset="utf-8" />
    <script src="js/jquery-2.1.3.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.2.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <link rel="stylesheet" href="css/sample2.css">
    <title>ジャーナリングアプリ</title>
    <style>
        body {
            background-image: url(img/background.png);
            font-family:Georgia, 'Times New Roman', Times, serif
            }
        </style>

</head>

<body>
    <header>
        <h1><i>Journaling Diary</i></h1>
    </header>

    <main>
        <!-- インプット画面 -->
        <!-- <button id="save2">SAVE</button>
        <button id="clear">CLEAR</button> -->
        <!-- <button id="load">読み込み</button> -->
        
            <div class="diary_area" id="diary_area">
                <div class="diary">
                    <!-- <h2>Diary</h2> -->
                    <p>今日はどんな1日でしたか？<br>
                心に残ったことを書きとめましょう</p>
                    <button id="showView" onclick="window.location.href='https://kekechama515.sakura.ne.jp/php02/select4.php'">過去の日記を見る</button><br>
                  

            
                <!-- 日付を入れる -->
            <form id="myForm" action="update.php?id=<?= $id ?>"  method="post" enctype="multipart/form-data">
                <input type="hidden" name="id">
                <label for="datepicker">select the date:</label>
                <input type="date" name="date" value="<?=h($v["date"])?>">
                <textarea name="memo" cols="20" rows="10"><?=h($v["memo"])?></textarea>
                <input type="file" name="image" > <!-- 画像をアップロードするためのinput -->
                <img src="<?= $v['image_path'] ?>"
                <input type="hidden" name="delete_image" value="<?=h($v["image_path"])?>"> <!-- 画像削除のためのフラグ -->
               <button type="submit" name="delete_image_button">写真を削除</button> <!-- 写真削除ボタン -->
          
            
              </div>
            </div>

         <div class="emotion_area" id="emotion_area">
            <!-- <h2>How are you feeling?</h2> -->
            <!-- <h3>selected emotion</h3><br> -->
            <p>自分の気持ちに近いものをクリックしてください。3つまで選択できます</p>
                
 
<div class="selectemotion">
    <div class="emotion-checkbox">
    <input type="checkbox" name="emotion[]" value="満足" <?php if (in_array('満足', $v)) echo 'checked'; ?>> 満足
    <input type="checkbox" name="emotion[]" value="感謝" <?php if (in_array('感謝', $v)) echo 'checked'; ?>> 感謝
    <input type="checkbox" name="emotion[]" value="嬉しい" <?php if (in_array('嬉しい', $v)) echo 'checked'; ?>> 嬉しい
    <input type="checkbox" name="emotion[]" value="ワクワク" <?php if (in_array('ワクワク',$v)) echo 'checked'; ?>> ワクワク
    <input type="checkbox" name="emotion[]" value="好き" <?php if (in_array('好き', $v)) echo 'checked'; ?>> 好き
    <input type="checkbox" name="emotion[]" value="感心" <?php if (in_array('感心', $v)) echo 'checked'; ?>> 感心
    <input type="checkbox" name="emotion[]" value="面白い" <?php if (in_array('面白い', $v)) echo 'checked'; ?>> 面白い
    <input type="checkbox" name="emotion[]" value="楽しい" <?php if (in_array('楽しい', $v)) echo 'checked'; ?>> 楽しい
    <input type="checkbox" name="emotion[]" value="すっきり" <?php if (in_array('すっきり', $v)) echo 'checked'; ?>>すっきり
    <input type="checkbox" name="emotion[]" value="ドキドキ" <?php if (in_array('ドキドキ', $v)) echo 'checked'; ?>> ドキドキ
    <input type="checkbox" name="emotion[]" value="安心" <?php if (in_array('安心', $v)) echo 'checked'; ?>> 安心
    <input type="checkbox" name="emotion[]" value="穏やか" <?php if (in_array('穏やか', $v)) echo 'checked'; ?>> 穏やか
    <input type="checkbox" name="emotion[]" value="普通" <?php if (in_array('普通', $v)) echo 'checked'; ?>> 普通
    <input type="checkbox" name="emotion[]" value="退屈" <?php if (in_array('退屈', $v)) echo 'checked'; ?>> 退屈
    <input type="checkbox" name="emotion[]" value="もやもや" <?php if (in_array('もやもや', $v)) echo 'checked'; ?>> もやもや
    <input type="checkbox" name="emotion[]" value="緊張" <?php if (in_array('緊張', $v)) echo 'checked'; ?>> 緊張
    <input type="checkbox" name="emotion[]" value="不安" <?php if (in_array('不安', $v)) echo 'checked'; ?>> 不安
    <input type="checkbox" name="emotion[]" value="悲しい" <?php if (in_array('悲しい', $v)) echo 'checked'; ?>> 悲しい
    <input type="checkbox" name="emotion[]" value="疲れた" <?php if (in_array('疲れた', $v)) echo 'checked'; ?>> 疲れた
    <input type="checkbox" name="emotion[]" value="イライラ" <?php if (in_array('イライラ', $v)) echo 'checked'; ?>> イライラ
    </div>
</div>
            <input type="hidden" name="id" value="<?=h($v["id"])?>"> 
            <button type="submit" name="update_button">更新</button> <!-- 更新ボタン -->
            </form>
      
    </main>
    <footer><small>Journaling Diary</small></footer>


<script>
   // ページが読み込まれた後に実行されるコード
document.addEventListener('DOMContentLoaded', function() {
    // フォーム要素を取得
    var form = document.getElementById('myForm');

    // フォームのsubmitイベントに対するイベントリスナーを追加
    form.addEventListener('submit', function(event) {
        // フォームが送信される前にemotion1, emotion2, emotion3に値を振り分ける
        var emotions = document.querySelectorAll('input[name="emotion[]"]:checked');
        var emotion1 = null, emotion2 = null, emotion3 = null;

        emotions.forEach(function(emotion, index) {
            if (index === 0) {
                emotion1 = emotion.value;
            } else if (index === 1) {
                emotion2 = emotion.value;
            } else if (index === 2) {
                emotion3 = emotion.value;
            }
        });

        // 振り分けた値をhiddenフィールドに設定する
        form.insertAdjacentHTML('beforeend', '<input type="hidden" name="emotion1" value="' + emotion1 + '">');
        form.insertAdjacentHTML('beforeend', '<input type="hidden" name="emotion2" value="' + emotion2 + '">');
        form.insertAdjacentHTML('beforeend', '<input type="hidden" name="emotion3" value="' + emotion3 + '">');
    });
});


// チェックボックス選択は3つまで
const checkMax = 3;
const checkBoxes = document.getElementsByName('emotion[]');

function checkCount(target) {
  let checkCount = 0;
  checkBoxes.forEach(checkBox => {
    if (checkBox.checked) {
      checkCount++;
    }
  });
  if (checkCount > checkMax) {
    alert('最大3つまでしか選択できません');
    target.checked = false;
  }
}

checkBoxes.forEach(checkBox => {
  checkBox.addEventListener('change', () => {
    checkCount(checkBox);
  })
});


document.addEventListener("DOMContentLoaded", function() {
    const submitButton = document.getElementById('save3'); // 送信ボタンを取得
    
    // 送信ボタンがクリックされたときにチェックを行う
    submitButton.addEventListener('click', function(event) {
        const checkboxes = document.querySelectorAll('.emotion-checkbox input[type="checkbox"]');
        let checked = false;
        
        // チェックされたチェックボックスが少なくとも1つあるかどうかを確認
        checkboxes.forEach(function(checkbox) {
            if (checkbox.checked) {
                checked = true;
            }
        });
        
        // チェックされたチェックボックスが1つもない場合、アラートを表示してフォームの送信をキャンセル
        if (!checked) {
            event.preventDefault(); // フォームの送信をキャンセル
            alert('感情を1つ以上選択してください');
        }
    });
});



</script>
</body>

</html>