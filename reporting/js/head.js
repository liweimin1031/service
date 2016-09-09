function fixFork () {
    var navMarginRight = 0;
    var bodyWidth = document.body.offsetWidth;
    var contnetWidth = $('#nav-wrap')[0].offsetWidth;
    if (bodyWidth < 1440) {
        navMarginRight = 150 - (bodyWidth - contnetWidth) / 2;
    }
    $('#nav')[0].style.marginRight = navMarginRight + 'px';
};


var idArrays = ["id_basic","id_rank","id_radar","id_personitem","id_bubble","id_pkmap","id_mci"];
function navigationClicked(divid) {
    if($("#"+divid).hasClass("navitem_active")) {
        return;
    }
    for(var i = 0; i < idArrays.length; i++) {
        if(divid == idArrays[i]) {
            $("#"+idArrays[i]).removeClass("navitem").addClass("navitem_active");
        } else {
            if($("#"+idArrays[i]).hasClass("navitem_active")) {
                $("#"+idArrays[i]).removeClass("navitem_active").addClass("navitem");
            }
        }
    }
    hash = divid.substr(3);
    refresh();
}
function report_init(jd) {
    var btnHtml = '';
    if(jd.data.paper.groups && jd.data.paper.groups.length > 1) {
        idArrays.push("id_groups");
        idArrays.push("id_abilitysum");
        btnHtml = '<li class="navitem" id="id_groups" >Groups</li>'
                + '<li class="navitem" id="id_abilitysum" >Ability Summary</li>';
    }
    $('#head')[0].innerHTML =
    '<div class="container">'
        + '<div class="navbar-collapse collapse" id="nav-wrap">'
          + '<ul class="nav navbar-nav " id="nav" style="max-width:100%;">'
            + 
            ('<li class="navitem_active" id="id_basic">Basic</li>'
                + '<li class="navitem" id="id_rank">Rank</li>'
                + '<li class="navitem" id="id_radar" >Radar</li>'
                + '<div style="height:42px;margin:5px 3px 0px 3px;float:left;border:1px solid #e0e0e0"></div>'
                + '<li class="navitem" id="id_personitem" >PI-Map</li>'
                + '<li class="navitem" id="id_bubble" >Bubble</li>'
                + '<div style="height:42px;margin:5px 3px 0px 3px;float:left;border:1px solid #e0e0e0"></div>'
                + '<li class="navitem" id="id_pkmap" >PKMAP</li>'
                + '<li class="navitem" id="id_mci" >MCI</li>'
                + '<div style="height:42px;margin:5px 3px 0px 3px;float:left;border:1px solid #e0e0e0"></div>'
                + btnHtml
                //+ '<li><a href="reports.html">Home</a></li>'
            )
          + '</ul>'
        + '</div><!--/.nav-collapse -->'
      + '</div>';

    for(var i = 0; i < idArrays.length; i++) {
        $("#"+idArrays[i]).bind("click", function() {
            navigationClicked($(this).attr('id'));
        });
    }
fixFork();
$(window).on('resize', fixFork);
}
function back2Top() {
    $("body,html").animate({scrollTop:0},1000);
    return false;
}


/*if (document.location.href.indexOf('local') == -1) {
    var hm = document.createElement("script");
    hm.src = "//hm.baidu.com/hm.js?4bad1df23f079e0d12bdbef5e65b072f";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(hm, s);
}*/


function standardDeviation(arr) {
    if(!arr || !arr.length) {
        return false;
    }
    var i, mean, len, sum, vari;
    sum = 0;
    len = arr.length;
    for(i = 0; i < len; i++) {
        sum += arr[i];
    }
    mean = sum*1.0/len;
    vari = 0;
    for(i = 0; i < len; i++) {
        vari += Math.pow(arr[i]-mean, 2);
    }
    var sd = Math.sqrt(vari/len);
    return sd.toFixed(2);
}
