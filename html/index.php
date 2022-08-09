<?php

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
    createTweet($tweet_textarea, $_SESSION['user_id']);
}

function newReplyTweet($tweet_textarea,$reply_post_id)
{
    createReTweet($tweet_textarea, $reply_post_id, $_SESSION['user_id']);
}
/**
 * ログアウト処理を行う。
 */
function logout()
{
    $_SESSION = [];
    $msg = 'ログアウトしました。';
}

if ($_POST) { /* POST Requests */ 
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
<?php?>
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
            <a href="index.php?reply=<?= "{$t['id']}" ?>">[返信する]</a> <a href="/view.php?id=<?= "{$t['reply_id']}" ?>">[返信元のメッセージ]</a>
          <?php }else{ ?>
            <p><a href="index.php?reply=<?= "{$t['id']}" ?>">[返信する]</a></p>
          <?php } ?>
          <!--返信課題はここまで修正しましょう。-->
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