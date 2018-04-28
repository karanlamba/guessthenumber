<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Karan Lamba">
	
	<link rel="icon" href="favicon.png">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<link rel="stylesheet" href="assets/style.css"> 

	<title>Guess The Number</title>
</head>
<body>
<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="#">The Greatest Number Guessing Game Ever</a>
		</div>
		<ul class="nav navbar-nav">
			<li><a class="active" href="game">Game</a></li>
			<li><a href="stats">Statistics</a></li>
		</ul>
	</div>
</nav>

<div class="container-fluid">
	<div id="game" class="main col-lg-4 col-lg-offset-4">
		<h3></h3>
		<p></p> 
		<input type="text" name=""><br>
		<button id="play_button" type="button" class="btn btn-primary"></button>
		<button id="stop_button" type="button" class="btn btn-primary">Abandon</button>
	</div>
	<div id="stats" class="main row">
		<h3></h3>
		<div id="data"></div> 
	</div>			
</div>

<!-- Modal -->
<div class="modal fade" id="winModal" tabindex="-1" role="dialog" aria-labelledby="winModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
	  WOOHOO! YOU GUESSED CORRECTLY!
      </div>
    </div>
  </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" 
	integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous">
</script>
<script src="assets/main.js"></script>
</body>
</html>
