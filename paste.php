<?php
	session_start();
	header( "Content-type: text/html; charset: 'utf8'" );
	require_once( 'config.php' );
	global $result;
	if( isset($_GET['id']) or isset($_POST['submit']) ) {
		$pdo = new PDO( $dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC] );
		if( isset($_GET['id']) ) { 
			$stmt = $pdo->prepare( "SELECT l.lang AS lang, p.nick AS name, p.data AS data, p.date AS date FROM pastes p INNER JOIN languages l ON l.id = p.`type` WHERE p.id = :id LIMIT 1" );
			$stmt->execute( ['id' => $_GET['id']] );
			$result = $stmt->fetch();
		}
		else {
			$stmt = $pdo->prepare( "INSERT INTO pastes (data, nick, `type`) SELECT :datum, INET_ATON(:ip), l.id FROM languages l WHERE l.lang = :lang" );
			$stmt->execute( ['ip' => $_SERVER['REMOTE_ADDR'], 'datum' => $_POST['data'], 'lang' => $_POST['lang']] );
			$result = $pdo->lastInsertId();
		}
	}
?>
<!DOCTYPE HTML>
<html>
<head>
	<base href="/" target="_self" />
	<meta charset="utf-8">
	<link href="favicon.ico" rel="shortcut icon">
	<title>HiT Hi FiT Hai &middot; Pastes dump</title>
<?php
	if( isset($result['data']) ) {
?>
	<link href="styles/prettify.css" rel="stylesheet" type="text/css" media="all">
	<script type="text/javascript" src="js/prettify/prettify.js"></script>
</head>
<body onLoad="prettyPrint();">
	<pre class="prettyprint" id="<?= strtoupper($result['lang']); ?>"><?= htmlspecialchars($result['data'], ENT_NOQUOTES, 'UTF-8'); ?></pre>
<?php
	}
	elseif( is_numeric($result) ) {
		header( "Location: paste/" . $result );
	}
	else {
?>
</head>
<body>
	<form method="post" action="">
		<fieldset>
			<legend>Syntax highlight</legend>
			<label for="lang">Language: </label>
			<select name="lang" size="1">
				<option value='apollo'>apollo</option>
				<option value='basic'>basic</option>
				<option value='clj'>clj</option>
				<option value='css'>css</option>
				<option value='dart'>dart</option>
				<option value='erlang'>erlang</option>
				<option value='go'>go</option>
				<option value='hs'>hs</option>
				<option value='html'>html</option>
				<option value='lisp'>lisp</option>
				<option value='llvm'>llvm</option>
				<option value='lua'>lua</option>
				<option value='matlab'>matlab</option>
				<option value='ml'>ml</option>
				<option value='mumps'>mumps</option>
				<option value='n'>n</option>
				<option value='pascal'>pascal</option>
				<option value='php'>php</option>
				<option value='none' selected>plain text</option>
				<option value='proto'>proto</option>
				<option value='r'>r</option>
				<option value='rd'>rd</option>
				<option value='scala'>scala</option>
				<option value='sql'>sql</option>
				<option value='tcl'>tcl</option>
				<option value='tex'>tex</option>
				<option value='vb'>vb</option>
				<option value='vhdl'>vhdl</option>
				<option value='wiki'>wiki</option>
				<option value='xq'>xq</option>
				<option value='yaml'>yaml</option>
			</select>
		</fieldset>
		<fieldset>
			<legend>Content</legend>
			<textarea name="data" cols="80" rows="20" dir="ltr" lang="en">Paste text here</textarea>
		</fieldset>
		<input name="submit" type="submit" id="submit" value="Paste on server" />
	</form>
<?php
	}
?>
</body>
</html>
