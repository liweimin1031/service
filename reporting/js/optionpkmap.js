/*
  adjustment : 
  restrict all logits to [-7,7]
  data in [-5,5] are kept real
  data out of [-5,5] are amplified to [-7,-5] and [5,7]
*/
function OptionPkmap(jd) {
  var me = this;
  var paper = jd.data.paper.overall;
  var items = jd.data.items;
  me.students = jd.data.students;
  me.range_0 = Math.min(paper.difficulty_range[0], paper.ability_range[0]);
  me.range_1 = Math.max(paper.difficulty_range[1], paper.ability_range[1]);
  //use the range of difficulty to draw PKMAP
  me.MAX = 6.0;
  me.MIN = -6.0;
  me.MAX_CAP = 5.0;
  me.MIN_CAP = -5.0;
  me.optionpkmap = {};
  me.subpkmap = {};
  var i;
  me.getPkKeys = function() {
      var pkKeys = [];
      for(i = 0; i < me.students.length; i++) {
          pkKeys.push(me.students[i].student);
      }
      return pkKeys;
  }
  me.pkDims = [];
  me.dims = [];
  me.dimsums = [];
  me.dimcats = [];
  if(!me.pkDims.length) {
    me.pkDims.push('所有範疇');
    for(i = 0; i < items.length; i++) {
      var tmpD = items[i].dimension;
      if(me.pkDims.indexOf(tmpD) < 0) {
          me.pkDims.push(tmpD);
          me.dimcats.push(tmpD);
          me.dims.push(0);
          me.dimsums.push(0);
      }
    }
  }

  var rMsgs = [];
  rMsgs.push('<p>Region A：未能掌握的知識，未來努力的目標</p>');
  rMsgs.push('<p>Region B：未能很好掌握的知識，通過幫助可以掌握</p>');
  rMsgs.push('<p>Region C：特別關註部分，檢查是否學生粗心，或特別原因而做錯</p>');
  rMsgs.push('<p>Region D：熟練掌握</p>');
  rMsgs.push('<p>Region E：做對但需鞏固的知識</p>');
  rMsgs.push('<p>Region F：意外做對的知識，需檢查是否真正掌握或是猜對</p>');

  me.render = function() {
    var pkmapHtml = '';
    if(jd.success == false) {
        pkmapHtml += '<div style="color:red;font-weight:bold">本次數據不符合Rasch模型，Rasch分析結果不可信</div>';
        /*if(jd.error.indexOf(' outfit ') > 0) {
            pkmapHtml += '擬合度Outfit在正常區間[0.5~1.5]的項目低於95%</div>';
        } else {
            pkmapHtml += '標準誤小於0.75的項目低於95%</div>';
        }*/
    } else {
        pkmapHtml += '<div style="color:#4d4d4d;font-weight:bold">此試卷數據與分析模型擬合，分析有效性高</div>';
    }
    pkmapHtml += '<div style="font-weight:bold">本圖圖示化了每個學生對所有試題的表現，將學生作答的試題分為六個區域，可以直觀看出不同學生的強項、弱項、疏忽的、待提高的試題分布和範疇，實現促進學習的評估(Assessment for Learning)</div>';
    //pkmapHtml += rMsgs[0]+rMsgs[1]+rMsgs[2]+rMsgs[3]+rMsgs[4]+rMsgs[5];

    var hds = ['試卷模型評估'];
    var conts = [pkmapHtml]
    var tabobj = new ZTabs("#chart_description", 1);
    tabobj.setTabs(hds, conts);
    tabobj.init();
    tabobj.render();
  }
  
  // v - value, ol - old logit, nl - new logit, se - standard error, step - 2.0
  // up area - me.MAX ~ nl+step
  // center area - (nl+step) ~ (nl - step)
  // down area - (nl - step) ~ me.MIN
  me.adjust_logit = function(v, ol, nl, se, step) {
      var res;
      if(v > ol+se) {
          res = nl + step + (v - ol - se)/(me.range_1-ol)*(me.MAX-step-nl);
      } else if( v < ol - se) {
          res = nl - step - (ol - se - v)/(ol-me.range_0)*(nl-step - me.MIN);
      } else {
          res = nl + step*((v-ol)/se)
      }
      return res;
  }
  
    var itemsLogits = {};
    var itemsDims = {};
    var rChars = 'AABCDEFG';
    for(var i = 0; i < items.length; i++) {
          itemsLogits[items[i].item] = items[i].difficulty_logit;
          itemsDims[items[i].item] = items[i].dimension;
    }

  me.getOption = function(index, dim) {
      var stu = me.students[index];
      if(!dim && me.optionpkmap[stu.student]) {
           return me.optionpkmap[stu.student];
      }
      var logit = stu.ability;
      var se = stu.standarderror;
      if(logit+2.0 >= me.MAX_CAP) {
          var new_logit = me.MAX_CAP - 2.0;
      } else if(logit - 2.0 <= me.MIN_CAP) {
          var new_logit = me.MIN_CAP + 2.0;
      } else {
          var new_logit = logit;
      }
      var logit_step = 1.5;
      var upval = new_logit+logit_step;
      var lowval = new_logit-logit_step;
      var zpd = [];
      var tmp;
      var dimZones = [];
      for(var ki = 0; ki < me.dimcats.length; ki++) {
          me.dimsums[ki] = 0;
          me.dims[ki] = 0;
      }
      var new_abil_mean = me.adjust_logit(paper.ability_mean, logit, new_logit, se, logit_step);
      for(var ii in stu.zpd) {
          if(ii < 3) {
             var step = 1.5;
          } else {
             var step = -1.5;
          }
          var rgi = parseInt(ii)+1;
          for(var jj in stu.zpd[ii]) {
              var tmpDim = itemsDims[stu.zpd[ii][jj]];
              var pos = me.dimcats.indexOf(tmpDim);
              me.dimsums[pos]++;
              if(ii > 2) {
                  me.dims[pos]++;
              }
              if(dim && me.pkDims[dim] != tmpDim) {
                  continue;
              }
              var ypos = me.adjust_logit(itemsLogits[stu.zpd[ii][jj]], logit, new_logit, se, logit_step);
              tmp = {value:[step*((jj%8)+(jj/8)+0.4), ypos, stu.zpd[ii][jj], rgi, itemsDims[stu.zpd[ii][jj]]], 
                     itemStyle : {
                         normal : {
                             label:{
                                 show:true,
                                 position:'inside',
                                 formatter:function(value) {
                                    return value.value[2];
                                 }
                             }
                         }
                     }
              };
              //zpd.push(tmp);
              dimZones.push(tmp);
          }
      }
      var srData = [
          {
              name:'個人能力',
              type:'scatter',
              data : [[0.1,new_logit,'AVE']],
              symbolSize: 1,
              symbol: 'image://../reporting/images/ic_personalability.png',
              markLine : {
                  show : true,
                  symbol: ['circle','none'],
                  symbolSize : 3,
                  itemStyle:{
                      normal : {
                          color : '#24537e',
                          //borderWidth : 0.5,
                          label : false
                      }
                  },
                  data : [[{xAxis:0,yAxis:new_logit,value:logit},{xAxis:-22,yAxis:new_logit}]]
              },
              markPoint : {
                  tooltip: {
                      show : false,
                      formatter : function(value) {
                          return '個人能力';
                      }
                  },
                  symbol : 'image://../reporting/images/ic_personalability.png',
                  symbolSize : 0,
                  itemStyle : {
                      normal : {
                          label : {
                              show : false
                          }
                      }
                  },
                  data : [{xAxis:-24,yAxis:new_logit+0.35}]
              }
          },
          {
              name:'學生平均能力',
              type:'scatter',
              data : [[0,new_abil_mean,'AVE']],
              symbolSize: 1,
              symbol: 'image://../reporting/images/ic_studentability.png',
              markLine : {
                  show : true,
                  //symbolSize : 1,
                  symbol: ['circle','none'],
                  symbolSize : 3,
                  itemStyle:{
                      normal : {
                          color : 'green',
                          //borderWidth : 0.5,
                          label : false
                      }
                  },
                  data : [[{xAxis:0,yAxis:new_abil_mean,value:new_abil_mean},{xAxis:22,yAxis:new_abil_mean}]]
              },
              markPoint : {
                  tooltip: {
                      show : false,
                      formatter : function(value) {
                          return '學生平均能力';
                      }
                  },
                  symbol : 'image://../reporting/images/ic_studentability.png',
                  symbolSize : 0,
                  itemStyle : {
                      normal : {
                          label : {
                              show : false
                          }
                      }
                  },
                  data : [{xAxis:22,yAxis:new_abil_mean+0.3}]
              }
          },
          {
              name:'潛力發展區劃',
              type:'scatter',
              data : [[17,upval+0.2,'A'],
                          [17,lowval+0.2,'B'],
                          [17,lowval-0.2,'C'],
                          [-17,lowval-0.2,'D'],
                          [-17,lowval+0.2,'E'],
                          [-17,upval+0.2,'F']],
              symbolSize: 0,
              markPoint : {
                  show : true,
                  tooltip: {
                      show : false,
                      formatter : function(value) {
                          return rMsgs[value['dataIndex']];
                      }
                  },
                  symbolSize : 13,
                  symbol: 'emptyCircle',
                  itemStyle:{
                      normal : {
                          color : '#4d4d4d',
                          borderWidth : 0,
                          label : {
                              formatter:function(value) {
                                  return value.value;
                              },
                              textStyle : {
                                  fontSize : 16
                              }
                          }
                      },
                      emphasis : {
                          color : '#4d4d4d',
                          borderWidth : 0,
                          label : {
                              formatter:function(value) {
                                  return value.value;
                              },
                              textStyle : {
                                  fontSize : 16
                              }
                          }
                      }
                  },
                  data : [{xAxis:20,yAxis:me.MAX-0.32,value:'未能掌握'},
                          {xAxis:19,yAxis:upval-0.32,value:'努力即可提升'},
                          {xAxis:20,yAxis:lowval-0.32,value:'意外做錯'},
                          {xAxis:-20,yAxis:lowval-0.32,value:'熟練掌握'},
                          {xAxis:-20,yAxis:upval-0.32,value:'需鞏固    '},
                          {xAxis:-20,yAxis:me.MAX-0.32,value:'意外做對'}
                         ]
              },
              markLine : {
                  symbol : 'none',
                  itemStyle : {
                      normal : {
                          color : '#4d4d4d',
                          //color : 'rgba(77,77,77,0.6)',
                          lineStyle : {
                              type : 'solid',
                              width : 3
                          }
                      }
                  },
                  data : [
                      [{xAxis:-22,yAxis:lowval},{xAxis:22,yAxis:lowval}],
                      [{xAxis:-22,yAxis:upval},{xAxis:22,yAxis:upval}]
                  ]
              }
          }
      ];
      srData.push({
          name:'題目',
          type:'scatter',
          symbolSize: 12,
          data: dimZones,
          itemStyle : {
              normal : {
                  //color : '#3dc4c3'
                  color : 'rgba(61, 196, 195, 0.6)'
              }
          },
          markLine : {
              symbol : 'none',
              symbolSize : 1,
              itemStyle : {
                color : 'white',
                normal : {
                  lineStyle : {
                      color : '#4d4d4d',
                      width : 3,
                      type : 'solid'
                  }
                }
              },
              data : [
                  [{xAxis:-22,yAxis:me.MIN},{xAxis:24,yAxis:me.MIN}]
              ]
          }
      });
    var tmpOption = {
      title : { 
          text : '學生個人能力表現分析',
          textStyle : {
              color : '#4d4d4d'
          }
      },
      legend : {
          x : 'right',
          padding : [5, 25, 0, 0],
          selectedMode : false,
          data : ['題目','個人能力','學生平均能力']
      },
      //color : ['green', 'blue'],
      tooltip : {
          show : false,
          backgroundColor: 'rgba(255,0,255,0.5)',
          trigger: 'axis',
          axisPointer:{
              show : false,
              type : 'shadow'
          },
          formatter: function (params,ticket,callback) {
              //console.log(params)
              //var res = params[0]+' : '+'<br/>';
              var res = params[2][2]+'(Region '+rChars[params[2][3]]+') <br>'
              res += "範疇："+params[2][4];
              return res;
          }
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
              position : 'bottom',
              splitNumber: 23,
              min : -24,
              max : 22,
              axisLine:false,
              splitArea : {
                  areaStyle : {
                      color : ['#fbd8e0','transparent','#fbf8e7','#fbf8e7','#fbf8e7','#fbf8e7','#fbf8e7','#fbf8e7','#fbf8e7','#fbf8e7','#fbf8e7','#fbf8e7','#fbf8e7','#fbd8e0','#fbd8e0','#fbd8e0','#fbd8e0','#fbd8e0','#fbd8e0','#fbd8e0','#fbd8e0','#fbd8e0','#fbd8e0']
                  }
              },
              splitLine: {
                  show : false
              },
              axisLabel : {
                  textStyle : {
                      color : "#4d4d4d",
                      fontSize : 14
                  },
                  formatter : function(value) {
                      if(value == -10) {
                          return "答對    ";
                      } else if(value == 10) {
                          return "答錯";
                      } else if(value == 0) {
                          return "易";
                      }
                  }
              }
          }
      ],
      yAxis : [
          {
              //splitNumber: 6,
              name : '題目難度(難)',
              nameTextStyle : {
                  color : '#4d4d4d',
                  fontSize : 14
              },
              scale: true,
              min : me.MIN,
              max : me.MAX,
              splitLine : false,
              splitArea : false,
              axisLine : {
                  lineStyle : {
                      color : '#4d4d4d',
                      width : 3
                  }
              },
              axisLabel : false,
              type : 'value'
          }
      ],
      series : srData
    };
    if(!dim) {
      me.optionpkmap[stu.student] = tmpOption;
    }
    return tmpOption;
  }
  me.getSubOption = function(index) {
      var stu = me.students[index];
      if(me.subpkmap[stu.student]) {
           return me.subpkmap[stu.student];
      }
      var indics = [];
      for(var ki = 0; ki < me.dimcats.length; ki++) {
          indics.push({text:me.dimcats[ki], max : me.dimsums[ki]});
      }
      if(me.dimcats.length >= 3) {
          var suboption = {
              title : {
                  textStyle : {
                      color : '#4d4d4d'
                  },
                  text: '個人範疇統計 - '+stu.student
              },
              tooltip : {
                  trigger: 'axis',
                  formatter : function(params) {
                      return '得分比('+params[0][3]+'): '+params[0][2]+
                          '/'+me.dimsums[me.dimcats.indexOf(params[0][3])];
                  }
              },
              polar : [{indicator : indics}],
              calculable : false,
              series : [{
                  name: 'Dimension',
                  type: 'radar',
                  symbolSize : 0,
                  data : [{
                      value : me.dims,
                      name : 'Dimension',
                      itemStyle : {
                          normal : {
                              label : {
                                  show : true,
                                  formatter : function(v) {
                                      /*if(v.name == maxd) {
                                          return 'Max:'+v.value.toFixed(2);
                                      } else if(v.name == mind) {
                                          return 'Min:'+v.value.toFixed(2);
                                      }*/
                                  },
                                  textStyle : {
                                      align : 'right',
                                      baseline: 'top'
                                  }
                              }
                          }
                      }
                  }]
              }]
          };
      } else if(me.dimcats.length==2) {
          var suboption = {
              title : {
                  textStyle : {
                      color : '#4d4d4d'
                  },
                  text: '個人範疇統計 - '+stu.student
              },
              tooltip : {
                  trigger: 'axis'
              },
              calculable : false,
              xAxis : [
                  {
                      type : 'category',
                      name : '範疇',
                      nameTextStyle : {
                          color : '#4d4d4d'
                      },
                      data : me.dimcats
                  }
              ],
              yAxis : [
                  {
                      type : 'value',
                      nameTextStyle : {
                          color : '#4d4d4d'
                      },
                      name : '範疇得分比',
                      axisLabel : false
                  }
              ],
              series : [
                  {
                      name: 'Dimension',
                      tooltip : {
                          show : false,
                          trigger: 'item',
                          formatter:function(params,ticket) {
                              return '平均分('+params[1]+'): '+params[2].toFixed(2);
                          }
                      },
                      type: 'bar',
                      barWidth : 40,
                      itemStyle : {
                          normal : {
                              label : {
                                  show : true,
                                  formatter : function(v) {
                                      return v.value+'/'+me.dimsums[me.dimcats.indexOf(v.name)];
                                  }
                              }
                          }
                      },
                      data : me.dims
                  }
              ]
          };
      } else { // one dimension
          var suboption = {
              title : {
                  textStyle : {
                      color : '#4d4d4d'
                  },
                  text: '個人範疇統計 - '+stu.student
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
                          data : [{xAxis:5,yAxis:5,value:'範疇('+me.dimcats[0]+'),得分比是'+me.dims[0]+'/'+me.dimsums[0]}]
                      }
                  }
              ]
          };
      }
      me.subpkmap[stu.student] = suboption;
      return suboption;
  }
}
