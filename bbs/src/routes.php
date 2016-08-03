<?php
namespace App\Repository;
use Respect\Validation\Validator as v;
include "app/usecase/TopicUsecase.php";
include "app/usecase/ImageUsecase.php";
include "app/usecase/CommentUsecase.php";
// Routes

if( !isset($_SESSION) ) {
  session_start();
}

$app->get('/', function ($request, $response, $args) {
    $topicUsecase = new \App\Usecase\TopicUsecase();
    $topics = $topicUsecase->selectNewTopics($this);
    $weekly = $topicUsecase->selectWeeklyRankingTopics($this);
    return $this->renderer->render($response, 'index.phtml', ['page_name'=>'new','topics'=>$topics,'weekly'=>$weekly]);
});

$app->get('/new', function ($request, $response, $args) {
    $topicUsecase = new \App\Usecase\TopicUsecase();
    $topics = $topicUsecase->selectNewTopics($this);
    $weekly = $topicUsecase->selectWeeklyRankingTopics($this);
    return $this->renderer->render($response, 'index.phtml', ['page_name'=>'new','topics'=>$topics,'weekly'=>$weekly]);
});

$app->get('/ranking', function ($request, $response, $args) {
    $topicUsecase = new \App\Usecase\TopicUsecase();
    $topics = $topicUsecase->selectRankingTopics($this);
    $weekly = $topicUsecase->selectWeeklyRankingTopics($this);
    return $this->renderer->render($response, 'index.phtml', ['page_name'=>'rank','topics'=>$topics,'weekly'=>$weekly]);
});

$app->get('/search', function ($request, $response, $args) {
    $params=$request->getParams();
    $keyword = $params['keyword'];
    $topicUsecase = new \App\Usecase\TopicUsecase();
    $topics = $topicUsecase->searchTopicByTitle($this,$keyword);
    $weekly = $topicUsecase->selectWeeklyRankingTopics($this);
    return $this->renderer->render($response, 'index.phtml', ['page_name'=>'search','topics'=>$topics,'weekly'=>$weekly]);
});


$app->get('/topic/{id:[0-9]+}', function ($request, $response, $args) {
    $topicUsecase = new \App\Usecase\TopicUsecase();
    $topic = $topicUsecase->selectTopicById($this,$args['id']);
    if(empty($topic)){
      return $this->renderer->render($response, 'error.phtml');
    }
    $commentUsecase = new \App\Usecase\CommentUsecase();
    $comments = $commentUsecase->selectCommentsByTopicId($this,$args['id']);
    $weekly = $topicUsecase->selectWeeklyRankingTopics($this);
    return $this->renderer->render($response, 'topic.phtml',['id'=>$args['id'],'topic'=>$topic,'comments'=>$comments,'weekly'=>$weekly]);
});

// Create comment
$app->post('/topic/comment/confirm', function ($request, $response, $args) {
    $params=$request->getParams();
    $error_list = validateComment($params);
    $file = $request->getUploadedFiles()['image'];
    $file_name = null;
    if($file->getSize() > 0){
      $path = $this->get('settings')['image_path']['tmp_image'];
      $file_name = saveTmpFile($path,$file);
      $error_list = validateFile($path,$file_name);
    }
    $params['save_file_name']=$file_name;
    $csrf_name = $request->getAttribute('csrf_name');
    $csrf_value = $request->getAttribute('csrf_value');
    if(!empty($error_list)){
      $topicUsecase = new \App\Usecase\TopicUsecase();
      $topic = $topicUsecase->selectTopicById($this,$params['id']);
      $commentUsecase = new \App\Usecase\CommentUsecase();
      $comments = $commentUsecase->selectCommentsByTopicId($this,$params['id']);
      return $this->renderer->render($response, 'topic.phtml',['id'=>$params['id'],'topic'=>$topic,'comments'=>$comments,'error_list'=>$error_list]);
    }
    return $this->renderer->render($response, 'comment_confirm.phtml',['id'=>$params['id'],'params'=>$params,
      'csrf_name'=>$csrf_name,'csrf_value'=>$csrf_value]);
});

$app->post('/topic/comment/complete', function ($request, $response, $args) {
    if (false === $request->getAttribute('csrf_status')) {
      return $this->renderer->render($response, 'error.phtml');
    }
    $params=$request->getParams();
    $error_list = validateComment($params);
    if(!empty($error_list)){
      return $this->renderer->render($response, 'error.phtml');
    }
    $file_name = $params['save_file_name'];
    if(!empty($file_name)){
      $imageUsecase = new \App\Usecase\ImageUsecase();
      $tmp_path = $this->get('settings')['image_path']['tmp_image'];
      $real_path = $this->get('settings')['image_path']['public_image'];
      $imageUsecase->saveAsPublicFile($tmp_path,$real_path,$file_name);
    }
    $commentUsecase = new \App\Usecase\CommentUsecase();
    $commentUsecase->createComment($this,$params['id'],$params['name'],$params['comment'],$file_name);

    return $response
        ->withStatus(302)
        ->withHeader('Location', "/topic/".$params['id']);
});

// Create topic
$app->get('/topic/create', function ($request, $response, $args) {
    return $this->renderer->render($response, 'topic_create.phtml', $args);
});

$app->post('/topic/confirm', function ($request, $response, $args) {
    $params=$request->getParams();
    $error_list = validateTopic($params);
    $error_list = validateComment($params);
    // Save image as tmp
    $file = $request->getUploadedFiles()['image'];
    $file_name = null;
    if($file->getSize() > 0){
      $path = $this->get('settings')['image_path']['tmp_image'];
      $file_name = saveTmpFile($path,$file);
      $error_list = validateFile($path,$file_name);
    }
    if(!empty($error_list)){
      return $this->renderer->render($response, 'topic_create.phtml',['params'=>$params,'error_list'=>$error_list]);
    }
    $params['save_file_name']=$file_name;
    $csrf_name = $request->getAttribute('csrf_name');
    $csrf_value = $request->getAttribute('csrf_value');
    return $this->renderer->render($response, 'topic_confirm.phtml',['params'=>$params,
      'csrf_name'=>$csrf_name,'csrf_value'=>$csrf_value]);
});

$app->post('/topic/complete', function ($request, $response, $args) {
    if (false === $request->getAttribute('csrf_status')) {
      return $this->renderer->render($response, 'error.phtml');
    }
    $params=$request->getParams();
    $error_list = validateTopic($params);
    $error_list = validateComment($params);
    if(!empty($error_list)){
      return $this->renderer->render($response, 'error.phtml');
    }
    $topicUsecase = new \App\Usecase\TopicUsecase();
    $file_name = $params['save_file_name'];
    if(!empty($file_name)){
      $imageUsecase = new \App\Usecase\ImageUsecase();
      $tmp_path = $this->get('settings')['image_path']['tmp_image'];
      $real_path = $this->get('settings')['image_path']['public_image'];
      $imageUsecase->saveAsPublicFile($tmp_path,$real_path,$file_name);
    }
    $topic_id = $topicUsecase
      ->createNewTopic($this,$params['title'],$file_name);
    $commentUsecase = new \App\Usecase\CommentUsecase();
    $commentUsecase->createComment($this,$topic_id,$params['name'],$params['comment'],$file_name);

    return $this->renderer->render($response, 'topic_complete.phtml',['params'=>$params]);
});

function saveTmpFile($path,$file){
  $imageUsecase = new \App\Usecase\ImageUsecase();
  return $imageUsecase->saveTmpFile($path,$file);
}

function validateFile($path,$file_name){
  $error_list = null;
  if(!v::image()->validate($path.$file_name)){
    unlink($path.$file_name);
    $error_list[]="アップロードできるファイルは画像のみです。";
  }elseif(!v::size('1B', '5MB')->validate($path.$file_name)){
    unlink($path.$file_name);
    $error_list[]="アップロードできるファイルは5MBまでです。";
  }
  return $error_list;
}

function validateTopic($params){
  $error_list = null;
  $titleValidator = v::length(1, 100);
  if(!$titleValidator->validate($params['title'])){
    $error_list[] = "タイトルは１文字以上１００文字以下でお願いします。";
  };
  return $error_list;
}

function validateComment($params){
  $error_list = null;
  $commentValidator = v::length(1, 300);
  if(!$commentValidator->validate($params['comment'])){
    $error_list[] = "コメントは１文字以上３００文字以下でお願いします。";
  };
  $nameValidator = v::length(0, 30);
  if(!$nameValidator->validate($params['name'])){
    $error_list[] = "名前は３０文字以下でお願いします。";
  };
  return $error_list;
}
