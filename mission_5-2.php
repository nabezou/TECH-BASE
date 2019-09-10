<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>ミッション5-2</title>
    <meta name="description" content="入力・削除・編集フォーム">
  </head>
  <body>
    <h1>テーマ：知っているうまい店！</h1>
    <h5>なかったら何でもいいので書いて試してみて！</h5>
    <form action="mission_5-2.php" method="post">
      <p>名前：<br>
      <input type="text" name="name" value="名前">
      </p>
      <p>コメント：<br>
      <input type="text" name="comment" value="コメント">
      </p>
      <p>パスワード：<br>
      <input type="text" name="password" value="<?php if(isset($_POST['edit_password'])){echo $_POST['edit_password'];}else{echo 'パスワード';}?>">
      </p>
      <p><input type="hidden" name="edit" value="<?php if(isset($_POST['edit_number'])){echo $_POST['edit_number'];} ?>"></p>
      <p><input type="submit" value="送信"></p>
    </form>
    <form action="mission_5-2.php" method="post">
      <p>削除対象番号：<br>
      <input type="text" name="delete_number" value="番号">
      </p>
      <p>パスワード：<br>
      <input type="text" name="password" value="パスワード">
      </p>
      <p><input type="submit" value="削除"></p>
    </form>
    <form action"mission_5-2.php" method="post">
      <p>編集対象番号：<br>
      <input type="text" name="edit_number" value="番号">
      </p>
      <p>パスワード：<br>
      <input type="text" name="edit_password" value="パスワード">
      </p>
      <p><input type="submit" value="編集"></p>
    </form>
  </body>
</html>

<?php
//データベースへの接続
  //データベースの識別名の設定 (data source name)
  $dsn = 'データベース名';
  //ユーザ名、パスワードの設定
  $user = 'ユーザー名';
  $password = 'パスワード';
  //PHP data objectのインスタンス化 (エラーオプションの設定)
  $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
//テーブルの作成
  //SQL
  $sql = "CREATE TABLE IF NOT EXISTS board (id INT AUTO_INCREMENT PRIMARY KEY, name char(32), comment TEXT, datetime DATETIME, password TEXT);";
  //ステイトメント
  $stmt = $pdo -> query($sql);
//投稿フォーム
  if(isset($_POST['name'])){
    //名前の取得
    $name = $_POST['name'];
    //コメントの取得
    $comment = $_POST['comment'];
    //日時の取得
    $datetime = date("Y/m/d H:i:s");
    //パスワードの取得
    $password = $_POST['password'];
    //新規投稿モード
    if($_POST['edit'] == ""){
      //データベースへの書き込み
      //SQL
      $sql = "INSERT INTO board (name, comment, datetime, password) VALUES (:name, :comment, :datetime, :password)";
      //ステイトメント
      $stmt = $pdo -> prepare($sql);
      //名前
      $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
      //コメント
      $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
      //日時
      $stmt -> bindParam(':datetime', $datetime, PDO::PARAM_STR);
      //パスワード
      $stmt -> bindParam(':password', $password, PDO::PARAM_STR);
      //実行
      $stmt -> execute();
      //新規投稿完了
      echo "新規投稿が完了しました"."<br>";
      echo "<br>";
    }
    else{
      //編集
      //編集対象番号の取得
      $edit_number = $_POST['edit'];
      //編集対象番号が存在するか確かめる変数
      $scope = 0;
      //パスワードが正しいか確かめる変数
      $checker = 0;
      //idとpasswordの取得
      //SQL
      $sql = "SELECT id, password FROM board";
      //ステイトメント
      $stmt = $pdo -> query($sql);
      //情報の読み込み
      $info = $stmt -> fetchAll();
      //編集対象番号が存在するか確かめる
      foreach ($info as $row){
        if($edit_number === $row['id']){
          $scope = 1;
          if($password === $row['password']){
            $checker = 1;
          }
          break;
        }
      }
      //存在する場合
      if($scope === 1){
        //パスワードが正しい場合
        if($checker === 1){
          //SQL
          $sql = "UPDATE board set name=:name, comment=:comment, datetime=:datetime WHERE id=:id AND password LIKE :password";
          //ステイトメント
          $stmt = $pdo -> prepare($sql);
          //名前
          $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
          //コメント
          $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
          //日時
          $stmt -> bindParam(':datetime', $datetime, PDO::PARAM_STR);
          //id
          $stmt -> bindParam(':id', $edit_number, PDO::PARAM_INT);
          //パスワード
          $stmt -> bindParam(':password', $password, PDO::PARAM_STR);
          //実行
          $stmt -> execute();
          //編集完了
          echo "投稿番号".$edit_number."を編集しました"."<br>";
          echo "<br>";
        }
        //パスワードが違う場合
        else{
          echo "パスワードが違います"."<br>";
          echo "<br>";
        }
      }
      //存在しない場合
      else{
        echo "編集対象番号が存在しません"."<br>";
        echo "<br>";
      }
    }
    //テーブルの表示 (password以外)
    //SQL
    $sql = "SELECT id, name, comment, datetime FROM board";
    //ステイトメント
    $stmt = $pdo -> query($sql);
    //結果の読み込み
    $results = $stmt -> fetchAll();
    //表示
    foreach($results as $row){
      echo "投稿番号".$row['id'];
      echo " 名前".$row['name'];
      echo " コメント".$row['comment'];
      echo " 日時".$row['datetime']."<br>";
      echo "<hr>";
    }
  }

//削除フォーム
  else if(isset($_POST['delete_number'])){
    //削除
    //編集対象番号の読み込み
    $delete_number = $_POST['delete_number'];
    //パスワードの読み込み
    $password = $_POST['password'];
    //削除対象番号が存在するか確かめる変数
    $scope = 0;
    //パスワードが正しいか確かめる変数
    $checker = 0;
    //idとpasswordの取得
    //SQL
    $sql = "SELECT id, password FROM board";
    //ステイトメント
    $stmt = $pdo -> query($sql);
    //情報の読み込み
    $info = $stmt -> fetchAll();
    //削除対象番号が存在するか確かめる
    foreach ($info as $row){
      if($delete_number === $row['id']){
        $scope = 1;
        if($password === $row['password']){
          $checker = 1;
        }
        break;
      }
    }
    //存在する場合
    if($scope === 1){
      //パスワードが正しい場合
      if($checker === 1){
        //SQL
        $sql = "DELETE FROM board WHERE id=:id AND password LIKE :password";
        //ステイトメント
        $stmt = $pdo -> prepare($sql);
        $stmt -> bindParam(':id', $delete_number, PDO::PARAM_INT);
        $stmt -> bindParam(':password', $password, PDO::PARAM_STR);
        $stmt -> execute();
        //削除完了
        echo "投稿番号".$delete_number."を削除しました"."<br>";
        echo "<br>";
      }
      //パスワードが違う場合
      else{
        echo "パスワードが違います"."<br>";
        echo "<br>";
      }
    }
    //存在しない場合
    else{
      echo "削除対象番号が存在しません"."<br>";
      echo "<br>";
    }
    //テーブルの表示 (password以外)
    //SQL
    $sql = "SELECT id, name, comment, datetime FROM board";
    //ステイトメント
    $stmt = $pdo -> query($sql);
    //結果の読み込み
    $results = $stmt -> fetchAll();
    //表示
    foreach($results as $row){
      echo "投稿番号".$row['id'];
      echo " 名前".$row['name'];
      echo " コメント".$row['comment'];
      echo " 日時".$row['datetime']."<br>";
      echo "<hr>";
    }
  }

//編集フォーム
  else if(isset($_POST['edit_number'])){
    echo "編集内容を送信してください";
  }
?>