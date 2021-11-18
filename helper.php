<?php
	function create_album_link($service, $id) {
		switch ($service) {
			case "spotify":
				return "https://open.spotify.com/album/".$id;
			case "youtube":
				return "https://www.youtube.com/playlist?list=".$id;
			case "soundcloud":
				return "https://soundcloud.com/".$id;
			case "deezer":
				return "https://www.deezer.com/album/".$id;
			case "amazon":
				return "https://www.amazon.com/dp/".$id;
			case "applemusic":
				return "https://music.apple.com/ca/album/".$id;
			case "googleplay":
				return "https://play.google.com/store/music/album/?id=".$id;
			case "bandcamp":
			default:
				return $id;
		}
	}

	function create_track_link($service, $id) {
		switch ($service) {
			case "spotify":
				return "https://open.spotify.com/track/".$id;
			case "youtube":
				return "https://www.youtube.com/watch?v=".$id;
			case "soundcloud":
				return "https://soundcloud.com/".$id;
			case "deezer":
				return "https://www.deezer.com/track/".$id;
			case "amazon":
				return "https://www.amazon.com/dp/".$id;
			case "bandcamp":
			default:
				return $id;
		}
	}

	function get_service_info($service) {
		switch ($service) {
			case "spotify":
				return ["Spotify", "images/icons/spotify.png", "Play via"];
			case "youtube":
				return ["YouTube", "images/icons/youtube.png", "Play via"];
			case "soundcloud":
				return ["Soundcloud", "images/icons/soundcloud.png", "Play via"];
			case "deezer":
				return ["Deezer", "images/icons/deezer.png", "Play via"];
			case "amazon":
				return ["Amazon", "images/icons/amazon.png", "Buy on"];
			case "applemusic":
				return ["Apple Music", "images/icons/apple.png", "Play via"];
			case "googleplay":
				return ["Google Play", "images/icons/gplay.png", "Buy on"];
			case "bandcamp":
				return ["Bandcamp", "images/icons/bandcamp.png", "Buy on"];
			default:
				return [ucfirst($service), "images/icons/play.png", "Play via"];
		}
	}

	function format_seconds($seconds) {
		if ($seconds < 0) {
			return "unknown duration";
		}
		$s = floor($seconds % 60);
		$m = floor(($seconds / 60) % 60);
		$u = floor((($seconds / 60) / 60 ) % 60);
		if ($m < 10) {
			$m = '0' . $m;
		}
		if ($s < 10) {
			$s = '0' . $s;
		}
		if ($u < 1) {
			return ($m . ':' . $s);
		}
		else if ($u >= 1) {
			return ($u . ':' . $m . ':' . $s);
		}
	}

	function release_to_link_parameter($release) {
		return preg_replace("/[^a-zA-Z0-9\-]+/", "", str_replace(" ", "-", strtolower($release["title"])));
	}

	function track_to_link_parameter($track) {
		return preg_replace("/[^a-zA-Z0-9\-]+/", "", str_replace(" ", "-", strtolower($track["title"])));
	}

	function track_is_extended_or_radio_mix($track) {
		return (strpos(strtolower($track["title"]), "extended") !== false || strpos(strtolower($track["title"]), "radio") !== false);
	}

	function get_release_by_link_parameter($music, $parameter) {
		foreach ($music as $release) {
			if (release_to_link_parameter($release) == $parameter) {
				return $release;
			}
		}
		return null;
	}

	function get_release_by_upc($music, $upc)
	{
		foreach ($music as $release) {
			if ($release["upc"] == $upc) {
				return release_to_link_parameter($release);
			}
		}
		return null;
	}

	function get_track_num_by_link_parameter($release, $parameter) {
		for ($i = 0; $i < count($release["tracks"]); $i++) {
			if (track_to_link_parameter($release["tracks"][$i]) == $parameter) {
				return $i;
			}
		}
		return -1;
	}

	function get_track_url_by_isrc($music, $isrc)
	{
		foreach ($music as $release) {
			foreach ($release["tracks"] as $track) {
				if ($track["isrc"] == $isrc) {
					return array(release_to_link_parameter($release), track_to_link_parameter($track));
				}
			}
		}
		return array(null, null);
	}

	function get_random_background($music) {
		$backgrounds = array();
		foreach ($music as $release) {
			if (!empty($release["background"])) {
				array_push($backgrounds, $release["background"]);
			}
			foreach ($release["tracks"] as $track) {
				if (!empty($track["background"])) {
					array_push($backgrounds, $track["background"]);
				}
			}
		}
		$backgrounds = array_unique($backgrounds);
		return $backgrounds[array_rand($backgrounds)];
	}

	function release_type_to_name($type) {
		switch ($type) {
			case "album":
				return "album";
			case "single":
				return "single";
			case "maxi-single":
				return "maxi-single";
			case "ep":
				return "EP";
			case "featured":
				return "release";
			default:
				return "release";
		}
	}
?>