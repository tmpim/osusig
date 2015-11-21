<html>
    <head>
        <title>osu!next Signature Generator</title>

        <link href='https://fonts.googleapis.com/css?family=Exo+2:400,300' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/app.css">

        <link rel="icon" href="favicon.ico">

        <meta charset='UTF-8'>
        <meta name='keywords' content='osu, next, signature, generator, forum, sig'>
        <meta name='description' content='A signature generator in the style of osu!next!'>
        <meta name='author' content='Lemmmy'>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#bb1177">
        <meta name='handheldfriendly' content='true'>
        <meta name='mobileoptimized' content='480'>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>
        <script src="js/spectrum.js"></script>
        <script src="js/app.js"></script>
    </head>
    <body>
        <div class="page-header">
            <span class="title">osu!next Signature Generator</span><br>
            <span class="shameless-self-promotion">by <a href="https://osu.ppy.sh/u/4656511">Lemmmy</a> - source code available <a href="https://github.com/Lemmmy/osusig">here</a></span>
        </div>
        <header>Choose a colour</header>
        <article>
            <ul class="colours">
                <li style="background-color: #e33" id="colour-red">red</li>
                <li style="background-color: #e83" id="colour-orange">orange</li>
                <li style="background-color: #fc2" id="colour-yellow">yellow</li>
                <li style="background-color: #ad0" id="colour-green">green</li>
                <li style="background-color: #6cf" id="colour-blue">blue</li>
                <li style="background-color: #86e" id="colour-purple">purple</li>
                <li style="background-color: #f6a" id="colour-bpink">pink</li>
                <br>
                <li style="background-color: #25e" id="colour-darkblue">dark blue</li>
                <li style="background-color: #b17" id="colour-pink" class="selected">dark pink</li>
                <li style="background-color: #000" id="colour-black">black</li>
                <br>
                <br>
                <li style="background-color: #b17; background-repeat: no-repeat; background-position: center; text-shadow: 0 1px 6px rgba(0, 0, 0, 0.4), 0 1px 3px rgba(0, 0, 0, 0.3), 0 1px 1px rgba(0, 0, 0, 0.6)" id="colour-hex">custom</li>
                <br>
                <br>
                <input type="text" id="hex-picker" />
            </ul>
        </article>
        <header>Set your data</header>
        <article style="text-align: center;">
            <label for="uname">Name</label>
            <input type="text" placeholder="Lemmmy" name="uname" id="uname" />
            <br><br>
            <label for="mode">Mode</label>
            <ul class="modes">
                <li class="osu selected" id="mode-osu"><img src="img/osu.png"><span>osu!</span></li>
                <li class="taiko" id="mode-taiko"><img src="img/taiko.png"><span>osu!taiko</span></li>
                <li class="ctb" id="mode-ctb"><img src="img/ctb.png"><span>osu!ctb</span></li>
                <li class="mania" id="mode-mania"><img src="img/mania.png"><span>osu!mania</span></li>
            </ul>
            <br><br>
            <label for="ppmode">Performance Points Display</label>
            <ul class="ppmodes">
                <li id="ppmode--1" class="selected">don't show</li>
                <li id="ppmode-0">replace level</li>
                <li id="ppmode-1">after accuracy</li>
                <li id="ppmode-2">above rank</li>
            </ul>
            <br><br>
            <input type="checkbox" name="country-rank" /> Display your country rank (experimental)
        </article>
        <header>Your Signature</header>
        <article style="text-align: center;">
            <div id="previewarea">
                <img src="sig.php?colour=pink&uname=Lemmmy" class="preview lazy" width="338" height="94">
            </div>
            <br>
            <label for="out">BBCode:</label>
            <input type="text" name="out" id="out" value="[img]http://lemmmy.pw/osusig/sig.php?colour=pink&uname=Lemmmy[/img]" style="width: 100%; text-align: center" readonly />
            <br><br>
            <a href="#" id="regen" class="button">Generate</a>
        </article>
        <header>Advanced options</header>
        <article style="text-align: center;" class="twocol">
            <div class="twocol-col">
                <span>
                    <input type="checkbox" name="adv-av-margin" /> Remove extra margin from avatar
                </span>
                <span>
                    <input type="checkbox" name="adv-flag-shadow" /> Add a shadow behind the flag
                </span>
                <span>
                    <input type="checkbox" name="adv-flag-stroke" /> Add a white outline to the flag
                </span>
                <span>
                    <input type="checkbox" name="adv-opaque-avatar" /> Add a background behind the avatar
                </span>
            </div>
            <div class="twocol-col">
                <span>
                    <input type="checkbox" name="adv-dark-triangles" /> Darken the triangles on the header
                </span>
                <span>
                    <input type="checkbox" name="adv-dark-header" /> Use dark text on the header
                </span>
                <span>
                    <input type="checkbox" name="adv-avatar-rounding" /> Set a custom rounding for the avatar
                    <input type="number" name="adv-avatar-rounding-num" min="0" value="4" disabled class="smallnumber"/>
                </span>
                <span>
                    <input type="checkbox" name="adv-ranked-score" /> Show your ranked score (replaces playcount)
                </span>
            </div>
        </article>
        <?php include_once('/var/www/req/.analytics.php'); ?>
    </body>
</html>