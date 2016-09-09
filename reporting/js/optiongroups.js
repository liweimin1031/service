function OptionGroups(jd) {
  var me = this;
  var paper = jd.data.paper.overall;
  var students = jd.data.students;
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
  var gps = jd.data.paper.groups;
  var groupedText = [];
  var groupedData = [];
  for(i = 0; i < gps.length; i++) {
    var gp = gps[i];
    var gabs = gp.overall.student_ability.overall;
    var gn = 0;
    var ga = 0;
    for(var ss in gabs) {
        gn++;
        ga += parseFloat(gabs[ss]);
    }
    groupedText.push(gp.overall.group);
    groupedData.push(ga/gn);
  }
  var ymax = Math.max.apply(null, groupedData);
  var ymin = Math.min.apply(null, groupedData)-0.1;
  for(i = 0; i < groupedData.length; i++) {
      groupedData[i] = groupedData[i]-ymin;
  }
console.log(groupedText);
console.log(groupedData);
  //$("#chart_description").html(topHtml);
  me.render = function() {
    var hds = ['分組平均能力比較圖'];
    var conts = [topHtml]
    var tabobj = new ZTabs("#chart_description", 1);
    tabobj.setTabs(hds, conts);
    tabobj.init();
    tabobj.render();
  }
  me.optiongroup = {
    title : {
        text: '分組平均能力比較圖(滿分'+paper.fullmark+'分、總人數'+len+'人)',
        textStyle : {
            color : '#4d4d4d',
            fontSize : 18
        }
    },
    calculable : false,
    xAxis : [
        {
            type : 'category',
            name : '分組',
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
                }
            },
            data : groupedText
        }
    ],
    yAxis : [
        {
            type : 'value',
            name : '學生平均能力',
            nameTextStyle : {
                color:'#4d4d4d',
                fontSize : 12
            },
            splitArea : false,
            splitLine : false,
            axisLabel : {
                textStyle : {
                    fontSize:14
                },
                formatter:function(v) {
                    if(v == 0) {
                        return "低";
                    } else if(v == Math.ceil(ymax-ymin+1)) {
                        return "高";
                    }
                }
            },
            max : ymax-ymin+1
        }
    ],
    series : [
        {
            name:'成績分布',
            type:'bar',
            data:groupedData,
            itemStyle : {
                normal : {
                    color : '#3dc4c3'
                }
            },
            markLine : {
                symbolSize : 3,
                symbol : ['circle','none'],
                itemStyle:{
                    normal : {
                        borderWidth : 1.0,
                        color:'red',
                        label : {
                            //show : false,
                            formatter:function(value) {
                                return '全部學生平均能力';
                            },
                            textStyle : {
                                align : 'center',
                                baseline : 'bottom',
                                fontSize : 10
                            }
                        }
                    }
                },
                data : [[{xAxis:-0.5,yAxis:paper.ability_mean-ymin},{xAxis:gps.length,yAxis:paper.ability_mean-ymin}]]
            }
        }
    ]
  };
  me.getOption = function() {
    return me.optiongroup;
  }
}
