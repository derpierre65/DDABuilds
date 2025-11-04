<?php

namespace App\Services;

class Steam
{
	public function getUserInfo($steamID)
	{
		// TODO https?
		$url = file_get_contents('http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='.config('services.steam.apiKey').'&steamids='.$steamID);
		$data = json_decode($url, true);

		return $data['response']['players'][0] ?? null;
	}
}