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
	<form action="index.php" method="post" style="margin-bottom: 15px; margin-top: 10px;">
		<a name="login_form"></a>
		<header><h2>Zaloguj się do forum</h2></header>  
		<input type="text" name="useridlogin" placeholder="Nazwa logowania" pattern="[A-Za-z0-9\-]*" autofocus \><br />
		<input type="password" name="pass" placeholder="Hasło" \><br />
		<button type="submit">Zaloguj się</button>
	</form>
	<form action="index.php" method="post">
		<a name="newuser_form"></a>
		<header><h2>Jesli nie jesteś zarejestrowany, to możesz zapisać się do forum.</h2></header>  
		<input type="text" name="userid" placeholder="Nazwa logowania (dozwolone są tylko: litery, cyfry i znak '-')" pattern="[A-Za-z0-9\-]*" autofocus \><br />
		<input type="text" name="username" placeholder="Imię autora" \><br />
		<input type="password" name="pass1" placeholder="Hasło" \><br />
		<input type="password" name="pass2" placeholder="Powtórz hasło" \><br />
		<button type="submit">Zapisz się do forum</button>
	</form>
</section>

<footer>
Ostatni wpis na formu powstał dnia: <?=get_last_post_date($posts_file, $separator);?>
</footer>
</body>
</html>    