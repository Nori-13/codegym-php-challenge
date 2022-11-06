<?php
/**
 * @param string $name ユーザー名
 * @return PDOStatement ユーザー情報の連想配列を格納したPDOStatement
 * 名前を元にユーザー情報を取得します。
 */
function getUserByName($name)
{
    $sql = 'select * from users where name = :name';
    $stmt = getPdo()->prepare($sql);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * @param string $name ユーザー名
 * @param string $$password_hash ユーザーパスワードハッシュ値
 * @return bool 成功・失敗
 */
function createUser($name, $password_hash)
{
    $sql = 'insert into users (name, password_hash, created_at, updated_at)';
    $sql .= ' values (:name, :password_hash, :created_at, :updated_at)';
    $now = date("Y-m-d H:i:s");
    $stmt = getPdo()->prepare($sql);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
    $stmt->bindValue(':created_at', $now, PDO::PARAM_STR);
    $stmt->bindValue(':updated_at', $now, PDO::PARAM_STR);
    return $stmt->execute();
}

/**
 * @param string $text 投稿内容
 * @param string $user_id ユーザーID
 * @return bool 成功・失敗
 */
function createTweet($text, $user_id)
{
    $sql = 'insert into tweets (text, user_id, created_at, updated_at)';
    $sql .= ' values (:text, :user_id, :created_at, :updated_at)';
    $now = date("Y-m-d H:i:s");
    $stmt = getPdo()->prepare($sql);
    $stmt->bindValue(':text', $text, PDO::PARAM_STR);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':created_at', $now, PDO::PARAM_STR);
    $stmt->bindValue(':updated_at', $now, PDO::PARAM_STR);
    return $stmt->execute();
}

function createReTweet($text, $reply_post_id, $user_id)
{
    $sql = 'insert into tweets (text, user_id, created_at, updated_at, reply_id)';
    $sql .= ' values (:text, :user_id, :created_at, :updated_at, :reply_id)';
    $now = date("Y-m-d H:i:s");
    $stmt = getPdo()->prepare($sql);
    $stmt->bindValue(':text', $text, PDO::PARAM_STR);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':created_at', $now, PDO::PARAM_STR);
    $stmt->bindValue(':updated_at', $now, PDO::PARAM_STR);
    $stmt->bindValue(':reply_id', $reply_post_id, PDO::PARAM_INT);
    return $stmt->execute();
}
/**
 * @return PDOStatement ユーザー情報の連想配列を格納したPDOStatement
 * 投稿の一覧を取得します。
 */
function getTweets()
{
    $sql = 'select t.id, t.text, t.user_id, t.created_at, t.updated_at, t.reply_id, u.name';
    $sql .= ' from tweets t join users u on t.user_id = u.id';
    $sql .= ' order by t.updated_at desc';
    $stmt = getPdo()->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    
}

/* 返信課題はここからのコードを修正しましょう。 */
function getTweet($id)
{
    $sql = 'select t.id, t.text, t.user_id, t.created_at, t.updated_at, u.name, t.reply_id';
    $sql .= ' from tweets t join users u on t.user_id = u.id';
    $sql .= ' where t.id =' . "$id"; //:がないと入れる変数がないのでbindValueが必要ない
    $stmt = getPdo()->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
    //var_dump($id);
}
/* 返信課題はここからのコードを修正しましょう。 */

/* いいね機能課題のコード修正。　*/
function createFavorite($member_id, $post_id)
{
//10. sqlを実行する
    $sql = 'insert into favorites (member_id, post_id, created_at, updated_at)';
    $sql .= ' values (:member_id, :post_id, :created_at, :updated_at)';//　:＝php状でsqlの変数を定義する　その数だけbindValueが必要
    $now = date("Y-m-d H:i:s");
    $stmt = getPdo()->prepare($sql); //prepare=準備する
    $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
    $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->bindValue(':created_at', $now, PDO::PARAM_STR);
    $stmt->bindValue(':updated_at', $now, PDO::PARAM_STR); //bind=紐ずける
    return $stmt->execute();//　execute=実行する
    //var_dump($member_id);
    //var_dump($post_id);
    //var_dump($now);
}

//いいね機能を消去
function deleteFavorite($member_id, $post_id)
{
    //DELETE FROM `favorites` WHERE member_id=1 and  post_id=5;	
    $sql = 'DELETE FROM favorites WHERE post_id = :post_id AND member_id = :member_id'; 
    $stmt = getPdo()->prepare($sql);
    $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
    $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    return $stmt->execute();
}

function getFavoriteUsers($post_id) //何件いいねしたsql
{
    //  $sql = 'select * from users where name = :name';　参考
    //  $stmt = getPdo()->prepare($sql);
    //  $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $sql = 'SELECT count(id)';
    $sql .= ' FROM favorites';
    $sql .= ' WHERE post_id = :post_id'; //調べる 複数条件
    $stmt = getPdo()->prepare($sql);
    $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);//
    $stmt->execute();
    $result = $stmt->fetchColumn();
    //count(id)の結果を取り出している　
    return $result;
}
 //いいねしたかどうかを判定する　count１がいいねしている　０がいいねしていない
function hasFavorite($post_id, $member_id)
{
    $sql = 'SELECT count(id)';
    $sql .= ' FROM favorites';
    $sql .= ' WHERE post_id = :post_id AND member_id = :member_id';
    $stmt = getPdo()->prepare($sql);
    $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchColumn();
    return $result;
}