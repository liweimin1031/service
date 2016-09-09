var myChart,subChart, leftChart, rightChart;
var optionobj;
var pkmapobj;// = new OptionPkmap(jsonData);
var domCode = document.getElementById('sidebar-code');
var domGraphic = document.getElementById('graphic');
var domMain = document.getElementById('main');
var domMainLeft = document.getElementById('main2_left');
var domMainRight = document.getElementById('main2_right');
var subMain = document.getElementById('submain');
var iconResize = document.getElementById('icon-resize');
var needRefresh = false;
var echartsClickId = '__echarts_click_id';

var hash = 'basic';
pkmapIndex = 0;
pkdimIndex = 0;
groupIndex = 0;

var curTheme="macarons";
function requireCallback (ec, defaultTheme) {
    curTheme = defaultTheme;
    echarts = ec;
    refresh();
    if(myChart) {
        window.onresize = myChart.resize;
    }
}

function refreshFilters() {
    var studentSelector = $('#pkmap-select');
    studentSelector.empty();
    var k;
    var pks = pkmapobj.getPkKeys();
    if (studentSelector && pks && pks.length) {
        var studentHtml = '';
        for(k=0;k<pks.length;k++) {
          if(k == pkmapIndex) {
            studentHtml += '<option selected="true" value='+k+' name="'+pks[k]+'">'+pks[k]+'</option>';
          } else {
            studentHtml += '<option value='+k+' name="'+pks[k]+'">'+pks[k]+'</option>';
          }
        }
        studentSelector.html(studentHtml);
        $(studentSelector).on('change', function(){
            studentChange(parseInt($(this).val()));
        });
        function studentChange(value){
            $(studentSelector).val(value);
            pkmapIndex = value;
            myChart.setOption(pkmapobj.getOption(pkmapIndex, pkdimIndex), true);
            subChart.setOption(pkmapobj.getSubOption(pkmapIndex), true);
        }
    }
    
    var dimSelector = $('#dimension-select');
    dimSelector.empty();
    var pkds = pkmapobj.pkDims;
    if (dimSelector && pkds && pkds.length) {
        var dimHtml = '';
        for(k=0;k<pkds.length;k++) {
          if(k == pkdimIndex) {
            dimHtml += '<option selected="true" value='+k+' name="'+pkds[k]+'">';
          } else {
            dimHtml += '<option value='+k+' name="'+pkds[k]+'">';
          }
          if(k == 0) {
            dimHtml += pkds[k]+'</option>';
          } else {
            dimHtml += '范畴：'+pkds[k]+'</option>';
          }
        }
        dimSelector.html(dimHtml);
        $(dimSelector).on('change', function(){
            dimChange(parseInt($(this).val()));
        });
        function dimChange(value){
            $(dimSelector).val(value);
            pkdimIndex = value;
            myChart.setOption(pkmapobj.getOption(pkmapIndex, pkdimIndex), true);
        }
    }
}

function refreshGroupFilters(obj) {
    var groupSelector = $('#group-select');
    groupSelector.empty();
    var k;
    var grps = obj.getGroups();
    if (groupSelector && grps && grps.length) {
        var groupHtml = '';
        for(k=0;k<grps.length;k++) {
          if(k == groupIndex) {
            groupHtml += '<option selected="true" value='+k+' name="'+grps[k]+'">'+grps[k]+'</option>';
          } else {
            groupHtml += '<option value='+k+' name="'+grps[k]+'">'+grps[k]+'</option>';
          }
        }
        groupSelector.html(groupHtml);
        $(groupSelector).on('change', function(){
            groupChange(parseInt($(this).val()));
        });
        function groupChange(value){
            $(groupSelector).val(value);
            groupIndex = value;
            myChart.setOption(obj.getOption(groupIndex), true);
        }
    }
}

function autoResize() {
    if ($(iconResize).hasClass('glyphicon-resize-full')) {
        focusCode();
        iconResize.className = 'glyphicon glyphicon-resize-small';
    }
    else {
        focusGraphic();
        iconResize.className = 'glyphicon glyphicon-resize-full';
    }
}

function focusCode() {
    domCode.className = 'col-md-8 ani';
    domGraphic.className = 'col-md-4 ani';
}

function focusGraphic() {
    domCode.className = 'col-md-4 ani';
    domGraphic.className = 'col-md-8 ani';
    if (needRefresh) {
        refresh();
    }
}

/*var editor = CodeMirror.fromTextArea(
    document.getElementById("code"),
    { lineNumbers: true }
);
editor.setOption("theme", 'monokai');


editor.on('change', function(){needRefresh = true;});
*/

function refresh(isBtnRefresh){
    if (isBtnRefresh) {
        needRefresh = true;
        focusGraphic();
        return;
    }
    clearEchartsClickPopup();
    needRefresh = false;
    if($("#main2").is(":visible")) {
        if (leftChart && leftChart.dispose) {
            leftChart.dispose();
            leftChart = undefined;
        }
        if (rightChart && rightChart.dispose) {
            rightChart.dispose();
            rightChart = undefined;
        }
    } else {
        if (myChart && myChart.dispose) {
            myChart.dispose();
            myChart = undefined;
        }
        if (subChart && subChart.dispose) {
            subChart.dispose();
            subChart = undefined;
        }
    }
    //(new Function(editor.doc.getValue()))();
    var currOption = option;
    var html = '';
    /*$("#pkmap-select").hide();
    $("#pkmap-label").hide();
    $("#dimension-label").hide();
    $("#dimension-select").hide();*/
    $("#main").show();
    $("#id_filters").hide();
    $('#group_filters').hide();
    $("#submain").empty();
    $("#submain").hide();
    $("#main2").hide();
    $('#main').css("height",'400px');
    var showChart = true;
    var show2Chart = false;
    switch(hash) {
        default:
        case "basic":
            optionobj = new OptionTop(jsonData);
            currOption = optionobj.getOption();
            optionobj.render();
            $("#submain").show();
            subChart = echarts.init(subMain, curTheme);
            subChart.setOption(optionobj.getSubOption(), true);

            //html = topHtml;
            break;
        case "personitem":
            optionobj = new OptionBasic(jsonData);
            currOption = optionobj.getOption();
            optionobj.render();
            //html = basicHtml;
            break;
        case "radar":
            show2Chart = true;
            $("#main").hide();
            $("#main2").show();
            optionobj = new OptionRadar(jsonData);
            leftOption = optionobj.getOption();
            //leftOption = currOption;
            optionobj.render();
            //currOption = optionradar;
            //html = radarHtml;
        case "dimension":
            optionobj = new OptionDimension(jsonData);
            rightOption = optionobj.getOption();
            optionobj.render();
            //currOption = optiondimension;
            //html = dimensionHtml;
            break;
        case "bubble":
            optionobj = new OptionBubble(jsonData);
            currOption = optionobj.getOption();
            optionobj.render();
            //currOption = optionbubble;
            //html = bubbleHtml;
            break;
        case "pkmap":
            //optionobj = new OptionPkmap(jsonData);
            currOption = pkmapobj.getOption(pkmapIndex, pkdimIndex);
            /*$("#pkmap-select").show();
            $("#pkmap-label").show();
            $("#dimension-select").show();
            $("#dimension-label").show();*/
            $("#id_filters").show();
            //html = pkmapHtml;
            $('#main').css("height",'500px');
            $("#submain").show();
            refreshFilters();
            subChart = echarts.init(subMain, curTheme);
            subChart.setOption(pkmapobj.getSubOption(pkmapIndex), true);
            optionobj = pkmapobj;
            pkmapobj.render();
            break;
        case "mci":
            optionobj = new OptionMci(jsonData);
            currOption = optionobj.getOption();
            optionobj.render();
            //currOption = optionmci;
            //html = mciHtml;
            break;
        case "groups":
            optionobj = new OptionGroups(jsonData);
            currOption = optionobj.getOption();
            optionobj.render();
            break;
        case "abilitysum":
            $('#group_filters').show();
            optionobj = new OptionAbilitySum(jsonData);
            refreshGroupFilters(optionobj);
            currOption = optionobj.getOption();
            optionobj.render();
            break;
        case "rank":
            optionobj = new OptionRank(jsonData);
            currOption = optionobj.getOption();
            //currOption = optionrank;
            //html = rankHtml;
            showChart = false;
            $('#main').css("height",'100%');
            optionobj.render();
            break;
    }
    //$("#chart_description").html(html);
      if(showChart) {
        if(show2Chart) {
            leftChart = echarts.init(domMainLeft, curTheme);
            rightChart = echarts.init(domMainRight, curTheme);
            leftChart.setOption(leftOption, true);
            rightChart.setOption(rightOption, true);
        } else {
            myChart = echarts.init(domMain, curTheme);
            window.onresize = myChart.resize;
            if(optionobj && optionobj.onclick) {
                var ecConfig = require('echarts/config');
                myChart.on(ecConfig.EVENT.CLICK, optionobj.onclick);
            }
            myChart.setOption(currOption, true);
        }
      } else {
        $(domMain).empty();
        $(domMain).append(currOption);
      }
    /*$("#id_dup_items").unbind();
    $("#id_dup_items").bind('mouseover', function() {
         myChart.setOption(optionDupBubble, true);
    });
    $("#id_dup_items").bind('mouseout', function() {
         myChart.setOption(currOption, true);
    });*/
}

function needMap() {
    var href = location.href;
    return href.indexOf('map') != -1
           || href.indexOf('mix3') != -1
           || href.indexOf('mix5') != -1
           || href.indexOf('dataRange') != -1;

}

var echarts;
(function() {
    // for echarts online home page
    require.config({
        paths: {
            echarts: '../../jslib/echarts/doc/example/www/js'
        }
    });
    launchExample();
    if(jsonData.data.rasch !== false) {
        pkmapobj = new OptionPkmap(jsonData);
    }
    report_init(jsonData);
})();

var isExampleLaunched;
function launchExample() {
    if (isExampleLaunched) {
        return;
    }

    // 按需加载
    isExampleLaunched = 1;
    require(
        [
            'echarts',
            'echarts/theme/macarons',
            'echarts/chart/line',
            'echarts/chart/bar',
            'echarts/chart/scatter',
            'echarts/chart/k',
            'echarts/chart/pie',
            'echarts/chart/radar',
            'echarts/chart/force',
            'echarts/chart/chord',
            'echarts/chart/gauge',
            'echarts/chart/funnel',
            'echarts/chart/eventRiver',
            'echarts/chart/venn',
            'echarts/chart/treemap',
            'echarts/chart/tree',
            'echarts/chart/wordCloud',
            'echarts/chart/heatmap',
            needMap() ? 'echarts/chart/map' : 'echarts'
        ],
        requireCallback
    );
}

function clearEchartsClickPopup() {
    var d = document.getElementById(
        echartsClickId
    );
    if(d) {
        d.onclick = null;
        d.innerHTML = '';
        document.body.removeChild(d);
        d = null;
    }
}

