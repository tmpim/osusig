# osu!next Signature Generator
This is a signature generator written in PHP for osu!next style signatures. The design is based on flyte's design. Live version with the generator can be found [here](http://lemmmy.pw/osusig).

## Usage
The generator itself is in `sig.php`. It accepts the following parameters:

* `uname` - **required** The username of the user you want to generate a signature for
* `colour` - **required** The background and stroke colour of the signature. Can take the name of any file in the templates directory, e.g. `pink`.
* `mode` - The gamemode you wish to view stats for. `0` is osu!standard, `1` is osu!taiko, `2` is osu!catch and `3` is osu!mania.
* `pp` - Where you want the pp to be displayed. Undefined does not show at all, `0` shows in place of the level, `1` shows next to the accuracy and `2` shows above the rank.
* `removeavmargin` - If specified (no value required), there will not be a 3px margin between the card's stroke and the profile picture.
* `flagshadow` - Adds a shadow to the flag
* `flagstroke` - Adds a stroke to the flag
* `countryrank` - Shows your country rank (experimental)

## Examples

| `colour` | `uname`  | `mode`      | Image |
| -------- | -------- | ----------- |:-----:|
| `green`  | `Lemmmy` | unspecified | ![](http://lemmmy.pw/osusig/sig.php?colour=green&uname=Lemmmy) |
| `pink`   | `peppy`  | `1` | ![](http://lemmmy.pw/osusig/sig.php?colour=pink&uname=peppy&mode=1) |

### Live examples

`http://lemmmy.pw/osusig/sig.php?colour=purple&uname=Lemmmy`
![](http://lemmmy.pw/osusig/sig.php?colour=purple&uname=Lemmmy)


`http://lemmmy.pw/osusig/sig.php?colour=blue&uname=rrtyui&pp=2`
![](http://lemmmy.pw/osusig/sig.php?colour=blue&uname=rrtyui&pp=2)


`http://lemmmy.pw/osusig/sig.php?colour=yellow&uname=jhlee0133&mode=3&pp=1`
![](http://lemmmy.pw/osusig/sig.php?colour=yellow&uname=jhlee0133&mode=3&pp=1)

## Requirements
The generator requires ImageMagick and memcached.
You will need a file in `p/` called `.priv.php` with contents like such:

    <?php
    define("AKEY", "your-osu!-api-key");

It is recommended to place a blocking `.htaccess` in this directory.

## Credits
Favicon is owned by Dean 'peppy' Herbert. The mode icons and flags are designed by Flyte and can be found at his pixelapse [here](https://www.pixelapse.com/flyte/projects/osu!designs/files/). `triangles.png` and `triangles2.png` are self-made.

## License
Everything except the following files are licensed under GPL-v3:

```
fonts/*
flags/*
modes/*
img/ctb.png
img/mania.png
img/taiko.png
img/osu.png
img/tris.png
```

The following files are licensed under AGPL-v3 and are from [this](https://github.com/ppy/osu-web) repository:
```
fonts/osu!font.ttf
flags/*
img/ctb.png
img/mania.png
img/taiko.png
img/osu.png
```