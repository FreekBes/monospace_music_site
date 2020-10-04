<?php
    require_once("helper.php");

    $settings = json_decode(file_get_contents("settings.json"), true);
    $music = json_decode(file_get_contents("music.json"), true);

    $doSingleTrack = false;
    $release = null;
    $trackNum = -1;
    $pageTitle = "Monospace";
    $pageDesc = "The official home of Monospace";
    $pageImg = "https://freekb.es/monospace/images/icon.jpg";
    $pageImgDesc = "The Monospace logo";

    if (isset($_GET["r"]) && !empty($_GET["r"])) {
        $release = get_release_by_link_parameter($music, $_GET["r"]);
        if (empty($release)) {
            header("Location: index.php#music?error=Release%20not%20found");
            exit();
        }

        if (isset($_GET["t"]) && !empty($_GET["t"])) {
            $trackNum = get_track_num_by_link_parameter($release, $_GET["t"]);
            if ($trackNum > -1) {
                $doSingleTrack = true;
                $pageTitle = $release["tracks"][$trackNum]["title"] . " - Monospace";
                $pageDesc = $release["tracks"][$trackNum]["title"] . " by " . $release["tracks"][$trackNum]["artists"] . " - Monospace";
                $pageImg = $release["tracks"][$trackNum]["coverart"];
                $pageImgDesc = "Cover art";
            }
        }
        else if ($release["type"] == "featured" && count($release["tracks"]) == 1) {
            header("Location: ?r=".$_GET["r"]."&t=".track_to_link_parameter($release["tracks"][0]));
            exit();
        }
        else {
            $pageTitle = $release["title"] . " - Monospace";
            $pageDesc = $release["title"] . " by " . $release["artists"] . " - Monospace";
            $pageImg = $release["coverart"];
            $pageImgDesc = "Album art";
        }
    }
    else if (isset($_GET["c"]) && !empty($_GET["c"]))
    {
        if (strlen($_GET["c"]) == 13)
        {
            $param = get_release_by_upc($music, $_GET["c"]);
            if (!empty($param))
            {
                header("Location: music.php?r=".$param);
                exit();
            }
            else {
                header("Location: index.php#music?error=Release%20not%20found");
                exit();
            }
        }
        else if (strlen($_GET["c"]) == 12)
        {
            $params = get_track_url_by_isrc($music, $_GET["c"]);
            if (!empty($params[1]))
            {
                header("Location: music.php?r=".$params[0]."&t=".$params[1]);
                exit();
            }
            else {
                header("Location: index.php#music?error=Track%20not%20found");
                exit();
            }
        }
        else {
            header("Location: index.php#music?error=Invalid%20parameter");
            exit();
        }
    }
    else {
        header("Location: index.php#music?error=404 Not Found");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title><?PHP echo $pageTitle; ?></title>
    <meta name="description" content="<?PHP echo $pageDesc; ?>">
    <meta property="og:type" content="website" />
    <meta name="og:site_name" content="Monospace"/>
    <meta property="og:title" content="<?PHP echo $pageTitle; ?>" />
    <meta property="og:description" content="<?PHP echo $pageDesc; ?>" />
    <meta property="og:image" content="<?PHP echo $pageImg; ?>" />
    <meta property="og:image:alt" content="<?PHP echo $pageImgDesc; ?>" />

    <meta name="robots" content="index,follow">
    <meta name="googlebot" content="index,follow">
    <meta name="pinterest" content="nopin">
    
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style><?PHP readfile("styles.css"); ?></style>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
    <link rel="icon" type="image/ico" href="images/favicon.ico" />
    <meta name="theme-color" content="#b10000" />
    <meta name="copyright" content="Freek Bes">
    <meta name="owner" content="Freek Bes">
</head>
<body style="background: #111; height: auto; min-height: 100%;">
    <header>
        <img id="logo" src="images/logo.png" />
        <nav>
            <a href="index.php#home">Home</a>
            <a href="index.php#music">Discography</a>
        </nav>
        <hr />
    </header>
    <main style="height: auto;">
        <section style="min-height: 0px;">
            <?php if (!$doSingleTrack) { ?>
                <div id="album-details">
                    <img id="coverart" src="<?php echo $release["coverart"]; ?>" />
                    <h2 style="padding-top: 16px;"><?php echo $release["title"]; ?></h2>
                    <h3><?php echo $release["artists"]; ?></h3>
                    <hr />
                    <div id="play-via">
                        <?php
                            $platformKeys = array_keys($release["platforms"]);
                            $platformIds = array_values($release["platforms"]);
                            for ($i = 0; $i < count($platformKeys); $i++) {
                                $platformInfo = get_service_info($platformKeys[$i]);
                                echo '<button onclick="window.open(\''.create_album_link($platformKeys[$i], $platformIds[$i]).'\');"><img src="'.$platformInfo[1].'" /><span>'.$platformInfo[2].' '.$platformInfo[0].'</span></button>';
                            }
                        ?>
                    </div>
                    <p id="track-details">Originally released on the <?php echo date("jS \o\\f F, Y", strtotime($release["release_date"])); ?>.<br/><?php echo (!empty($release["upc"]) ? '<small>UPC: <code>'.$release["upc"].'</code></small>' : '<small>UPC: <i>none or unknown</i></small>'); ?></p>
                    <div id="tracklist">
                        <ol>
                            <?php
                                foreach ($release["tracks"] as $track) {
                                    ?>
                                    <li onclick="window.location.href='?r=<?php echo $_GET['r']; ?>&t=<?php echo track_to_link_parameter($track); ?>';"><a href="?r=<?php echo $_GET["r"]; ?>&t=<?php echo track_to_link_parameter($track); ?>"><span class="track-title"><?php echo $track["title"]; ?></span></a> <span class="track-duration"><?php echo format_seconds($track["duration"]); ?></span></li>
                                    <?php
                                }
                            ?>
                        </ol>
                    </div>
                </div>
            <?php } else { ?>
                <div id="album-details">
                    <img id="coverart" src="<?php echo $release["tracks"][$trackNum]["coverart"]; ?>" />
                    <h2 style="padding-top: 16px;"><?php echo $release["tracks"][$trackNum]["title"]; ?></h2>
                    <h3><?php echo $release["tracks"][$trackNum]["artists"]; ?></h3>
                    <hr />
                    <div id="play-via">
                        <?php
                            $platformKeys = array_keys($release["tracks"][$trackNum]["platforms"]);
                            $platformIds = array_values($release["tracks"][$trackNum]["platforms"]);
                            for ($i = 0; $i < count($platformKeys); $i++) {
                                $platformInfo = get_service_info($platformKeys[$i]);
                                echo '<button onclick="window.open(\''.create_track_link($platformKeys[$i], $platformIds[$i]).'\');"><img src="'.$platformInfo[1].'" /><span>'.$platformInfo[2].' '.$platformInfo[0].'</span></button>';
                            }
                        ?>
                    </div>
                    <p id="track-details">From the <?php echo release_type_to_name($release["type"]); ?> <a href="?r=<?php echo $_GET["r"]; ?>"><?php echo $release["title"]; ?></a> by <i><?php echo $release["artists"]; ?></i>.<br/>Originally released on the <?php echo date("jS \o\\f F, Y", strtotime($release["tracks"][$trackNum]["release_date"])); ?>.<br/><br/><?php echo (!empty($release["tracks"][$trackNum]["isrc"]) ? '<small>ISRC: <code>'.$release["tracks"][$trackNum]["isrc"].'</code></small>' : '<small>ISRC: <i>none or unknown</i></small>'); ?></p>
                </div>
            <?php } ?>
        </section>

        <?php
            if (!$doSingleTrack) {
                if (!empty($release["background"])) {
                    echo '<img class="page-bg" src="'.$release["background"].'" />';
                }
                else {
                    echo '<img class="page-bg" src="'.$release["coverart"].'" />';
                }
            }
            else {
                if (!empty($release["tracks"][$trackNum]["background"])) {
                    echo '<img class="page-bg" src="'.$release["tracks"][$trackNum]["background"].'" />';
                }
                else {
                    echo '<img class="page-bg" src="'.$release["tracks"][$trackNum]["coverart"].'" />';
                }
            }
        ?>
    </main>
</body>
</html>