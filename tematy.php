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
    <a style="float:right;" href="?cmd=logout">Wyloguj się</a>
	<span style="float:right; margin-right: 5px;">Zalogowany jako: <?=htmlspecialchars($_SESSION['username'])?> (<?=htmlspecialchars($_SESSION['login'])?>)</span>   
<?php }?>
<?php if (isset($_SESSION['admin']) && $_SESSION["admin"] == true) {?>
    <a style="float:left;" href="?cmd=lista">Lista użytkowników</a>
	<div class="user-info">
	<?php if($_SESSION['showTable'] == true) include("tabelka.php");?>
	</div>
<?php }?>
</nav>
<?php if( !$topics ){ ?>
  <p>To forum nie zawiera jeszcze żadnych tematów!</p>
<?php }else{ ?>
  <p><br>Możesz dodac nowy temat za pomocą <a href="#topic_form">formularza</a>.</p>
<?php foreach($topics as $k=>$v){ ?>
  <article class="topic">
    <header> </header>
    <div><a href="?topic=<?=$k?>"><?=htmlentities($v['topic'])?></a></div>
    <footer>ID: <?=$v['topicid']?>, Autor: <?=htmlentities($v['username'])?>, 
        Utworzono: <?=$v['date']?>, Liczba wpisów: <?=isset($posts_count[$v['topicid']])?$posts_count[$v['topicid']]:0;?>
<?php if(($v['username'] == $_SESSION['username']) || $_SESSION['admin'] == true) { ?>
	<nav>
  <a href="?topic=<?=htmlentities($v['topicid'])?>&cmd=editTopic#topic_form">EDYTUJ</a>  
  <a class="danger" href="?topic=<?=htmlentities($v['topicid'])?>&cmd=deleteSubject">KASUJ</a>
	</nav>
<?php } ?>
    </footer>
  </article>
<?php } } ?>
  <form action="index.php" method="post">
     <a name="topic_form"></a>
     <header><h2><?php if(isset($_GET['cmd']) and $_GET['cmd']=="editTopic"){ ?>Edytuj temat<?php }else{ ?>Dodaj nowy temat do dyskusji<?php } ?></h2></header>  
     <input type="text" name="topic" placeholder="Nowy temat" value="<?=(isset($_GET['cmd']) and $_GET['cmd']=="editTopic")?$topic["topic"]:'';?>"><br />
     <textarea name="topic_body" cols="80" rows="10" placeholder="Opis nowego tematu"><?=(isset($_GET['cmd']) and $_GET['cmd']=="editTopic")?$topic["topic_body"]:'';?></textarea><br />
     <input type="hidden" name="topicid" value="<?=(isset($_GET['cmd']) and $_GET['cmd']=="editTopic")?$topic["topicid"]:'';?>" />
	 <button type="submit" >Zapisz</button>
  </form>
</section>

<footer>
Ostatni wpis na formu powstał dnia: <?=get_last_post_date($posts_file, $separator);?>
</footer>
</body>
</html>    