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

	include('_include/APIKEY.inc');

    if(isset($_POST['Submit']))
	{
		$username = $_POST['Username'];
		$usernameurl = rawurlencode($username);
		
        $curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://euw1.api.riotgames.com/lol/summoner/v3/summoners/by-name/' . $usernameurl . '?api_key=' . $api_key);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
		$result = curl_exec($curl);
        $json = json_decode($result, true);
		$summonerid = $json['accountId'];

        curl_setopt($curl, CURLOPT_URL, 'https://euw1.api.riotgames.com/lol/match/v3/matchlists/by-account/' . $summonerid . '?api_key=' . $api_key);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$results = curl_exec($curl);
		$matches = json_decode($results, true);
		for($i=0;$i<6;$i++)
		{				
			$champID[$i] = $matches['matches'][$i]['champion'];
			$string = file_get_contents('_include/ChampList.json');
			$content = json_decode($string, true);

			foreach($content['data'] as $champs)
			{
				if($champs['id'] == $champID[$i])
				{
					$championName[$i] = $champs['name'];
					$championTitle[$i] = $champs['title'];
				}
			}
			
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
					$teamposition = $y;
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
				$matchResult[$i] = 'DEFEAT';
			}
			else if($matchResult[$i] == 'Win')
			{
				$matchResult[$i] = 'VICTORY';
			}

			//TYPE of each of the game
			$gametype[$i] = $match['queueId'];
			if($gametype[$i] == 420)
			{
				$gamemode[$i] = "Ranked Solo Game";
			} else 
			{
				$gamemode[$i] = "Normal Game";
			}

			//KILLS, DEATHS, ASSISTS for each of the games
			$kills[$i] = $match['participants'][$teamposition]['stats']['kills']; 
			$deaths[$i] = $match['participants'][$teamposition]['stats']['deaths'];
			$assists[$i] = $match['participants'][$teamposition]['stats']['assists'];
			$KDA[$i] = $kills[$i] . " / " . $deaths[$i] . " / " . $assists[$i];

			//ITEMS for each of the games
			$item0[$i] = $match['participants'][$teamposition]['stats']['item0'];
			$item1[$i] = $match['participants'][$teamposition]['stats']['item1'];
			$item2[$i] = $match['participants'][$teamposition]['stats']['item2'];
			$item3[$i] = $match['participants'][$teamposition]['stats']['item3'];
			$item4[$i] = $match['participants'][$teamposition]['stats']['item4'];
			$item5[$i] = $match['participants'][$teamposition]['stats']['item5'];

			//CHECK if empty
			for($w=0;$w<6;$w++)
			{
				if(${'item' . $w}[$i] == 0)
				{
					${'item' . $w}[$i] = "img/noitem.png";
				} else 
				{
					${'item' . $w}[$i] = "http://opgg-static.akamaized.net/images/lol/item/" . ${'item' . $w}[$i] . ".png";
				}
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
		<div class="card-group">
			<?php for($i=0;$i<3;$i++){?>
				<div class="card mb-3">
					<img class="card-img-top" src="https://ddragon.leagueoflegends.com/cdn/img/champion/splash/<?php echo $championName[$i];?>_0.jpg">
					<div class="card-body text-center">
						<h2 class="card-title mb-0 text-primary"><?php echo $championName[$i];?></h2>
						<p class="card-text font-italic"><?php echo $championTitle[$i];?></p>
						<div class="row d-block">
							<img src="<?php echo $item0[$i]?>"><img src="<?php echo $item1[$i]?>"><img src="<?php echo $item2[$i]?>">
						</div>
						<div class="row d-block">
							<img src="<?php echo $item3[$i]?>"><img src="<?php echo $item4[$i]?>"><img src="<?php echo $item5[$i]?>">
						</div>
						<h1 class="mt-3"><?php echo $KDA[$i];?></h1>
					</div>
					<div class="card-footer"><?php echo $gamemode[$i] . " - " . $matchResult[$i];?></div>
				</div>
			<?php }?>
		</div>
		<div class="card-group">
			<?php for($i=3;$i<6;$i++){?>
				<div class="card">
					<img class="card-img-top" src="https://ddragon.leagueoflegends.com/cdn/img/champion/splash/<?php echo $championName[$i];?>_0.jpg">
					<div class="card-body">
						<h5 class="card-title"><?php echo $championName[$i];?></h5>
						<p class="card-text"><?php echo $championTitle[$i];?></p>

					</div>
				</div>
			<?php }?>
		</div>
	</div>
		
	<?php } ?>
	
    </body>

    </html>