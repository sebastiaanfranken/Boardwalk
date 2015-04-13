<!DOCTYPE html>

<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Boardwalk</title>
		<link rel="stylesheet" type="text/css" href="<?php print public_assets();?>css/demo.css" />
	</head>

	<body>
		<main>
			<h1>Boardwalk</h1>
			<p>Het werkt! Nu is het tijd om <em>coole dingen</em> te gaan bouwen. Gebruik de links hieronder om demo's van het systeem te bekijken.</p>
			<ul>
				<li><a href="<?php print $textDemoLink;?>">Tekst demo</a></li>
				<li><a href="<?php print $databaseDemoLink;?>">Database demo</a></li>
			</ul>
		</main>
	</body>
</html>
