<?php

    function Search($username);
	{
		//$username = $_POST['Username'];
		$usernameurl = rawurlencode($username);
		$api_key = 'RGAPI-68fcf9f5-b203-48ca-bf96-5f4755edc4e4';
		
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
		for($i=0;$i<5;$i++)
		{
			return $matchResult[$i];
		}
		$curl = curl_close();
		
	}
    ?>