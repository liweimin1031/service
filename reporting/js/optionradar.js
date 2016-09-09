function OptionRadar(jd) {
  var me = this;
  var paper = jd.data.paper.overall;
  
  var radarcategory = [];
  var dimension = [];
  //var dimnum = [];
  var indics = [];
  var maxa = 0;
  var mina = 1000;
  var maxd,mind,ave;
  for(var d in paper.dimension.average) {
      radarcategory.push(d);
      ave = paper.dimension.average[d]/paper.dimension.distribution[d];
      dimension.push(ave);
      if(mina > ave) {
          mind = d;
          mina = ave;
      }
      if(maxa < ave) {
          maxd = d;
          maxa = ave;
      }
      //dimnum.push(paper.dimension.distribution[d]);
      indics.push({ text : d, max : 1.0});
  }
  var radarHtml = '';
  radarHtml += '<div style="font-weight:bold">範疇統計平均分</div>';
  if(maxd != mind) {
      radarHtml += '<div>表現最好的範疇: "'+maxd+'",平均分: '+(100*maxa).toFixed(2)+'%</div>';
      radarHtml += '<div>表現最差的範疇: "'+mind+'",平均分: '+(100*mina).toFixed(2)+'%</div>';
  }
  if(dimension.length < 2) {
      radarHtml += '<div>本試卷只有一個範疇('+maxd+'),平均分是'+(maxa*100).toFixed(2)+'%</div>';
  }
  radarHtml += '<div style="margin-top:10px;font-weight:bold">試卷範疇分布</div>';
  radarHtml += '<div>每一範疇所占比重</div>';
  //$("#chart_description").html(radarHtml);
  me.render = function() {
    var hds = ['範疇統計'];
    var conts = [radarHtml]
    var tabobj = new ZTabs("#chart_description", 1);
    tabobj.setTabs(hds, conts);
    tabobj.init();
    tabobj.render();
  }
  if(dimension.length>=3) {
      me.optionradar = {
          title : {
              textStyle : {
                  color : '#4d4d4d'
              },
              text: '範疇統計平均分'
          },
          tooltip : {
              trigger: 'axis',
              formatter : function(params) {
                  return '平均分('+params[0][3]+'): '+params[0][2].toFixed(2);
              }
          },
          toolbox: {
              show : false,
              feature : {
                  /*mark : {show: true},
                  dataView : {show: true, readOnly: false},
                  restore : {show: true},*/
                  saveAsImage : {show: true}
              }
          },
          polar : [
             {
                 indicator : indics
              }
          ],
          calculable : false,
          series : [
              {
                  name: 'Dimension',
                  type: 'radar',
                  data : [
                      {
                          value : dimension,
                          name : 'Dimension',
                          itemStyle : {
                              normal : {
                                  label : {
                                      show : true,
                                      formatter : function(v) {
                                          if(v.name == maxd) {
                                              return 'Max:'+v.value.toFixed(2);
                                          } else if(v.name == mind) {
                                              return 'Min:'+v.value.toFixed(2);
                                          }
                                      },
                                      textStyle : {
                                          align : 'right',
                                          baseline: 'top'
                                      }
                                  }
                              }
                          }
                      }
                  ]
              }
          ]
      };
  } else if(dimension.length > 1) {
      me.optionradar = {
          title : {
              textStyle : {
                  color : '#4d4d4d'
              },
              text: '範疇統計平均分'
          },
          tooltip : {
              trigger: 'axis'
          },
          toolbox: {
              show : false,
              feature : {
                  /*mark : {show: true},
                  dataView : {show: true, readOnly: false},
                  restore : {show: true},*/
                  saveAsImage : {show: true}
              }
          },
          calculable : false,
          xAxis : [
              {
                  type : 'category',
                  name : '範疇',
                  nameTextStyle : {
                      color : '#4d4d4d'
                  },
                  data : radarcategory
              }
          ],
          yAxis : [
              {
                  type : 'value',
                  nameTextStyle : {
                      color : '#4d4d4d'
                  },
                  name : '範疇平均分',
                  axisLabel : {
                      formatter:function(v) {
                          return v*100+'%';
                      }
                  }
              }
          ],
          series : [
              {
                  name: 'Dimension',
                  tooltip : {
                      trigger: 'item',
                      formatter:function(params,ticket) {
                          return '平均分('+params[1]+'): '+params[2].toFixed(2);
                      }
                  },
                  type: 'bar',
                  barWidth : 40,
                  data : dimension
              }
          ]
      };
  } else {
      me.optionradar = {
          title : {
              textStyle : {
                  color : '#4d4d4d'
              },
              text: '範疇統計平均分'
          },
          xAxis : [
              {
                  type : 'value',
                  min : 0,
                  max : 10,
                  axisLine : false,
                  splitLine : false,
                  splitArea : false,
                  axisLabel : false
              }
          ],
          yAxis : [
              {
                  type : 'value',
                  min : 0,
                  max : 10,
                  splitLine : false,
                  axisLine : false,
                  splitArea : false,
                  axisLabel : false
              }
          ],
          series : [
              {
                  type: 'scatter',
                  symbolSize : 0,
                  symbol : 'none',
                  data : [[1,1]],
                  markPoint : {
                      symbol : 'emptyRect',
                      symbolSize : 20,
                      itemStyle : {
                        normal : {
                          borderWidth : 0,
                          color : '#4d4d4d',
                          label : {
                              textStyle : {
                                  fontSize : 16
                              },
                              formatter : function(v) {
                                   return v.value;
                              }
                          }
                        },
                        emphasis : {
                          borderWidth : 0,
                          color : '#4d4d4d',
                          label : {
                              textStyle : {
                                  fontSize : 16
                              },
                              formatter : function(v) {
                                   return v.value;
                              }
                          }
                        }
                      },
                      data : [{xAxis:5,yAxis:5.5,value:'本試卷只有一個範疇('+maxd+')'},
                      {xAxis:5,yAxis:4.5,value:'平均分是'+(maxa*100).toFixed(2)+'%'}]
                  }
              }
          ]
      };
  };
  me.getOption = function() {
      return me.optionradar;
  }
}
