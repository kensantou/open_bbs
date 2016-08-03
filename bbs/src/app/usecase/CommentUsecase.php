<?php
namespace App\Usecase;
use \PDO;

class CommentUsecase{

    public function createComment($container,$topic_id,$name,$message,$image_name){
      $connection = $container->get('pdo');
      if(empty($name)){
        $name="匿名";
      }
      $comment_id_query = $connection->prepare(
      "SELECT
        max(comment_id)
       FROM
        comments
       WHERE
        topic_id = :topic_id
      ");
      $comment_id_query->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
      $comment_id_query->execute();
      $comment_id = $comment_id_query->fetch()[0] + 1;

      $comment_query = $connection->prepare(
      "INSERT INTO comments
        (topic_id,comment_id,name,message,image_name)
       VALUES
        (:topic_id,:comment_id,:name,:message,:image_name)"
      );
      $comment_query->bindParam(':topic_id',$topic_id, PDO::PARAM_INT);
      $comment_query->bindParam(':comment_id',$comment_id, PDO::PARAM_INT);
      $comment_query->bindParam(':name', $name, PDO::PARAM_STR);
      $comment_query->bindParam(':message', $message, PDO::PARAM_STR);
      $comment_query->bindParam(':image_name', $image_name, PDO::PARAM_STR);
      $comment_query->execute();

      $comment_cnt_query = $connection->prepare(
      "SELECT
        max(comment_cnt)
       FROM
        topics
       WHERE
        topic_id = :topic_id
      ");
      $comment_cnt_query->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
      $comment_cnt_query->execute();
      $comment_count = $comment_cnt_query->fetch()[0] + 1;

      $count_query = $connection->prepare("UPDATE topics set comment_cnt = :comment_cnt WHERE topic_id = :topic_id");
      $count_query->bindParam(':comment_cnt',$comment_count, PDO::PARAM_INT);
      $count_query->bindParam(':topic_id',$topic_id, PDO::PARAM_INT);
      $count_query->execute();

      return true;
    }



    public function selectCommentsByTopicId($container,$topic_id){
      $connection = $container->get('pdo');
      $query = $connection->prepare(
      "SELECT
        comment_id,name,message,image_name,created
       FROM
        comments
       WHERE
        topic_id = :topic_id
       ORDER BY created asc
      ");
      $query->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
      $query->execute();
      $comments = $query->fetchAll();
      return $comments;
    }

}
