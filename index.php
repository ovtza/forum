<?php
// Załadowanie funkcji

include("data.php");


//start sesji
session_start();

// Konfiguracja
$posts_file = 'wypowiedzi1.txt';
$topic_file = 'tematy1.txt';
$users_file = 'uzytkownicy.txt';
$separator = ",";

if( !is_file($posts_file) ) file_put_contents($posts_file,'');
if( !is_file($topic_file) ) file_put_contents($topic_file,'');
if( !is_file($users_file) ) file_put_contents($users_file,'');

// Pobranie wszystkicj tematów
$topics = get_topics($topic_file, $separator);

// zapis i edycja tematu
if( isset($_POST['topic']) and $_POST['topic']!="" and $_POST['topic_body']!=""){
	/*
	Tutaj sprawdzam czy uzytkownik edytuje swoj post
	zabezpieczenie gdyby ktos zmienil id posta przy edycji w linku 
	pobieram najpierw posta i sprawdzam czy zgadza sie nazwa autora
	dla admina to sprawdzenie nie obowiazuje, sprawdzam z sesji czy posiada uprawnienia
	*/
	$temat = $topics[$_POST['topicid']];
	if($_POST['topicid']!=''){
		if($temat['username'] == $_SESSION['username'] || $_SESSION['admin'] == true){
		$res = update_topic($_POST['topicid'], $_POST['topic'], $_POST['topic_body'], $temat["username"], $topic_file, $separator);
		}
	} else {
  $res = put_topic($_POST['topic'], $_POST['topic_body'], $_SESSION["username"], $topic_file, $separator);
	}

  header("Location: index.php");exit;
}   

// zapis lub aktualizacjia postu
if( isset($_POST['post']) and $_POST['post']!=""){
	/*
	Tutaj sprawdzam czy uzytkownik edytuje swoj post
	zabezpieczenie gdyby ktos zmienil id posta przy edycji w linku 
	pobieram najpierw posta i sprawdzam czy zgadza sie nazwa autora
	dla admina to sprawdzenie nie obowiazuje, sprawdzam z sesji czy posiada uprawnienia
	*/
	$pozwol = false;
	$post = get_post($_POST['postid'], $posts_file, ",");
	foreach ($post as $klucz => $wartosc){
		if (htmlentities($wartosc) == htmlentities($_SESSION['username'])) $pozwol = true;
	}
		  if( $_POST['postid']!='' ){
			  	 if($pozwol || $_SESSION['admin'] == true) { // tutuaj sprawdzenie
			$res = update_post( $_POST['postid'], $_POST['post'], $post["username"], $posts_file, $separator );
				 }
		  }else{
			$res = put_post( $_GET['topic'], $_POST['post'], $_SESSION["username"], $posts_file, $separator);
		  }


	 
  header("Location: index.php?topic=".$_GET['topic'] );exit;
}   

// kasowanie postu
if( isset($_GET['cmd']) and $_GET['cmd']=="delete" and $_GET['id']!="" and $_GET['topic']!=""){
	//tutaj to samo sprawdzenie tylko, ze id posta pobieram z GET'a
	$pozwol = false;
	$post = get_post($_GET['id'], $posts_file, ",");
	foreach ($post as $klucz => $wartosc){
		if (htmlentities($wartosc) == htmlentities($_SESSION['username'])) $pozwol = true;
	}
	if($pozwol || $_SESSION['admin'] == true) delete_post($_GET['id'], $posts_file, $separator);
  header("Location: index.php?topic=".$_GET['topic'] );exit;
}

// kasowanie tematu
if( isset($_GET['cmd']) and $_GET['cmd']=="deleteSubject" and $_GET['topic']!=""){
	//tutaj to samo sprawdzenie tylko, ze id posta pobieram z GET'a
	$temat = $topics[$_GET['topic']];
	if($temat['username'] == $_SESSION['username'] || $_SESSION['admin'] == true){
	delete_topic($_GET['topic'], $topic_file, $separator);
	//usuniecie wszystkich postow tematu
	$postyTematow = get_posts($_GET['topic'], $posts_file, $separator);
		foreach($postyTematow as $k=>$v){
			delete_post($v['postid'], $posts_file, $separator);
		}
	}
  header("Location: index.php");exit;
}

// pobranie danych postu w celu ich edycji
if( isset($_GET['cmd']) and $_GET['cmd']=="edit" and $_GET['id']!="" and $_GET['topic']!=""){
  $post = get_post($_GET['id'], $posts_file, $separator);
}else{
  $post=false;
}

// pobranie danych tematu w celu jego edycji
if( isset($_GET['cmd']) and $_GET['cmd']=="editTopic" and $_GET['topic']!=""){
  $temat = $topics[$_GET['topic']];
  
}else{
  $temat=false;
}  


// rejestracja uzytkownika
if(validRegistrationData($_POST)){ //walidacja
		add_user($_POST['userid'], $_POST['username'], $_POST['pass1'], $users_file, $separator);
		$users = get_users($users_file, $separator);
		login($users, $_POST['userid'], $_POST['pass1']);
		header("Location: index.php");exit;
	}

// logowanie
if( isset($_POST['useridlogin']) and $_POST['useridlogin']!="" 
and isset($_POST['pass']) and $_POST['pass']!=""){ // sprawdzam czy id jest unikalne i czy hasla sa takie same
	session_start();
	$users = get_users($users_file, $separator);
	login($users, $_POST['useridlogin'], $_POST['pass']);

  header("Location: index.php");exit;
}

//Pana funkcja wylogowania
if(isset($_GET['cmd']) and $_GET['cmd']=="logout"){
	$_SESSION['valid'] = false;
	$_SESSION = array();
   if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000,
          $params["path"], $params["domain"],
          $params["secure"], $params["httponly"]
      );
   }
   session_destroy();
   header("Location: index.php");exit;
}

// funkcja do pokazywania/ukrywania tabeli
if(isset($_GET['cmd']) and $_GET['cmd']=="lista" and $_SESSION["admin"] == true){
		if($_SESSION['showTable'] == true){
			$_SESSION['showTable'] = false;
		} else {
			$_SESSION['showTable'] = true;
		}
		if(isset($_GET['topic']) and $_GET['topic']!=""){
			header("Location: index.php?topic=".$_GET['topic'] );exit;
		} else {
			header("Location: index.php");exit;
		}
}
//funkcja do zmiany uprawnien uzytkownika
if(isset($_GET['cmd']) and $_GET['cmd']=="changeperm" and $_GET['userid']!="" and $_SESSION["admin"] == true){

			$usersForPermList = get_users($users_file, $separator);
			 foreach($usersForPermList as $k=>$v){
			if($v['userid'] == $_GET['userid']){
					if($v['permission'] == "user"){
						delete_user($_GET['userid'], $users_file, $separator);
						add_user($_GET['userid'], $v['username'], $v['pass'], "uzytkownicy.txt", ",", "admin");
					} else {
						delete_user($_GET['userid'], $users_file, $separator);
						add_user($_GET['userid'], $v['username'], $v['pass'], "uzytkownicy.txt", ",", "user");
					}
				}
			 }


		if(isset($_GET['topic']) and $_GET['topic']!=""){
			header("Location: index.php?topic=".$_GET['topic'] );exit;
		} else {
			header("Location: index.php");exit;
		}
}

//funckcja do usuwania uzytkownika, jego tematow i postow
if(isset($_GET['cmd']) and $_GET['cmd']=="deleteuser" and $_GET['userid']!="" and $_SESSION["admin"] == true){
		delete_user($_GET['userid'], $users_file, $separator);
		
		//usuniecie wszystkich tematow ktorych uzytkownik byl autorem
		foreach($topics as $k=>$v){ // przeszukiwanie tematow
			if($v['username'] == $_GET['userid']){
				delete_topic($v['topicid'], $topic_file, $separator);
			}
				//usuwam tez wszystkie wypowiedzi uzytkownika, pobierajac id tematow z poprzedniej petli
				$posty = get_posts($v['topicid'], $posts_file, $separator);
				foreach ($posty as $kk=>$vv){
					if($vv['username'] == $_GET['userid']){
						delete_post($vv['postid'], $posts_file, $separator); 
					}
				}
		}


			header("Location: index.php");exit;
}


//-------------------------------------------------------------
// Prezentacja
//-------------------------------------------------------------
if (isset($_SESSION['valid']) && $_SESSION['valid'] == true) {
	if(isset($_GET["topic"]) and $_GET["topic"]!='' and @$_GET["cmd"] != "editTopic"){ 
   $posts = get_posts($_GET["topic"], $posts_file, $separator);
   $topic = $topics[$_GET["topic"]];
   include('wypowiedzi.php');
} else { // widok tematów  
   // policz posty w tematach
   $posts_count = get_posts_count($posts_file, $separator);
   if(isset($_GET["topic"])) $topic = $topics[$_GET["topic"]];
   include('tematy.php'); 

} 

} else {
	  include('logowanie.php');
}

?>