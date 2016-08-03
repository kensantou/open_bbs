<?php
namespace App\Usecase;
use \PDO;

class TopicUsecase{

    public function createNewTopic($container,$title,$image_name){
      $connection = $container->get('pdo');
      $topic_query = $connection->prepare(
      "INSERT INTO topics
        (title,comment_cnt,image_name)
       VALUES
        (:title,:comment_cnt,:image_name)"
      );
      $topic_query->bindParam(':title', $title, PDO::PARAM_STR);
      $topic_query->bindValue(':comment_cnt',0, PDO::PARAM_INT);
      $topic_query->bindParam(':image_name', $image_name, PDO::PARAM_STR);
      $topic_query->execute();
      $query = $connection->prepare(
      "SELECT max(topic_id) FROM topics"
      );
      $query->execute();
      $topic = $query->fetch();
      $topic_id = $topic[0];

      return $topic_id;
    }

    public function selectNewTopics($container){
      $connection = $container->get('pdo');
      $query = $connection->prepare(
      "SELECT
        topic_id,title,comment_cnt,image_name,created
       FROM
        topics
       order by created desc
      ");
      $query->execute();
      $topics = $query->fetchAll();
      return $topics;
    }

    public function selectRankingTopics($container){
      $connection = $container->get('pdo');
      $query = $connection->prepare(
      "SELECT
        topic_id,title,comment_cnt,image_name,created
       FROM
        topics
       order by comment_cnt desc
      ");
      $query->execute();
      $topics = $query->fetchAll();
      return $topics;
    }

    public function selectWeeklyRankingTopics($container){
      $connection = $container->get('pdo');
      $query = $connection->prepare(
      "SELECT
        topic_id,title,comment_cnt,image_name,created
       FROM
        topics
       WHERE
        created > TIMESTAMP(DATE_SUB(NOW(), INTERVAL 7 DAY))
       order by comment_cnt desc
      ");
      $query->execute();
      $topics = $query->fetchAll();
      return $topics;
    }

    public function selectTopicById($container,$topic_id){
      $connection = $container->get('pdo');
      $topic_query = $connection->prepare(
      "SELECT
        title,comment_cnt,image_name,created
       FROM
        topics
       WHERE
        topic_id = :topic_id
      ");
      $topic_query->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
      $topic_query->execute();
      $topic = $topic_query->fetch();
      return $topic;
    }

    public function searchTopicByTitle($container,$title){
      $connection = $container->get('pdo');
      $query = $connection->prepare(
      "SELECT
        title,comment_cnt,image_name,created
       FROM
        topics
       WHERE
        title like :title
      ");
      $title = '%'.$title.'%';
      $query->bindParam(':title', $title, PDO::PARAM_STR);
      $query->execute();
      $topics = $query->fetchAll();
      return $topics;
    }

}
