function OptionMci(jd) {
  var me = this;
  var paper = jd.data.paper.overall;
  var items = jd.data.items;
  var students = jd.data.students;
  var mciData = [];
  var mciNum = [0,0,0,0];
  //var mciNum = paper.mci;
  //var yLine = paper.fullmark>>1;
  var yLine = 0.5;
  for(var p in students) {
      var stu = students[p];
      var tsc = stu.score/paper.fullmark;
      mciData.push([Math.min(stu.mci,0.6), tsc, stu.student]);
      if(tsc >= yLine) {
          if(stu.mci <= 0.3) {
              mciNum[0]++;
          } else {
              mciNum[1]++;
          }
      } else {
          if(stu.mci <= 0.3) {
              mciNum[3]++;
          } else {
              mciNum[2]++;
          }
      }
  }
  var len = students.length;
  var tps = {'A':'<p>A. '+mciNum[0]+'('+(100*mciNum[0]/len).toFixed(2)+'%)個學生表現良好且表現穩定</p>',
          'B': '<p>B. '+mciNum[1]+'('+(100*mciNum[1]/len).toFixed(2)+'%)個學生表現良好但偶爾較為粗心</p>',
          'C': '<p>C. '+mciNum[2]+'('+(100*mciNum[2]/len).toFixed(2)+'%)個學生學習能力較差且未能較好掌握學習內容，需要提高</p>',
          'D': '<p>D. '+mciNum[3]+'('+(100*mciNum[3]/len).toFixed(2)+'%)個學生表現不穩定且未能掌握學習內容</p>'};
  var mciHtml = '';
  mciHtml += '<p>成績高于50%表示學習較好</p>';
  mciHtml += '<div style="color:#4d4d4d">';
  mciHtml += tps['A'];
  mciHtml += tps['B'];
  mciHtml += tps['C'];
  mciHtml += tps['D'];
  mciHtml += '</div>';
  //$("#chart_description").html(mciHtml);
  me.render = function() {
    var hds = ['學生總體表現'];
    var conts = [mciHtml]
    var tabobj = new ZTabs("#chart_description", 1);
    tabobj.setTabs(hds, conts);
    tabobj.init();
    tabobj.render();
  }
    me.optionmci = {
      title : {
          text : '學生總體表現分析',
          textStyle : {
              color : '#4d4d4d'
          }
      },
      //color : ['green', 'blue'],
      tooltip : {
          trigger: 'item',
          //backgroundColor: 'rgba(255,0,255,0.5)',
          showDelay : 0,
          axisPointer:{
              show: true,
              type : 'shadow'
          },
          formatter: function (params,ticket,callback) {
              //console.log(params)
              var res = '學生 : '+params[2][2]+'<br/>';
              res += "(得分:"+parseInt(params[2][1]*paper.fullmark)+", 正確率: "+(100*params[2][1]).toFixed(2)+"%)";
              return res;
          }
      },
      legend: {
          selectedMode : false,
          x : 'right',
          padding : [5,30,0,0],
          data:['學生']
      },
      toolbox: {
          show : false,
          feature : {
              /*mark : {show: true},
              dataZoom : {show: true},
              dataView : {show: true, readOnly: false},
              restore : {show: true},*/
              saveAsImage : {show: true}
          }
      },
      xAxis : [
          {
              type : 'value',
              nameLocation : 'end',
              position : 'bottom',
              splitNumber : 6,
              splitLine : false,
              splitArea : false,
              /*splitArea : {
                  areaStyle : {
                      color : ['#f1f1f1','#fcfcfc','#fcfcfc','#fcfcfc','#f1f1f1','#f1f1f1']
                  }
              },*/
              max : 0.6,
              min : 0.0,
              splitLine : false,
              axisLine : {
                onZero : true,
                lineStyle : {
                  color : '#4d4d4d'
                }
              },
              axisLabel : {
                  formatter:function(value) {
                      if(value == 0.3) {
                          return '';
                      } else if(value == 0.2) {
                          return '細心            ';
                      } else if(value == 0.4) {
                          return '            粗心';
                      } else {
                          return '';
                      }
                  },
                  textStyle : {
                      color : "#4d4d4d",
                      fontSize : 16
                  }
              }
          }
      ],
      yAxis : [
          {
              type : 'value',
              name : '正確率',
              splitNumber: 11,
              splitLine : false,
              splitArea : false,
              scale: true,
              min : 0,
              max : 1.1,
              axisLine : {
                lineStyle : {
                  color : '#4d4d4d'
                }
              },
              axisLabel : {
                  //show : false,
                  formatter : function(value) {
                      if(value == yLine) {
                          return '50%';//value;
                      }
                  }
              }
          }
      ],
      series : [
          {
              name:'學生',
              type:'scatter',
              symbolSize: 10,
              symbol:'image://../reporting/images/ic_personalability.png',
              data: mciData,
              itemStyle : {
                  normal : {
                      color : '#24537e'
                  }
              },
              markLine : {
                  tooltip : {
                      show : false
                  },
                  symbol : 'none',
                  itemStyle : {
                      normal : {
                          lineStyle : 'solid',
                          color : '#4d4d4d'
                      }
                  },
                  data : [
                      [{xAxis:0.3,yAxis:0},{xAxis:0.3,yAxis:100}],
                      [{xAxis:0,yAxis:yLine},{xAxis:1,yAxis:yLine}]
                  ]
              },
              markPoint : {
                  tooltip: {
                      //backgroundColor: 'rgba(255,100,200,0.5)',
                      show : false,
                      formatter : function(value) {
                          return tps[value.value[0]];
                      }
                  },
                  symbol : 'emptyRectangle',
                  symbolSize : 20,
                  /*effect : {
                      color : '#33aa88',
                      shadowColor : '#aa33ff'
                  },*/
                  itemStyle : {
                      normal : {
                          borderWidth : 0,
                          label : {
                              textStyle : {
                                  color : '#4d4d4d',
                                  fontSize : 16
                              },
                              formatter : function(value) {
                                  return value.value;
                              }
                          }
                      },
                      emphasis : {
                          borderWidth : 0,
                          label : {
                              textStyle : {
                                  color : '#4d4d4d',
                                  fontSize : 16
                              },
                              formatter : function(value) {
                                  return value.value;
                              }
                          }
                      }
                  },
                  data : [{xAxis:0.038,yAxis:1.05,value:'A. 表現良好'},
                          {xAxis:0.550,yAxis:1.05,value:'B. 偶爾較為粗心'},
                          {xAxis:0.052,yAxis:0.05,value:'C. 學習能力較差'},
                          {xAxis:0.562,yAxis:0.05,value:'D. 未能掌握'}]
              }
          }
      ]
  };
  me.getOption = function() {
      return me.optionmci;
  }
}
