<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
	<title>Red Crown test case</title>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />

	<style>
		.navbar-static-top {
			box-shadow: 0 1px 4px rgba(0,0,0,.2);
		}
		.navbar-brand {
			height: auto;
			padding-top: 4px;
			padding-bottom: 3px;
		}
		.navbar-brand img {
			height: 42px;
		}
	</style>
</head>
<body>

<div class="wrap">
	<header class="navbar navbar-static-top">
		<div class="container">
			<div class="navbar-header">
				<a href="/" class="navbar-brand">
					<img src="https://spb.hh.ru/employer-logo/1745599.png" alt="" />
				</a>
			</div>
		</div>
	</header>

	<div class="container">
		<?= $content ?>
	</div>
</div>

</body>
</html>
