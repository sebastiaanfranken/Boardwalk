<!DOCTYPE html>

<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Content conversie</title>
		<link rel="stylesheet" type="text/css" href="<?php print public_assets();?>css/demo.css" />
	</head>

	<body>
		<form method="post" action="text-demo">
			<textarea name="text" cols="82" rows="24"></textarea><br />
			<input type="submit" name="process" value="Verwerk" />
		</form>

		<?php if(isset($result)) : ?>
		<main><?php print $result;?></main>
		<?php endif;?>
	</body>
</html>
