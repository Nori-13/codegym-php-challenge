<?php
//4. $_POSTにデータが含まれている＄_GETにはデータが含まれていない（なぜならばformのmethod=postだから）
session_start();

//ログインしていない場合、login.phpを表示
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once('db.php');
require_once('functions.php');

/**
 * @param String $tweet_textarea
 * つぶやき投稿を行う。
 */
function newtweet($tweet_textarea)
{
  // 汎用ログインチェック処理をルータに作る。早期リターンで
    createTweet($tweet_textarea, $_SESSION['user_id']); //関数の呼び出し
}

function newReplyTweet($tweet_textarea, $reply_post_id)
{
    createReTweet($tweet_textarea, $reply_post_id, $_SESSION['user_id']);
}
//functionで名前決め、functionを呼び出す（いいね機能）→どんな引数を渡すか→SQLを使う
// 8.newfavorite関数が呼び出される
function newFavorite($post_id)
{
    $member_id = $_SESSION['user_id'];
// 9.functions.phpに書いてあるcreateFavoriteが実行される
    createFavorite($member_id, $post_id);//$_SESSION['user_id']);
}
//todo: member_idとpost_idを渡していいねを消す処理を作る
//todo: 関数を埋める 
function eraseFavorite($post_id)
{
    $member_id = $_SESSION['user_id'];
    deleteFavorite($member_id, $post_id);
}
/**
 * ログアウト処理を行う。
 */
function logout()
{
    $_SESSION = [];
    $msg = 'ログアウトしました。';
}
// 5.function xxx()関数の定義なので処理が飛ばされ、if文の処理が始まる
// 6. $_POSTに値があるので、if文に入る（※４番参照）
if ($_POST) { /* POST Requests */
    var_dump($_POST);
    if (isset($_POST['logout'])) { //ログアウト処理
        logout();
        header("Location: login.php");
    } else if (isset($_POST['tweet_textarea'])) { //投稿処理
        if (isset($_POST['reply_post_id'])) {
            newReplyTweet($_POST['tweet_textarea'], $_POST['reply_post_id']);
        } else {
            newtweet($_POST['tweet_textarea']);
            header("Location: index.php");
        }
    // 7.$_POSTに●●というkeyが入るので、if文のnewFavoriteが実行される
    // todo：　投稿idの有無を確認する
    } else if (isset($_POST['post_id'])) {
        if (isset($_POST['nice_button'])) {
            newFavorite($_POST['post_id']); //いいねを新しく作る
        } else {
            eraseFavorite($_POST['post_id']);
            //いいねを消す
            // todo: 引数を考える
        }
    }
}




$tweets = getTweets();
$tweet_count = count($tweets);
/* 返信課題はここからのコードを修正しましょう。 */
function getUserName($post_id) {
    $sql = 'select u.name ';
    $sql .= ' from tweets t join users u on t.user_id = u.id';
    $sql .= ' where t.id =' . "$post_id";
    $stmt = getPdo()->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $name = $result['name'];/* $post_idを元にユーザー名を取得する処理を記載しましょう。 */
    return $name;
}

function getUserReplyText($post_id) {
    //「Re: @名前」の文字列を作りましょう。
    return "Re: @" . getUserName($post_id) . ' ';
}
/* 返信課題はここからのコードを修正しましょう。 */
?>

<!DOCTYPE html>
<html lang="ja">

<?php require_once('head.php'); ?>

<body>
  <div class="container">
    <h1 class="my-5">新規投稿</h1>
    <div class="card mb-3">
      <div class="card-body">
        <form method="POST">
          <textarea class="form-control" type=textarea name="tweet_textarea" ><?php if (isset($_GET['reply'])) { 
            echo getUserReplyText($_GET['reply']);
          } ?></textarea>
          <!-- 返信課題はここからのコードを修正しましょう。 -->
          <?php if (isset($_GET['reply'])) { ?>
            <input type="hidden" name="reply_post_id" value="<?= "{$_GET['reply']}" ?>"/>
          <?php } ?>
          <!-- 返信課題はここからのコードを修正しましょう。 -->
          <br>
          <input class="btn btn-primary" type=submit value="投稿">
        </form>
      </div>
    </div>
    <h1 class="my-5">コメント一覧</h1>
    <?php foreach ($tweets as $t) { ?>
      <div class="card mb-3">
        <div class="card-body">
          <p class="card-title"><b><?= "{$t['id']}" ?></b> <?= "{$t['name']}" ?> <small><?= "{$t['updated_at']}" ?></small></p>
          <p class="card-text"><?= "{$t['text']}" ?></p>
          
          <!--返信課題はここから修正しましょう。-->
          <?php if (isset($t['reply_id'])) { ?>
            <p><a href="index.php?reply=<?= "{$t['id']}" ?>">[返信する]</a>  <a href="/view.php?id=<?= "{$t['reply_id']}" ?>">[返信元のメッセージ]</a></p>
          <?php } else { ?>
            <p><a href="index.php?reply=<?= "{$t['id']}" ?>">[返信する]</a></p>
          <?php } ?>
          <!--返信課題はここまで修正しましょう。-->
          <!--ハートを押すとPOSTメゾットでデータ送信-->
          <!-- todo. post_idに投稿id を入れる -->
        <form method="POST" name="like_form">
          <input type="hidden" name="post_id" value="<?= "{$t['id']}" ?>"/>
          <!--input type="submit" name="送信"/ -->
          <?php if (hasFavorite($t['id'],$_SESSION['user_id']) ==0) { ?>
          <!-- 1.いいねボタンを押す　2.name=like_formのformが実行される 3.index.phpが再読み込みされる（なぜならばformにactionが指定されていないから-->
            <input type="hidden" name="nice_button"/>
            <a href="#" onclick="this.parentNode.submit()"><img class="favorite-image" src='/images/heart-solid-gray.svg'></a>
          <?php } else { ?>
            <input type="hidden" name="delete_button"/>
            <a href="#" onclick="this.parentNode.submit()"><img class="favorite-image" src='/images/heart-solid-red.svg'></a>
          <?php } ?>
          <?php echo getFavoriteUsers($t['id']) ?> <!-- 下にいいねの数値を取る todo -->
          </form> 
        
        
        </div>
      </div>
    <?php } ?>
    <form method="POST">
      <input type="hidden" name="logout" value="dummy">
      <button class="btn btn-primary">ログアウト</button>
    </form>
    <br>
  </div>
</body>

</html>
