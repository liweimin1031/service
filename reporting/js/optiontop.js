function OptionTop(jd) {
  var me = this;
  var paper = jd.data.paper.overall;
  var ymax = Math.max.apply(null, paper.distribution);
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
    topHtml += '<div style="font-weight:bold"><span style="color:#24537e">平均成績是：'+paper.average+'</span>，<span style="color:orange">中位數成績是：'+paper.median+'</span>，標準差：'+standardDeviation(scores)+'</div>';
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
  
  //$("#chart_description").html(topHtml);
  me.render = function() {
    var hds = ['試卷成績分布圖'];
    var conts = [topHtml]
    var tabobj = new ZTabs("#chart_description", 1);
    tabobj.setTabs(hds, conts);
    tabobj.init();
    tabobj.render();
  }
  me.optiontop = {
    title : {
        //text: paper.title+' Distribution'
        text: '成績分布圖(滿分'+paper.fullmark+'分、總人數'+len+'人)',
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
    legend: {
        show : false,
        x : 'right',
        padding : [5,30,0,0],
        data:['成績分布']
    },
    toolbox: {
        show : false,
        feature : {
            /*mark : {show: true},
            dataView : {show: true, readOnly: false},
            magicType : {show: true, type: ['line', 'bar']},
            restore : {show: true},*/
            saveAsImage : {show: true}
        }
    },
    calculable : false,
    xAxis : [
        {
            type : 'category',
            name : '分數區間',
            nameTextStyle : {
                color:'#4d4d4d'
            },
            splitArea : false,
            splitLine : false,
            axisLabel : {
                textStyle : {
                    fontSize:11
                }
            },
            data : [markDist[0]+'~'+markDist[1],markDist[1]+'~'+markDist[2],markDist[2]+'~'+markDist[3],markDist[3]+'~'+markDist[4],markDist[4]+'~'+markDist[5],markDist[5]+'~'+markDist[6],markDist[6]+'~'+markDist[7],markDist[7]+'~'+markDist[8],markDist[8]+'~'+markDist[9],markDist[9]+'~'+markDist[10]]
        }
    ],
    yAxis : [
        {
            type : 'value',
            name : '學生數',
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
            max : ymax+2
        }
    ],
    series : [
        {
            name:'成績分布',
            type:'bar',
            data:paper.distribution,
            itemStyle : {
                normal : {
                    color : '#3dc4c3'
                }
            },
            tooltip : {
                trigger: 'item',
                formatter : function(params, ticket) {
                    if(params[2] >= 1) {
                        return params[0] + ' of '+params[1] + ' : <br>'+params[2];
                    } else {
                        return params[0] + ' of '+params[1] + ' : <br>0';
                    }
                }
            }
        }
    ]
  };
  var scales = [];
  for(var i = 1; i < paper.fullmark; i++) {
      if(i%5 == 0) {
          var yv = 0.4;
      } else {
          var yv = 0.2;
      }
      scales.push([{xAxis:i,yAxis:0},{xAxis:i,yAxis:yv}]);
  }
  me.suboption = {
    title : {
        text: '平均分與中位數比較圖',
        textStyle : {
            color : '#4d4d4d',
            fontSize : 18
        }
    },
    legend : {
        selectedMode : false,
        x : 'center',
        y : 'bottom',
        data : ['最低分('+paper.minsc+')','最高分('+paper.maxsc+')']
    },
    calculable : false,
    xAxis : [
        {
            type : 'value',
            min : 0,
            max : paper.fullmark,
            splitLine : false,
            splitNumber : paper.fullmark,
            splitArea : false,
            axisLine : false,
            axisLabel : {
                show : true,
                textStyle : {
                    fontSize:10,
                    baseline : 'bottom'
                },
                formatter : function(v) {
                    if(v%5 == 0) {
                        return v;
                    } else {
                        return '';
                    }
                    if(v > 0 && v < paper.fullmark) {
                        return '';
                    } else {
                        return v;
                    }
                }
            }
        }
    ],
    yAxis : [
        {
            //show : false,
            type : 'value',
            //splitArea : false,
            splitNumber : 7,
            axisLine : {
                show : false
            },
            splitLine: false,
            splitArea : {
                areaStyle : {
                    color : ['#f7f7f7','#f7f7f7','#ededed','#ededed','#ededed','#ededed','#ededed']
                }
            },
            axisLabel : false,
            min : -0.2,
            max : 1.2
        }
    ],
    series : [
        {
            type:'line',
            smooth : true,
            symbolSize : 0,
            itemStyle : { 
                normal : {
                    lineStyle:{
                        color:'#b6dc71'
                    },
                    areaStyle : {
                        color:'#b6dc71', 
                        type : 'default'
                    }
                }
            },
            data:[[paper.average,1],[paper.median,1]],
            markLine : {
                symbol : 'none',
                itemStyle : {
                    normal : {
                        lineStyle : {
                            type : 'solid',
                            width : 3
                        },
                        label : {
                            textStyle : {
                                //align : paper.average>paper.median?'left':'right',
                                //baseline : 'top'
                                fontSize : 18
                            },
                            formatter : function(v) {
                                var tmp = '平均分:('+paper.average+'分)';
                                if(paper.average>paper.median) {
                                    return '    '+tmp;
                                } else {
                                    return tmp+'   ';
                                }
                            }
                        },
                        color : '#24537e'
                    }
                },
                data : [
                    [{xAxis:paper.average,yAxis:0,value:'average'},{xAxis:paper.average,yAxis:1.2}]
                ]
            },
        },
        {
            type:'scatter',
            data:[{xAxis:0,yAxis:-0.1,value:1}],
            symbolSize:0,
            markLine : {
                symbol : 'none',
                itemStyle : {
                    normal : {
                        lineStyle : {
                            type : 'solid',
                            width : 3
                        },
                        label : {
                            textStyle : {
                                //align : paper.average>paper.median?'right':'left',
                                //baseline : 'top'
                                fontSize : 18
                            },
                            formatter : function(v) {
                                var tmp = '中位數:('+paper.median+'分)';
                                if(paper.average>paper.median) {
                                    return tmp+'   ';
                                } else {
                                    return '    '+tmp;
                                }
                            }
                        },
                        color : '#f09034'
                    }
                },
                data : [
                    [{xAxis:paper.median,yAxis:1,value:'median'},{xAxis:paper.median,yAxis:-0.2}]
                ]
            }
        },
        {
            name : '最高分('+paper.maxsc+')',
            type : 'scatter',
            symbol : 'circle',
            symbolSize:10,
            itemStyle : {
                normal : {
                    color : 'green'
                }
            },
            data : [[paper.maxsc, 0.5]]
        },
        {
            name : '最低分('+paper.minsc+')',
            type : 'scatter',
            symbol : 'circle',
            symbolSize:10,
            itemStyle : {
                normal : {
                    color : 'red'
                }
            },
            data : [[paper.minsc, 0.5]],
            markLine : {
                symbol : 'none',
                itemStyle : {
                    normal : {
                        lineStyle : {
                            type : 'solid',
                            width : 0.5
                        },
                        label : {
                            show : false
                        },
                        color : '#4d4d4d'
                    }
                },
                data : scales
            }
        }
    ]
  };
  me.getOption = function() {
    return me.optiontop;
  }
  me.getSubOption = function() {
    return me.suboption;
  }
}
