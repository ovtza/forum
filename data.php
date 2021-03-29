<?php
// ---------------------------------------------------------------------------
// Topics - funkcje zarzadzania tematami
//------------------------------------------------------------------------------
// funkcja zapisu do pliku
function put_topic($topic, $topic_body, $username, 
                   $datafile="tematy1.txt", $separator=":-:" )
{
   // ostatni wiersz zawiera najmłodszy wpis
   if( is_file($datafile) ){
      // odczyt pliku
      $data=file( $datafile );
      // pobranie danych z ostatniego elementu tablicy $data
      $record = explode( $separator, trim(array_pop($data))); 
      $id = (count($record)>1)?($record[0] + 1):1;
   }else{
      $id = 1;    
   }
   // utworzenie nowego wiersz danych
   // zakodowanie przez bin2hex() danych przesłanych przez użtykownika
   $data = implode( $separator, 
                     array( $id, 
                            bin2hex($topic),
                            bin2hex($topic_body), 
                            bin2hex($username), 
                            date("Y-m-d H:i:s") 
                  ));
   // zapis danych na końcu pliku
   if( $fh = fopen( $datafile, "a+" )){
      fwrite($fh, $data."\n");
      fclose($fh);
      return $postid;
   }else{
      return FALSE;
   };                               
}
//------------------------------------------------------------------------------
// funkcja aktualizuje w pliku dane dla wypowiedzi o danym $postid
function update_topic($topicid, $topic, $topic_body, $username, 
                      $datafile="tematy1.txt", $separator=":-:")
{
    $data=file( $datafile ); 
    $new_topic=FALSE;
    foreach($data as $k=>$v ){
       $r = explode( $separator, trim($v));
       if( $r[0]==$topicid ){
           $new_topic = array( 
                 "topicid"  => $r[0],
                 "topic" => bin2hex($topic),
                 "topic_body" => bin2hex($topic_body),
                 "username" => bin2hex($username),
                 "date"    => date("Y-m-d H:i:s")
              );
              $data[$k] = implode($separator,$new_topic)."\n";
              file_put_contents($datafile, implode("", $data));  
            break;  
       }
    }
    return $new_topic; 
}

//------------------------------------------------------------------------------
// funkcja usuwa z pliku dane dla tematow o danym $id
function delete_topic( $id, 
                      $datafile="tematy1.txt", $separator=":-:")
{
   if( $data=file( $datafile ) ){
      foreach($data as $k=>$v){
         $r = explode( $separator, trim($v));
         if( $r[0]==$id ){
            unset($data[$k]);
            break;
         }   
      }
      return file_put_contents($datafile,implode("", $data)); 
   }else{
      return FALSE;
   }   
}

//------------------------------------------------------------------------------
// funkcja odczytu z pliku wszystkich tematów
function get_topics( $datafile="tematy1.txt", $separator=":-:" )
{
   // wczytanie pliku do tablicy stringów
   if( $data=file( $datafile ) ){
      // utworzenie pustej tablicy wynikowej
      $topics=array();
      // dla każdego elementu tablicy $data
      //    $k - klucz ementu,  $v - wartość elementu
      foreach($data as $k=>$v){
          // umieszcza kolejne elementy wiersza rozdzielone separatoerm 
          // w kolejnych elementach zwracanej tablicy
          $record = explode( $separator, trim($v));
          // jesli pasuje identyfikator tematu
          // przepakowanie do $posts[] i dekodowanie danych użytkownika
          $topics[$record[0]]=array( 
             "topicid"    => $record[0],
             "topic"      => hex2bin($record[1]),
             "topic_body" => hex2bin($record[2]),
             "username"   => hex2bin($record[3]),
             "date"       => $record[4]
          );
      }
      // zwraca tablice z wynikami
      return $topics;   
   }else{
      // zwraca kod błędu
      return FALSE;
   }
}

//------------------------------------------------------------------------------
// funkcja wyznacza id poprzedniego tematu
function get_previous_topic_id( $topicid, 
                                $datafile="tematy.txt", $separator=":-:")
{
    $data=file( $datafile );
    $pre=0;
    if( count($data) ){
       foreach($data as $k=>$v ){
          $r = explode( $separator, trim($v));
          if( $r[0]<$topicid) $pre=$r[0];
          if( $r[0]==$topicid ) break;  
       }
    }
    return $pre;
}

//------------------------------------------------------------------------------
// funkcja wyznacza id następnego tematu
function get_next_topic_id( $topicid, 
                            $datafile="tematy.txt", $separator=":-:")
{
    $data=file( $datafile );
    $next=0;
    if( count($data) ){
       foreach($data as $k=>$v ){
          $r = explode( $separator, trim($v));
          if( $r[0]<$topicid ) continue;
          if( $r[0]>$topicid) {
             $next=$r[0];
             break;
          }     
       }
    }
    return $next;
}

// ---------------------------------------------------------------------------
// Posts - funkcje zarzadzania wypowiedziami
//------------------------------------------------------------------------------
// funkcja wyszukująca wypowiedzi na określony temat
//   $topicid - identyfikator tematu
//   $datafile - ścieżka do pliku zawierającego dane
//   $separator - znaki tworzące separator pól rekordu
//
// format pliku danych:
// postid:-:topicid:-:post:-:username:-:date
// 
function get_posts( $topicid, 
                    $datafile="wypowiedzi.txt", $separator=":-:")
{
   // wczytanie pliku do tablicy stringów
   if( $data=file( $datafile ) ){
      // utworzenie pustej tablicy wynikowej
      $posts=array();
      // dla każdego elementu tablicy $data
      //    $k - klucz ementu,  $v - wartość elementu
      foreach($data as $k=>$v){
          // umieszcza kolejne elementy wiersza rozdzielone separatoerm 
          // w kolejnych elementach zwracanej tablicy
          $record = explode( $separator, trim($v));
          // jesli pasuje identyfikator tematu
          if( $record[1]==$topicid ){
              // przepakowanie do $posts[] i dekodowanie danych użytkownika
              $posts[]=array( 
                 "postid"  => $record[0],
                 "topicid" => $record[1],
                 "post"    => hex2bin($record[2]),
                 "username"=> hex2bin($record[3]),
                 "date"    => $record[4]
              );
          }
      }
      // zwraca tablice z wynikami
      return $posts;   
   }else{
      // zwraca kod błędu
      return FALSE;
   }
}

//------------------------------------------------------------------------------
// funkcja zapisu wypowiedzi do pliku
function put_post( $topicid, $post, $username, 
                   $datafile="wypowiedzi.txt", $separator=":-:")
{
   // ostatni wiersz zawiera najmłodszy wpis
   if( is_file($datafile) ){
      // odczyt pliku
      $data=file( $datafile );
      $postid = 1;
      // pobranie danych z ostatniego elementu tablicy $data
      if( $last = trim(array_pop($data)) ){
         $record = explode( $separator, $last); 
         $postid = $record[0]+1;
      }
   }      
   // utworzenie nowego wiersz danych
   // zakodowanie przez bin2hex() danych przesłanych przez użtykownika
   $data = implode( $separator, 
                     array( $postid, 
                            $topicid, 
                            bin2hex($post), 
                            bin2hex($username), 
                            date("Y-m-d H:i:s") 
                     )
                  );
   // zapis danych na końcu pliku
   if( $fh = fopen( $datafile, "a+" )){
      fwrite($fh, $data."\n");
      fclose($fh);
      return $postid;
   }else{
      return FALSE;
   };                               
}

//------------------------------------------------------------------------------
// funkcja pobiera z pliku wypowiedz o danym $id
function get_post( $id, 
                   $datafile="wypowiedzi.txt", $separator=":-:" )
{
    $data = file( $datafile );
    $post=FALSE;
    foreach($data as $v ){
       $r = explode( $separator, trim($v));
       if( $r[0]==$id ){
           $post = array( 
                 "postid"  => $r[0],
                 "topicid" => $r[1],
                 "post"    => hex2bin($r[2]),
                 "username"=> hex2bin($r[3]),
                 "date"    => $r[4]
              );
            break;  
       }
    }
    return $post; 
}

//------------------------------------------------------------------------------
// funkcja aktualizuje w pliku dane dla wypowiedzi o danym $postid
function update_post( $postid, $post, $username, 
                      $datafile="wypowiedzi.txt", $separator=":-:")
{
    $data=file( $datafile ); 
    $new_post=FALSE;
    foreach($data as $k=>$v ){
       $r = explode( $separator, trim($v));
       if( $r[0]==$postid ){
           $new_post = array( 
                 "postid"  => $r[0],
                 "topicid" => $r[1],
                 "post"    => bin2hex($post),
                 "username"=> bin2hex($username),
                 "date"    => date("Y-m-d H:i:s")
              );
              $data[$k] = implode($separator,$new_post)."\n";
              file_put_contents($datafile, implode("", $data));  
            break;  
       }
    }
    return $new_post; 
}

//------------------------------------------------------------------------------
// funkcja usuwa z pliku dane dla wypowiedzi o danym $id
function delete_post( $id, 
                      $datafile="wypowiedzi.txt", $separator=":-:")
{
   if( $data=file( $datafile ) ){
      foreach($data as $k=>$v){
         $r = explode( $separator, trim($v));
         if( $r[0]==$id ){
            unset($data[$k]);
            break;
         }   
      }
      return file_put_contents($datafile,implode("", $data)); 
   }else{
      return FALSE;
   }   
}

//------------------------------------------------------------------------------
// funkcja zlicza wypowiedzi na każdy z tematów
function get_posts_count( $datafile="wypowiedzi.txt", $separator=":-:" )
{
   if( !is_file($datafile) ) 
      return FALSE;
   $post_count = array();   
   if( $data=file( $datafile ) ){
      foreach( $data as $v ){
         if( strlen(trim($v))>0 ){
           $p = explode( $separator, trim($v));
           if( isset($post_count[$p[1]]) )
             $post_count[$p[1]] = $post_count[$p[1]] + 1;
           else
             $post_count[$p[1]] = 1;
         }
      }
      return $post_count; 
   }else{
      return FALSE;
   }
}

//------------------------------------------------------------------------------
// funkcja pobiera date ostatniej wypowiedzi
function get_last_post_date($datafile="wypowiedzi.txt", $separator=":-:")
{
    if( $data=file( $datafile ) ){
        $record = explode( $separator, trim(array_pop($data)));
        return $record[4];
    }else{
        return '- brak postów -';
    } 
}

//------------------------------------------------------------------------------
// funkcja zapisuje uzytkownika do pliku
function add_user($userid, $username, $pass1, $datafile="uzytkownicy.txt", $separator=":-:", $permission="user")
{
   if( is_file($datafile) ){
      $data=file( $datafile );
      $postid = 1;
      if( $last = trim(array_pop($data)) ){
         $record = explode( $separator, $last); 
         $postid = $record[0]+1;
      }
   }      

   $data = implode( $separator, 
                     array( bin2hex($userid), 
                            bin2hex($username),
                            md5($pass1), //szyfrowanie
                            "$permission",
                     )
                  );
   if( $fh = fopen( $datafile, "a+" )){
      fwrite($fh, $data."\n");
      fclose($fh);
      return $postid;
   }else{
      return FALSE;
   };                               
}


//------------------------------------------------------------------------------
// funkcja pobiera dane o uzytkownikach
//------------------------------------------------------------------------------
function get_users( $datafile="uzytkownicy.txt", $separator=":-:" )
{
   if( $data=file( $datafile ) ){
      $users=array();
      foreach($data as $k=>$v){
          $record = explode( $separator, trim($v));
          $users[$record[0]]=array( 
             "userid"    => hex2bin($record[0]),
             "username"      => hex2bin($record[1]),
             "pass" => $record[2],
             "permission"   => $record[3]
          );
      }
      return $users;   
   }else{
      return FALSE;
   }
}
//------------------------------------------------------------------------------
// funkcja sprawdza czy id uzytkownika jest unikalne
//------------------------------------------------------------------------------
function is_unique($userid, $datafile="uzytkownicy.txt", $separator=":-:" )
{
	if($userid == "admin") return false; //sprawdzam czy id nie jest "admin" w przypadku gdyby nie bylo go jeszcze nie ma w bazie
		$data = file("$datafile");
		foreach($data as $line) {
		if (strstr($line,$userid)){
			return false;
			}
		}
	return true;
}
//------------------------------------------------------------------------------
// funkcja sprawdza czy znaki uzyte sie zgadzaja patternowi po stronie serwera
//------------------------------------------------------------------------------
function validate($text) {

	if (preg_match('/^[a-zA-Z0-9]+/', $text)) {
		return true;
	}
return false;
}
//------------------------------------------------------------------------------
// funkcja sprawdza dane przy logowaniu
//------------------------------------------------------------------------------
function login($userlist, $userid, $pass){
	
		
   foreach($userlist as $k=>$v){
			if($v['userid'] == $userid and $v['pass'] == md5($pass)){
				$_SESSION["login"] = "$userid";
				$_SESSION["username"] = $v['username'];
				$_SESSION['valid'] = true;
				$_SESSION['showTable'] = false;
					if($v['permission'] == "admin"){
						$_SESSION["admin"] = true;
							} else {
						$_SESSION["admin"] = false;
					}
				break;
			}
	}
}

//------------------------------------------------------------------------------
// funkcja usuwa z pliku uzytkownika
function delete_user($userid, $datafile="uzytkownicy.txt", $separator=":-:")
{

   if( $data=file($datafile) ){
      foreach($data as $k=>$v){
         $r = explode( $separator, trim($v));
         if( $r[0]==bin2hex($userid) ){
            unset($data[$k]);
            break;
         }   
      }
      return file_put_contents($datafile,implode("", $data)); 
   } else {
      return FALSE;
   }   
}

