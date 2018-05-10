<!DOCTYPE html>
    <html>

    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>LOL API</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="css/css.css" />
    </head>

    <body>
    <?php

	include 'APIKEY.php';

    if(isset($_POST['Submit']))
	{
		$username = $_POST['Username'];
		$usernameurl = rawurlencode($username);
		//$api_key = 'RGAPI-7a232c6c-35cf-4d03-bc76-fd57d36dd5bf';
		
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://euw1.api.riotgames.com/lol/summoner/v3/summoners/by-name/' . $usernameurl . '?api_key=' . $api_key);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
        $result = curl_exec($curl);
        $json = json_decode($result, true);
		$summonerid = $json['accountId'];

        curl_setopt($curl, CURLOPT_URL, 'https://euw1.api.riotgames.com/lol/match/v3/matchlists/by-account/' . $summonerid . '/recent?api_key=' . $api_key);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $results = curl_exec($curl);
		$matches = json_decode($results, true);
		for($i=0;$i<5;$i++)
		{			
			$matchid[$i] = $matches['matches'][$i]['gameId'];
			curl_setopt($curl, CURLOPT_URL, 'https://euw1.api.riotgames.com/lol/match/v3/matches/' . $matchid[$i] . '?api_key=' . $api_key);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
			$result = curl_exec($curl);
			$match = json_decode($result, true);
			for($y=0;$y<10;$y++)
			{
				$matchTeam[$y] = $match['participantIdentities'][$y]['player']['currentAccountId'];
				if ($matchTeam[$y] == $summonerid)
				{
					$teamID = $match['participantIdentities'][$y]['participantId'];
				}
			}
			if ($teamID <= 5)
			{
				$playerTeam = 0;
			} else
			{
				$playerTeam = 1;
			}
			$matchResult[$i] = $match['teams'][$playerTeam]['win'];
			if($matchResult[$i] == "Fail")
			{
				$matchResult[$i] = 'LOSE';
			}
			else if($matchResult[$i] == 'Win')
			{
				$matchResult[$i] = 'WIN';
			}
		}
		$curl = curl_close();

    }
    ?>
    <section class="jumbotron text-center">
	    <div class="container">
			<h1 class="jumbotron-heading">League of Legends API</h1>
			<p class="lead text-muted">Search through your past matches on the League of Legends EU WEST servers by inserting your in-game name below.</p>
			<form action="" method="POST">
				<div class="row justify-content-center">
					<div class="col-auto">
						<input type="text" class="form-control" placeholder="Username:" name="Username" value="<?php echo $username;?>"/>
					</div>
					<div class="col-auto">
						<input class="btn btn-primary" type="submit" name="Submit"/>
					</div>
				</div>
			</form>
		</div>
    </section>
	<?php
	if(isset($_POST['Submit']) and $_POST['Username'] != null)
	{ ?>
		<div class="container">
			<div class="card">
				<div class="card-header">
					<?php echo $matchResult[0];?>
				</div>
				<div class="card-body">
					<h5 class="card-title">Special Title</h5>
					<p class="card-text">Insert text here</p>
					<img src="https://ddragon.leagueoflegends.com/cdn/8.8.2/img/champion/<?php echo $champName[0]; ?>">
					<a href="#" class="btn btn-primary">Go somewhere</a>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="card">
				<div class="card-header">
					<?php echo $matchResult[1];?>
				</div>
				<div class="card-body">
					<h5 class="card-title">Special Title</h5>
					<p class="card-text">Insert text here</p>
					<a href="#" class="btn btn-primary">Go somewhere</a>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="card">
				<div class="card-header">
					<?php echo $matchResult[2];?>
				</div>
				<div class="card-body">
					<h5 class="card-title">Special Title</h5>
					<p class="card-text">Insert text here</p>
					<a href="#" class="btn btn-primary">Go somewhere</a>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="card">
				<div class="card-header">
					<?php echo $matchResult[3];?>
				</div>
				<div class="card-body">
					<h5 class="card-title">Special Title</h5>
					<p class="card-text">Insert text here</p>
					<a href="#" class="btn btn-primary">Go somewhere</a>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="card">
				<div class="card-header">
					<?php echo $matchResult[4];?>
				</div>
				<div class="card-body">
					<h5 class="card-title">Special Title</h5>
					<p class="card-text">Insert text here</p>
					<a href="#" class="btn btn-primary">Go somewhere</a>
				</div>
			</div>
		</div>
	<?php } ?>
	
    </body>

    </html>