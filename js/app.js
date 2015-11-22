$(function() {
    var colour = "pink";
    var mode = 0;
    var ppmode = -1;

    var lc = 0;

    function reloadSig() {
        if (new Date().getTime() >= lc + 500) {
            lc = new Date().getTime();

            var url = "sig.php?";

            url += "colour=" + colour.replace('#', "hex");
            url += "&uname=" + encodeURIComponent(($("input[name=uname]").val().replace(" ", "%20") || "Lemmmy"));
            if(mode > 0) url += "&mode=" + mode;
            url += (ppmode >= 0 ? "&pp=" + ppmode : "");

            url += ($("input[name=country-rank]").prop('checked') ? "&countryrank" : "");

            url += ($("input[name=adv-av-margin]").prop('checked') ? "&removeavmargin" : "");

            url += ($("input[name=adv-flag-shadow]").prop('checked') ? "&flagshadow" : "");
            url += ($("input[name=adv-flag-stroke]").prop('checked') ? "&flagstroke" : "");

            url += ($("input[name=adv-dark-header]").prop('checked') ? "&darkheader" : "");
            url += ($("input[name=adv-dark-triangles]").prop('checked') ? "&darktriangles" : "");

            url += ($("input[name=adv-opaque-avatar]").prop('checked') ? "&opaqueavatar" : "");
            url += ($("input[name=adv-avatar-rounding]").prop('checked') ? "&avatarrounding=" + $("input[name=adv-avatar-rounding-num]").val() : "");

            url += ($("input[name=adv-ranked-score]").prop('checked') ? "&rankedscore" : "");

            url += ($("select[name=adv-online-indicator]").val() !== '0' ? "&onlineindicator=" + $("select[name=adv-online-indicator]").val() : "");

            url += ($("input[name=adv-xp-bar]").prop('checked') ? "&xpbar" : "");
            url += ($("input[name=adv-xp-bar-hex]").prop('checked') ? "&xpbarhex" : "");

            var fullurl = "http://lemmmy.pw/osusig/" + url;

            $("img.preview").remove();

            var newimg = $("<img />", {
                class: "preview lazy",
                width: 338,
                height: 94,
                src: url
            });

            $("#previewarea").append(newimg);

            $("input[name=out]").val("[img]" + fullurl + "[/img]");
        }
    }

    $("#regen").click(function(e) {
        e.preventDefault();
        e.stopPropagation();
        reloadSig();
        $("#uname").select();
    });

    $("#uname").keypress(function(e) {
        if(e.which === 13) {
            e.preventDefault();
            e.stopPropagation();
            reloadSig();
            this.select();
        }
    }).focus();

    $("#out").click(function() {
        this.select();
    });

    $("#hex-picker").spectrum({
        color: $(".colours li.selected").css("background-color"),
        showInput: true,
        preferredFormat: "hex",
        flat: true,
        chooseText: "",
        cancelText: "",
        change: function(clr) {
            $("#colour-hex").css("background-color", clr.toHexString());
            colour = clr.toHexString();
        },
        move: function(clr) {
            $("#colour-hex").css("background-color", clr.toHexString());
            colour = clr.toHexString();
        },
    });

    $($("#hex-picker").spectrum("container")).hide();

    $(".colours li").each(function() {
        $(this).click(function() {
            $(".colours li").each(function() {
                 $(this).removeClass("selected");
            });
            $(this).addClass("selected");

            colour = $(this).attr('id').replace("colour-", "");

            if ($(this).attr('id') == 'colour-hex') {
                $($("#hex-picker").spectrum("container")).slideDown();
            } else {
                $("#colour-hex").css("background-color", $(this).css("background-color"));
                $("#hex-picker").spectrum("set", $(this).css("background-color"));
                $($("#hex-picker").spectrum("container")).slideUp();
            }
        });
    });

    $(".modes li").each(function() {
        $(this).click(function() {
            $(".modes li").each(function() {
                 $(this).removeClass("selected");
            });
            $(this).addClass("selected");

            if ($(this).hasClass("osu")) {
                mode = 0;
            } else if ($(this).hasClass("taiko")) {
                mode = 1;
            } else if ($(this).hasClass("ctb")) {
                mode = 2;
            } else if ($(this).hasClass("mania")) {
                mode = 3;
            }
        });
    });

    $(".ppmodes li").each(function() {
        $(this).click(function() {
            $(".ppmodes li").each(function() {
                 $(this).removeClass("selected");
            });
            $(this).addClass("selected");

            ppmode = $(this).attr('id').replace("ppmode-", "");
        });
    });

    $("input[name=adv-avatar-rounding]").change(function() {
        $("input[name=adv-avatar-rounding-num]").prop('disabled', !$(this).is(":checked"));
    });

    $("input[name=adv-xp-bar]").change(function() {
        $("input[name=adv-xp-bar-hex]").prop('disabled', !$(this).is(":checked"));
    });
});