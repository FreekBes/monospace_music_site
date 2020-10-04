<?php
    // error_reporting(0); ini_set('display_errors', 0);

    require_once("helper.php");

    $settings = json_decode(file_get_contents("settings.json"), true);
    $music = json_decode(file_get_contents("music.json"), true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Monospace</title>
    <meta name="description" content="The official home of Monospace">
    <meta property="og:type" content="website" />
    <meta name="og:site_name" content="Monospace"/>
    <meta property="og:title" content="Monospace" />
    <meta property="og:description" content="The official home of Monospace" />
    <meta property="og:image" content="https://freekb.es/monospace/images/icon.jpg" />
    <meta property="og:image:alt" content="The Monospace logo" />

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
<body>
    <header>
        <img id="logo" src="images/logo.png" />
        <nav>
            <a href="#home">Home</a>
            <a href="#music">Discography</a>
        </nav>
        <hr />
    </header>
    <main>
        <section id="home" style="background: <?php echo $settings["home"]["background"]["color"]; ?>;">
            <?php
                if ($settings["home"]["standout"]["enabled"]) {
                    ?>
                    <div id="standout">
                        <img id="standout-image" src="<?php echo $settings["home"]["standout"]["image"]; ?>" />
                        <div id="standout-next-to-image">
                            <h2><?php echo $settings["home"]["standout"]["header"]; ?></h2>
                            <hr />
                            <p><?php echo $settings["home"]["standout"]["text"]; ?></p>
                            <?php
                                if ($settings["home"]["standout"]["countdown_enabled"]) {
                                    ?>
                                    <div id="standout-countdown" data-countdown-link="<?php echo $settings["home"]["standout"]["countdown_reached_link"]; ?>" data-countdown-text="<?php echo $settings["home"]["standout"]["countdown_reached_text"]; ?>" data-countdown-to="<?php echo $settings["home"]["standout"]["countdown_to"]; ?>">Time remaining: <i>calculating...</i></div>
                                    <script>
                                        var countdownbox = document.getElementById("standout-countdown");
                                        var countdownTo = new Date(countdownbox.getAttribute("data-countdown-to")).getTime();
                                        setInterval(function() {
                                            var now = new Date().getTime();
                                            var distance = countdownTo - now;
                                            
                                            if (distance > 0) {
                                                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                                var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                                countdownbox.innerHTML = "Time remaining:"+(window.innerWidth < 400 ? "<br/>" : " ")+"<i>" + days + "d " + hours + "h " + minutes + "m " + seconds + "s</i>";
                                            }
                                            else {
                                                countdownbox.innerHTML = '<a href="'+countdownbox.getAttribute("data-countdown-link")+'" target="_blank">'+countdownbox.getAttribute("data-countdown-text")+'</a>';
                                            }
                                        }, 1000);
                                    </script>
                                    <?php
                                }
                            ?>
                        </div>
                    </div>
                    <?PHP
                }
            ?>
        </section>
        <section style="background: <?php echo $settings["music"]["background"]["color"]; ?>;">
            <h2 id="music">Discography</h2>
            <hr />
            <div id="discography">
                <?php
                    $isrcs = array();
                    foreach($music as $release) {
                        if (!$settings["music"]["tracks_instead_of_albums"]) {
                            ?>
                                <a class="release" href="music.php?r=<?php echo release_to_link_parameter($release); ?>">
                                    <img class="release-cover" src="<?php echo $release["coverart"]; ?>" />
                                    <?PHP
                                        if (!empty($release["platforms"])) {
                                            $servicekey = array_keys($release["platforms"])[0];
                                            $service = get_service_info($servicekey);
                                            ?>
                                            <div class="play-now" title="<?php echo $service[2].' '.$service[0]; ?>" onclick="window.open('<?php echo create_album_link($servicekey, $release['platforms'][$servicekey]); ?>'); event.preventDefault(); return false;" style="background-image: url('<?php echo $service[1]; ?>');"></div>
                                    <?PHP } else if ($release["type"] == "single" || $release["type"] == "featured") {
                                        if (!empty($release["tracks"][0]["platforms"])) {
                                            $servicekey = array_keys($release["tracks"][0]["platforms"])[0];
                                            $service = get_service_info($servicekey);
                                            ?>
                                            <div class="play-now" title="<?php echo $service[2].' '.$service[0]; ?>" onclick="window.open('<?php echo create_track_link($servicekey, $release['tracks'][0]['platforms'][$servicekey]); ?>'); event.preventDefault(); return false;" style="background-image: url('<?php echo $service[1]; ?>');"></div>
                                    <?PHP } } ?>
                                </a>
                            <?php
                        }
                        else {
                            foreach ($release["tracks"] as $track) {
                                if (!in_array($track["isrc"], $isrcs) && (!track_is_extended_or_radio_mix($track) || !$settings["music"]["hide_extended_and_radio_mixes"])) {
                                    ?>
                                        <a class="release" href="music.php?r=<?php echo release_to_link_parameter($release); ?>&t=<?php echo track_to_link_parameter($track); ?>">
                                            <img class="release-cover" src="<?php echo $track["coverart"]; ?>" />
                                            <?PHP
                                                if (!empty($track["platforms"])) {
                                                    $servicekey = array_keys($track["platforms"])[0];
                                                    $service = get_service_info($servicekey);
                                                    ?>
                                                    <div class="play-now" title="<?php echo $service[2].' '.$service[0]; ?>" onclick="window.open('<?php echo create_track_link($servicekey, $track['platforms'][$servicekey]); ?>'); event.preventDefault(); return false;" style="background-image: url('<?php echo $service[1]; ?>');"></div>
                                            <?PHP } ?>
                                        </a>
                                    <?php
                                    array_push($isrcs, $track["isrc"]);
                                }
                            }
                        }
                    }
                ?>
            </div>

            <?php
                if ($settings["music"]["background"]["enabled"]) {
                    echo '<img class="section-bg" src="'.$settings["music"]["background"]["href"].'" />';
                }
            ?>
        </section>

        <?php
            if ($settings["home"]["background"]["enabled"]) {
                if (!$settings["home"]["background"]["random_select_from_discography"]) {
                    echo '<img class="section-bg" src="'.$settings["home"]["background"]["href"].'" />';
                }
                else {
                    echo '<img class="section-bg" src="'.get_random_background($music).'" />';
                }
            }
        ?>
    </main>
    <footer>

    </footer>
</body>
</html>