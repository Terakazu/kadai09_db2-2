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

 <form action="insert2.php" method="post" enctype="multipart/form-data" id="myForm">
    <label for="datepicker">select the date:</label>
    <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>">
    <textarea name="memo" cols="20" rows="10" placeholder="今日の出来事、あなたが感じたことなど自由に書いてください"></textarea>
    <input type="file" name="image" id="fileinput"> <!-- 画像をアップロードするためのinput -->
    <img id="preview" src="" style="display: none;"> 
    <div class="emotion_area" id="emotion_area">
        <p>自分の気持ちに近いものをクリックしてください。3つまで選択できます</p>
        <div class="selectemotion">
            <div class="emotion-checkbox">
                <input type="checkbox" name="emotion[]" value="満足"> 満足
                <input type="checkbox" name="emotion[]" value="感謝"> 感謝
                <input type="checkbox" name="emotion[]" value="嬉しい"> 嬉しい
                <input type="checkbox" name="emotion[]" value="ワクワク"> ワクワク<br>
                <input type="checkbox" name="emotion[]" value="好き"> 好き
                <input type="checkbox" name="emotion[]" value="感心"> 感心
                <input type="checkbox" name="emotion[]" value="面白い"> 面白い
                <input type="checkbox" name="emotion[]" value="楽しい"> 楽しい<br>
                <input type="checkbox" name="emotion[]" value="すっきり"> すっきり
                <input type="checkbox" name="emotion[]" value="ドキドキ"> ドキドキ
                <input type="checkbox" name="emotion[]" value="安心"> 安心
                <input type="checkbox" name="emotion[]" value="穏やか"> 穏やか<br>
                <input type="checkbox" name="emotion[]" value="普通"> 普通
                <input type="checkbox" name="emotion[]" value="退屈"> 退屈
                <input type="checkbox" name="emotion[]" value="もやもや"> もやもや
                <input type="checkbox" name="emotion[]" value="緊張"> 緊張<br>
                <input type="checkbox" name="emotion[]" value="不安"> 不安
                <input type="checkbox" name="emotion[]" value="悲しい"> 悲しい
                <input type="checkbox" name="emotion[]" value="疲れた"> 疲れた
                <input type="checkbox" name="emotion[]" value="イライラ"> イライラ<br>
            </div>
        </div>
    </div>
    <button class="send" type="submit" id="save3">送信する</button>
</form>

<script>
document.getElementById('myForm').addEventListener('submit', function(event) {
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
    document.getElementById('myForm').insertAdjacentHTML('beforeend', '<input type="hidden" name="emotion1" value="' + emotion1 + '">');
    document.getElementById('myForm').insertAdjacentHTML('beforeend', '<input type="hidden" name="emotion2" value="' + emotion2 + '">');
    document.getElementById('myForm').insertAdjacentHTML('beforeend', '<input type="hidden" name="emotion3" value="' + emotion3 + '">');
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

// "fileInput" の変更イベントを監視
$("#fileInput").on("change", async function() {
    // 選択されたファイルを取得
    const file = this.files[0];
    
    // ファイルをプレビューするための処理
    const reader = new FileReader();
    reader.onload = function(e) {
        // プレビュー用の要素に画像を表示
        $("#preview").attr("src", e.target.result);
    };
    reader.readAsDataURL(file);
    $("#preview").show();
});


</script>
</body>

</html>