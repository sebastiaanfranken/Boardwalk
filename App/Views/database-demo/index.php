<!DOCTYPE html>

<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Database demo</title>
		<link rel="stylesheet" type="text/css" href="<?php print public_assets();?>css/demo.css" />
	</head>

	<body>
		<main>
			<h1>Database demo</h1>

			<form method="post" action="<?php print url('DatabaseDemo', 'postIndex', true);?>">
				<input type="submit" name="delete" value="Wis bepaalde records" />
				<a href="<?php print url('DatabaseDemo', 'getRekey');?>">Stel IDs opnieuw in</a>
			</form>

			<table cellpadding="0" cellspacing="0" border="0">
				<thead>
					<tr>
						<th>#</th>
						<th>IP</th>
						<th>URL</th>
						<th>Verzoektype</th>
						<th>Tijd</th>
					</tr>
				</thead>

				<tbody>
					<?php foreach($loglines as $logline) : ?>
					<tr>
						<td><?php print $logline->id;?></td>
						<td><?php print $logline->ip;?></td>
						<td><?php print $logline->url;?></td>
						<td><?php print $logline->request_method;?></td>
						<td><?php print timestamp('d-m-Y H:i:s', $logline->timestamp);?></td>
					</tr>
					<?php endforeach;?>
				</tbody>
			</table>

			<footer>
				<p><a href="<?php print url('index');?>">Terug</a></p>
			</footer>
		</main>
	</body>
</html>
