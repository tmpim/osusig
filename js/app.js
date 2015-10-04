$(function() {
    var colour = "pink";
    var mode = 0;
    var ppmode = -1;
    
    var lc = 0;
    
    function reloadSig() {
        if (new Date().getTime() >= lc + 500) {
            lc = new Date().getTime();
            
            var url = "sig.php?";
            
            url += "colour=" + colour;
            url += "&uname=" + ($("input[name=uname]").val() || "Lemmmy");
            url += "&mode=" + mode;
            url += (ppmode >= 0 ? "&pp=" + ppmode : "");
            
            url += ($("input[name=adv-av-margin]").prop('checked') ? "&removeavmargin" : "");            
            
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
    
    $("#regen").click(reloadSig);
    
    $(".colours li").each(function() {
        $(this).click(function() {
            $(".colours li").each(function() {
                 $(this).removeClass("selected");
            });
            $(this).addClass("selected");
            
            colour = $(this).attr('id').replace("colour-", "");
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
});