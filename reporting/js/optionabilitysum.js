function OptionAbilitySum(jd) {
  var me = this;
  var paper = jd.data.paper.overall;
  var students = jd.data.students;
  var items = jd.data.items;
  var len = students.length;
  var sItems = students.sort(function(a,b) {
                       if(a.score < b.score) return -1;
                       else return 1;
                   });
  if(len%2) {
      var median = sItems[(len-1)/2]['score'];
  } else {
      var median = (sItems[len/2]['score']+sItems[len/2-1]['score'])/2.0;
  }
  var scores = [];
  for(var i = 0; i < sItems.length; i++) {
      scores.push(sItems[i]['score']);
  }
  var mLine = 10.0*median/paper.fullmark;
  paper.median = median;
  paper.minsc = sItems[0].score;
  paper.maxsc = sItems[len-1].score;
  var aLine = 10.0*paper.average/paper.fullmark;
  var markDist = [0];
  if(paper.fullmark%10) {
      var fixbit = 1;
  } else {
      var fixbit = 0;
  }
  for(var i = 1; i < 10; i++) {
      markDist.push((i*paper.fullmark/10).toFixed(fixbit));
  }
  markDist.push(paper.fullmark);
  var topHtml = ''; // good if average > 75%, bad if < 50%
    topHtml += '<div style="font-weight:bold"><span style="color:#24537e">平均成績是：'+paper.average+'</span>，<span style="color:orange">中位數成績是：'+paper.median+'</span>，标准差：'+standardDeviation(scores)+'</div>';
    var under60 = paper.distribution[0]+paper.distribution[1]+paper.distribution[2]+
                paper.distribution[3]+paper.distribution[4]+paper.distribution[5];
    var above60 = paper.distribution[6]+paper.distribution[7]+paper.distribution[8]+
                paper.distribution[9];
    topHtml += '<div style="color:#4d4d4d">成績低過'+markDist[6]+'(60%)分有'+under60+'人、超過'+markDist[6]+'(60%)分有'+above60+'人</div>';
    if(paper.average >= markDist[7]) {
      topHtml += '<div>本次測試整體表現優異</div>';
    } else if(paper.average >= markDist[6]) {
      topHtml += '<div>本次測試整體表現較好</div>';
    } else if(paper.average >= markDist[5]) {
      topHtml += '<div>本次測試整體表現一般</div>';
    } else if(paper.median < paper.average) {
      topHtml += '<div>本次測試整體表現較差</div>';
    }
  
  //paper.overall.ability_mean
  me.groups = [];
  me.groupsNum = [];
  var gps = jd.data.paper.groups;
  var groupedData = [];
  //var mina = paper.ability_range[0];
  //var maxa = paper.ability_range[1];
  for(i = 0; i < gps.length; i++) {
    var gp = gps[i];
    me.groups.push(gp.overall.group);
    me.groupsNum[i] = [];
    /*var gabs = gp.overall.student_ability.overall;
    var gn = 0;
    var ga = 0;
    for(var ss in gabs) {
        gn++;
        ga += parseFloat(gabs[ss]);
    }
    groupedData.push((ga/gn)-mina);*/
  }
  var abilRange = paper.ability_range;
  var diffRange = paper.difficulty_range;
  var low = Math.min(abilRange[0], diffRange[0]);
  var lowStage = Math.max(low, -5.0);
  var high = Math.max(abilRange[1], diffRange[1]);
  var highStage = Math.min(high, 5.0);
  var basiccategory = [];
  me.ability = [];
  me.difficulty = [];
  var needle = Math.floor(lowStage);
  if(high - low < 4) {
      var step = 0.5;
  } else {
      var step = 1.0;
  }
  while(needle < highStage) {
      basiccategory.push(needle+"");
      needle += step;
      me.ability.push(0);
      me.difficulty.push(0);
      for(i = 0; i < gps.length; i++) {
        me.groupsNum[i].push(0);
      }
  }
  basiccategory.push(needle+"");
  me.ability.push(0);
  me.difficulty.push(0);
  for(i = 0; i < gps.length; i++) {
      me.groupsNum[i].push(0);
  }
  var key;
  for(var t in items) {
      var dl = Math.max(Math.min(items[t].difficulty_logit,highStage),lowStage);
      key = parseInt((dl-lowStage)/step);
      me.difficulty[key]++;
  }
  for(var p in students) {
      var sa = Math.max(Math.min(students[p].ability,highStage),lowStage);
      key = parseInt((sa-lowStage)/step);
      me.ability[key]++;
  }
  for(i = 0; i < gps.length; i++) {
    var gp = gps[i];
    var gabs = gp.overall.student_ability.overall;
    for(p in gabs) {
        var sa = Math.max(Math.min(gabs[p],highStage),lowStage);
        key = parseInt((sa-lowStage)/step);
        me.groupsNum[i][key]++;
    }
  }
  var ki, klen;
  klen = me.ability.length;
  //me.ability[0] and me.difficulty[0] won't be both 0
  for(ki = klen-1; ki > 0; ki --) {
      if(me.ability[ki] > 0 || me.difficulty[ki] > 0) {
          break;
      }
      me.ability.splice(ki,1);
      me.difficulty.splice(ki,1);
      basiccategory.splice(ki,1);
      for(i = 0; i < gps.length; i++) {
          me.groupsNum[i].splice(ki,1);
      }
  }
  klen = me.ability.length;

  var ymax = Math.max.apply(null, me.ability);
  //$("#chart_description").html(topHtml);
  me.render = function() {
    var hds = ['學生能力分布比較'];
    var conts = [topHtml]
    var tabobj = new ZTabs("#chart_description", 1);
    tabobj.setTabs(hds, conts);
    tabobj.init();
    tabobj.render();
  }
  me.optiongroup = {
    title : {
        text: '學生能力分布比較(滿分'+paper.fullmark+'分、總人數'+len+'人)',
        textStyle : {
            color : '#4d4d4d',
            fontSize : 18
        }
    },
    tooltip : {
        trigger: 'axis',
        axisPointer : {            // 坐标轴指示器，坐标轴触发有效
            type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
        }
    },
    calculable : false,
    legend : {
        x: 'right',
        data : ['全部學生人數','分組學生人數']
    },
    xAxis : [
        {
            type : 'category',
            name : '學生能力',
            nameTextStyle : {
                color:'#4d4d4d'
            },
            splitArea : false,
            splitLine : false,
            axisLine : {
                onZero : false
            },
            axisLabel : {
                textStyle : {
                    fontSize:11
                },
                formatter:function(v) {
                    if(v == basiccategory[0]) {
                        return "低";
                    } else if(v == basiccategory[klen-1]) {
                        return "高";
                    }
                }
            },
            data : basiccategory
        }
    ],
    yAxis : [
        {
            type : 'value',
            name : '全部學生人數',
            nameTextStyle : {
                color:'#4d4d4d',
                fontSize : 12
            },
            splitArea : false,
            splitLine : false,
            axisLabel : {
                textStyle : {
                    fontSize:14
                }
            },
            max : ymax
        },{
            type : 'value',
            name : '分組學生人數',
            position : 'right',
            nameTextStyle : {
                color:'#4d4d4d',
                fontSize : 12
            },
            splitArea : false,
            splitLine : false,
            axisLabel : {
                textStyle : {
                    fontSize:14
                }
            }
        }
    ],
    series : [
        {
            name : '全部學生人數',
            type:'bar',
            data:me.ability,
            itemStyle : {
                normal : {
                    color : '#3dc4c3'
                }
            },
            tooltip : {
                trigger: 'item',
                formatter : function(params, ticket) {
                    if(params[2] >= 1) {
                        return params[0] + ' : '+params[2];
                    } else {
                        return params[0] + ' : 0';
                    }
                }
            }
        }
    ]
  };
  me.getOption = function(gIndex) {
    if(!gIndex) {
        gIndex = 0;
    }
    me.optiongroup.series[1] = 
        {
            name : '分組學生人數',
            type : 'line',
            itemStyle : {
                normal : {
                    color : 'purple'
                }
            },
            yAxisIndex : 1,
            tooltip : {
                trigger: 'item',
                formatter : function(params, ticket) {
                    var res = me.groups[gIndex]+'<br>';
                    if(params[2] >= 1) {
                        res += params[0] + ' : '+params[2];
                    } else {
                        res += params[0] + ' : 0';
                    }
                    return res;
                }
            },
            data : me.groupsNum[gIndex]
        }
    ;
    return me.optiongroup;
  }
  me.getGroups = function() {
    return me.groups;
  }
}
