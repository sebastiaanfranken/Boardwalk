<!DOCTYPE html>

<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Foutmelding</title>
		<style type="text/css">
		* {
			margin: 0;
			padding: 0;
		}

		body {
			background: #fff;
			font-family: verdana, arial, sans-serif;
			font-size: 16px;
			color: #232323;
		}

		h1, h2, table, pre, table, p {
			margin-bottom: 20px;
		}

		h1, h2 {
			border-bottom: 1px solid #ddd;
			padding-bottom: 6px;
		}

		main {
			width: 1140px;
			margin: 30px auto;
		}

		table {
			border: 1px solid #ddd;
			width: 100%;
		}

		table thead tr th,
		table tbody tr td {
			width: 50%;
			border-bottom: 1px solid #ddd;
			padding: 10px 15px;
		}

		table tbody tr:last-of-type td {
			border-bottom: 0;
		}

		pre {
			border: 1px solid #ddd;
			background: #f8f8f8;
			padding: 30px;
		}
		</style>
	</head>

	<body>
		<main>
			<h1>Er is iets fout gegaan.</h1>
			<p>Er is helaas iets fout gegaan, met de volgende informatie. Dit is vooral voor de ontwikkelaar handig.</p>

			<table cellpadding="0" cellspacing="0" border="0">
				<thead>
					<tr>
						<th>Sleutel</th>
						<th>Waarde</th>
					</tr>
				</thead>

				<tbody>
					<tr>
						<td>Foutmelding</td>
						<td><?php print $message;?></td>
					</tr>

					<tr>
						<td>Foutcode</td>
						<td><?php print $code;?></td>
					</tr>

					<tr>
						<td>Regelnummer</td>
						<td><?php print $line;?></td>
					</tr>

					<tr>
						<td>Bestand</td>
						<td><?php print $file;?></td>
					</tr>

					<tr>
						<td>Fouttype</td>
						<td><?php print $exceptionType;?></td>
					</tr>
				</tbody>
			</table>

			<h2>Stacktrace</h2>
			<pre><?php print $trace;?></pre>

			<?php if(strlen($other) > 0) : ?>
			<h2>Andere info</h2>
			<pre><?php print $other;?></pre>
			<?php endif;?>

			<?php if(strlen($previous) > 0) : ?>
			<h2>Oudere stacktrace</h2>
			<pre><?php print $previous;?></pre>
			<?php endif;?>
		</main>
	</body>
</html>