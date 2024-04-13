<?php
session_start();
    
include("funcs.php");
$pdo=db_conn();

//２．データ登録SQL作成
$sql="SELECT * FROM jd_an_table2 ORDER BY date DESC";
$stmt = $pdo->prepare($sql);
$status = $stmt->execute();

//３．データ表示
$values="";
if($status==false) {
 sql_error($stmt);
}



//全データ取得
$values =  $stmt->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_ASSOC[カラム名のみで取得できるモード]
//JSONに値を渡す場合に使う
$json = json_encode($values,JSON_UNESCAPED_UNICODE);

?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="js/jquery-2.1.3.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.2.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
<title>Journaling Daiary読み込み</title>
<link rel="stylesheet" href="css/sample2.css">

</head>

<body>
<!-- Head[Start] -->
<header>
    <!-- Head[Start] -->
  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        <!-- <?=$_SESSION["name"]?>さん、こんにちは！
      <a class="navbar-brand" href="index.php">ジャーナリングを書く</a>
      <a class="navbar-brand" href="logout.php">ログアウト</a> -->
      </div>
    </div>
  </nav>
<!-- Head[End] -->
        <h1><i>Journaling Diary</i></h1>
</header>
<!-- Head[End] -->

<!-- Main[Start] -->
<h3>感情の変化</h3>
<canvas id="myChart"></canvas>

<h3>日記</h3>
<div id="cardContainer">

    </div>
</div>

 
</div>


<!-- Main[End]  -->

<script>
  //JSON受け取り
const obj = <?=$json?>;
console.log(obj);



// **********************日記カード部分************************
// ***********日付をフォーマットする関数*************
function formatDate(dateString) {
        const date = new Date(dateString);
        const year = date.getFullYear();
        const month = date.getMonth() + 1;
        const day = date.getDate();
        const dayOfWeek = ['日', '月', '火', '水', '木', '金', '土'][date.getDay()];
        return `${year}年${month}月${day}日(${dayOfWeek})`;
    }
// ****************日付終わり***************
// カードを生成する関数
function createCard(data) {
  const cardContainer = document.createElement('div');
        cardContainer.classList.add('cardContainer');

        const formattedDate = formatDate(data.date);

        // 日付要素を生成
    const dateElement = document.createElement('div');
    dateElement.classList.add('date');
    dateElement.setAttribute('data-date', data.date);
    dateElement.textContent = formattedDate;

    // cardContainerに日付要素を追加
    cardContainer.appendChild(dateElement);

        const card = document.createElement('div');
        card.classList.add('card');
      

    // emotion1, emotion2, emotion3 の値が null でない場合にのみ要素を生成する
    const emotionElements = [];
    if (data.emotion1 !== "null") {
        emotionElements.push(`<div class="emotion">${data.emotion1}</div>`);
    }
    if (data.emotion2 !== "null") {
        emotionElements.push(`<div class="emotion">${data.emotion2}</div>`);
    }
    if (data.emotion3 !== "null") {
        emotionElements.push(`<div class="emotion">${data.emotion3}</div>`);
    }
    
    // emotionElements が空でない場合にのみ emotion-wrapper を生成する
    const emotionWrapper = emotionElements.length > 0 ? `<div class="emotion-wrapper">${emotionElements.join('')}</div>` : '';

 
    
    card.innerHTML = `
        <div class="card-content">
            <div class="memo">${data.memo}</div>
            <div class="date">${data.date}</div>
            ${emotionWrapper} <!-- emotion2 と emotion3 の要素を含む emotion-wrapper -->
       
          <button class="links" onclick="location.href='detail.php?id=${data.id}'">更新</button>
          <button class="links" onclick="location.href='delete.php?id=${data.id}'">削除</button>

        </div>
        ${data.image_path ? `<img src="${data.image_path}">` : ''} <!-- 条件分岐 -->
    `;
   
    return card;
}


  // カードを表示する
const cardContainer = document.getElementById('cardContainer');

// 日付ごとにカードをグループ化するためのオブジェクト
const dateGroups = {};

// カードをグループ化する
obj.forEach(data => {
    const formattedDate = formatDate(data.date);

    // 日付をキーとして、カードをグループに追加する
    if (!dateGroups[formattedDate]) {
        dateGroups[formattedDate] = [];
    }
    dateGroups[formattedDate].push(createCard(data));
});

// グループごとにカードを表示する
Object.keys(dateGroups).forEach(date => {
    const dateElement = document.createElement('div');
    dateElement.classList.add('date');
    dateElement.textContent = date;

    // 日付要素をカードコンテナに追加
    cardContainer.appendChild(dateElement);

    // 日付ごとのカードをカードコンテナに追加
    dateGroups[date].forEach(card => {
        cardContainer.appendChild(card);
    });
});

// ******************グラフ表示の計算***************************************
  

  const selectedEmotions = [];
  const dates=[];
  let totalScore = 0;

  //感情のスコア
// 1.各感情のスコアを定義する
const emotionScores = {
    "満足": 5,
    "感謝": 5,
    "嬉しい": 5,
    "ワクワク":5,
    "好き":3,
    "感心":3,
    "面白い":3,
    "楽しい":3,
    "すっきり":1,
    "ドキドキ":1,
    "安心":1,
    "穏やか":1,
    "普通":-1,
    "退屈":-1,
    "もやもや":-1,
    "緊張":-1,
    "不安":-3,
    "悲しい":-3,
    "疲れた":-3,
    "イライラ":-3,
};

// 選択された感情から合計スコアを計算する関数
function calculateTotalScore(selectedEmotions) {
    let totalScore = 0;
    selectedEmotions.forEach(emotion => {
        // 感情が定義されているかを確認してからスコアを加算する
        if (emotionScores[emotion] !== undefined && emotionScores[emotion] !== null) {
            totalScore += emotionScores[emotion];
        } else {
            // 感情が定義されていない場合やnullの場合は0を加算する
            totalScore += 0;
        }
    });
    return totalScore;
}



// 各カードの感情を配列に変換して合計スコアを計算する
obj.forEach(item => {
    const emotionsArray = [item.emotion1];
    if (item.emotion2 !== null) {
        emotionsArray.push(item.emotion2);
    }
    if (item.emotion3 !== null) {
        emotionsArray.push(item.emotion3);
    }
    const score = calculateTotalScore(emotionsArray); // 合計スコアを計算
    console.log('カードの感情:', emotionsArray);
    console.log('合計スコア:', score);
});

// 各カードの感情を配列に変換して合計スコアを計算する
const scoresByDate = {}; // 日付ごとのスコアを格納するオブジェクト

obj.forEach(item => {
    const emotionsArray = [item.emotion1];
    if (item.emotion2 !== null) {
        emotionsArray.push(item.emotion2);
    }
    if (item.emotion3 !== null) {
        emotionsArray.push(item.emotion3);
    }
    const score = calculateTotalScore(emotionsArray); // 合計スコアを計算
    console.log(score);

    // 日付ごとのスコアをオブジェクトに追加
    if (!scoresByDate[item.date]) {
        scoresByDate[item.date] = score;
    } else {
        scoresByDate[item.date] += score;
    }
});


// 日付ごとの最大スコアを計算する関数
function calculateMaxScoresByDate() {
    const maxScoresByDate = {};
    obj.forEach(item => {
        const date = item.date; // 日付を取得
        const emotionsArray = [item.emotion1];
        if (item.emotion2 !== null && emotionScores[item.emotion2] !== undefined) {
            emotionsArray.push(item.emotion2);
        }
        if (item.emotion3 !== null && emotionScores[item.emotion3] !== undefined) {
            emotionsArray.push(item.emotion3);
        }
        const totalScore = emotionsArray.reduce((acc, emotion) => acc + emotionScores[emotion], 0);
        if (!maxScoresByDate[date] || maxScoresByDate[date] < totalScore) {
            maxScoresByDate[date] = totalScore;
        }
    });
    return maxScoresByDate;
}

// 日付ごとの最大スコアを取得
const maxScoresByDate = calculateMaxScoresByDate();
console.log(maxScoresByDate);




// グラフ作成のためのデータ配列を初期化
const graphData = [];

// グラフデータの作成
for (const date in maxScoresByDate) {
    if (Object.hasOwnProperty.call(maxScoresByDate, date)) {
        graphData.push({ date: date, score: maxScoresByDate[date] });
    }
}

// グラフデータを日付でソート
graphData.sort((a, b) => new Date(a.date) - new Date(b.date));

// グラフ用のデータを準備
const dates2 = Object.keys(scoresByDate).reverse(); // 日付の配列を逆順にする
const maxScores = dates2.map(date => maxScoresByDate[date]); // 逆順になった日付に対応する最大スコアを取得

// Chart.jsを使用して折れ線グラフを描画
const ctx = document.getElementById('myChart').getContext('2d');
const myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: dates2, // 逆順の日付
        datasets: [{
            label: '感情スコア',
            data: maxScores, // 逆順の最大スコア
            borderColor: 'rgba(255, 99, 132)',
            backgroundColor:['rgba(255, 99, 132, 0.2)'],
            borderWidth: 2,
            fill: true, // グラフの下側を塗りつぶす
            lineTension: 0.4 // 曲線の滑らかさを設定
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});


</script>

</body>
</html>













<!-- //     function renderRightDiary(card) {
//         const rightDiary = document.querySelector('.rightdiary');
//         rightDiary.innerHTML = '';
//         if (card) {
//             rightDiary.appendChild(card.cloneNode(true));
//         }
//     }

//     const leftDiary = document.querySelector('.leftdiary');
//     const rightDiary = document.querySelector('.rightdiary');
//     let selectedCard = null; // 選択されたカードを保持する変数

//     leftDiary.addEventListener('click', function(event) {
//         const card = event.target.closest('.card');
//         if (card) {
//             if (card === selectedCard) {
//                 selectedCard.classList.remove('selected');
//                 selectedCard = null;
//             } else {
//                 if (selectedCard) {
//                     selectedCard.classList.remove('selected');
//                 }
//                 card.classList.add('selected');
//                 selectedCard = card;
//             }
//             renderRightDiary(selectedCard);
//         }
//     });

//     // 左側の日記エリアがクリックされたときに処理を実行
// leftDiary.addEventListener('click', function(event) {
//     const card = event.target.closest('.card');
//     if (card) {
//         // 選択されたカードを右側の日記エリアにコピーして表示
//         renderRightDiary(card.cloneNode(true));
//     }
// });

// $(function() {
//   $(".card").click(function(){
//     // クリックされた要素の背景色を赤色に設定
//     $(this).css('background-color', 'red');
    
//     // 他のすべての .card 要素の背景色を元に戻す
//     $(".card").not(this).css('background-color', ''); // 空文字列を指定してデフォルトの色に戻す
    
//     // クリックされたカードを#cardContainerにコピーして表示する
//     const clonedCard = $(this).clone(); // クリックされたカードを複製
//     $('#rightdiary').empty().append(clonedCard); // #cardContainerをクリアして、クローンしたカードを挿入
//   });
// });


// ******************カード生成*********************

    // function createCard(data) {
    //     const cardContainer = document.createElement('div');
    //     cardContainer.classList.add('cardContainer');
    //     const formattedDate = formatDate(data.date);

    //     if (!document.querySelector(`.cardContainer .date[data-date="${data.date}"]`)) {
    //         cardContainer.innerHTML += `<div class="date" data-date="${data.date}">${formattedDate}</div>`;
    //     }

    //     console.log("Inside createCard function");
    //     console.log(data);
    //     const card = document.createElement('div');
    //     card.classList.add('card');
    //     // const emotionsArray = data.emotion.split('、');
    //     // const emotionsHTML = emotionsArray.map(emotion => `<div class="emotion">${emotion}</div>`).join('');
    

    //     let imageHTML = '';
    //     if (data.image_path) {
    //         imageHTML = `<img src="${data.image_path}">`;
    //     }

    //     let memoContent = data.memo;
    //     if (memoContent.length > 150) {
    //         memoContent = memoContent.substring(0, 70) + '...';
    //     }

    //     card.innerHTML = `
    //         <div class="card-content">
    //             <div class="memo">${memoContent}</div>
    //             <div class="date">${formattedDate}</div>
    //             // <div class="emotion-wrapper">${emotionsHTML}</div>
    //             <div class="links">
    //             // <a href="detail.php?id=${data.id}">更新</a>
    //             // <a href="delete.php?id=${data.id}">削除</a>
    //         </div>
    //         </div>
    //         ${imageHTML}
    //     `;


    //     cardContainer.appendChild(card);
    //     return cardContainer;
    // }

    // const cardContainer = document.getElementById('cardContainer');
    // obj.forEach(data => {
    //     const card = createCard(data);
    //     console.log(card);
    //     cardContainer.appendChild(card);
    // });
// **************カード生成終わり****************

    // 初期表示時にも右側の日記エリアに選択されたカードを表示
    // renderRightDiary(selectedCard);










 </script>
</body>
</html>
  --> 