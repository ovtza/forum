<!DOCTYPE html>
<html>
<head>
	<title>TWWW - User: 144144, zadanie3</title>
    <meta charset="utf-8">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <header>
      <h1>
          Zadanie 3
      </h1>
      <h2>
          Proste forum
      </h2>
    </header>
    <nav>
            <a href="../">Home</a>
            <?php for($n=1;$n<=10;$n++) { if( is_dir("../zadanie".$n) ) { ?>
            <a href="../zadanie<?=$n?>">Zadanie <?=$n?></a>
            <?php } } ?>
    </nav>
<section>

  <nav>
<?php if (isset($_SESSION['valid']) && $_SESSION['valid'] == true) {?>
    <a style="float:right;" href="?topic=<?=$_GET['topic']?>&cmd=logout">Wyloguj się</a>
	<span style="float:right; margin-right: 5px;">Zalogowany jako: <?=htmlspecialchars($_SESSION['username'])?> (<?=htmlspecialchars($_SESSION['login'])?>)</span>  
<?php }?>
<?php if (isset($_SESSION['admin']) && $_SESSION["admin"] == true){?>
    <a style="float:left;" href="?topic=<?=$_GET['topic']?>&cmd=lista">Lista użytkowników</a>
	<div class="user-info">
	<?php if($_SESSION['showTable'] == true) include("tabelka.php");?>
	</div>
<?php }?>
    <table><tr>
    <td>
<?php if( $p=get_previous_topic_id($_GET['topic'], $topic_file, $separator) ){ ?>
    <a style="float:left;" href="index.php?topic=<?=$p?>">&lt;-- Poprzedni temat</a>
<?php } ?>
    </td><td  style="width: 33%;">
    <a href="index.php">Lista tematów</a>
    </td><td  style="width: 33%;">  
<?php if( $p=get_next_topic_id($_GET['topic'], $topic_file, $separator) ){ ?>
    <a  style="float:right;" href="index.php?topic=<?=$p?>">Następny temat --&gt;</a>
<?php } ?>
    </td>
    </tr></table>
  </nav>
  <article  class="topic">
    <header>Temat dyskusji: <b><?=htmlentities($topic['topic'])?></b></header>
    <div><?=nl2br(htmlentities($topic['topic_body']))?></div>
    <footer>
    ID: <?=$topic['topicid']?>, Autor: <?=htmlentities($topic['username'])?>, Data: <?=$topic['date']?>
    </footer>
  </article>
<?php if( !$posts ){ ?>
  <p>To forum nie zawiera jeszcze żadnych głosów w dyskusji!</p>
  <p>Możesz dodać nową wypowiedź za pomocą <a href="#post_form">formularza</a>.</p>
<?php }else{ ?>
  <p>Możesz dodac nową wypowiedź za pomocą <a href="#post_form">formularza</a>.</p>
<?php foreach($posts as $k=>$v){ ?>
  <article>
  <div><?=nl2br(htmlentities($v['post']))?></div>
  <footer>
      <?php if(($v['username'] == $_SESSION['username']) || $_SESSION['admin'] == true) { ?>
  <nav>
  <a href="?topic=<?=$_GET['topic']?>&id=<?=$v['postid']?>&cmd=edit">EDYTUJ</a>  
  <a class="danger" href="?topic=<?=$_GET['topic']?>&id=<?=$v['postid']?>&cmd=delete">KASUJ</a>
  </nav>
	  <?php } ?>
  ID: <?=$v['postid']?>, Autor: <?=htmlentities($v['username'])?>, Utworzono dnia: <?=$v['date']?></footer>
  </article>
<?php } } ?>
  <form action="index.php?topic=<?=$_GET['topic']?>" method="post">
     <a name="post_form" ></a>
     <header><h2><?php if($post){ ?>Edytuj wypowiedź<?php }else{ ?>Dodaj nowa wypowiedź do dyskusji<?php } ?></h2></header>  
     <textarea name="post" autofocus cols="80" rows="10" placeholder="Wpisz tu swoją wypowiedź." ><?=($post)?$post["post"]:'';?></textarea><br />
     <input type="hidden" name="postid" value="<?=($post)?$post["postid"]:"";?>" />
     <button type="submit" >Zapisz</button>
  </form>
</section>
<footer>
Ostatni wpis na formu powstał dnia: <?=get_last_post_date($posts_file, $separator);?>
</footer>
</body>
</html>        