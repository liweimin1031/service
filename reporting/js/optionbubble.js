function compFunc(a,b) {
    if(a.difficulty_logit > b.difficulty_logit) {
        return 1;
    } else {
        return -1;
    }
}
function compPerson(a,b) {
    if(a.ability > b.ability) {
        return 1;
    } else {
        return -1;
    }
}
function OptionBubble(jd) {
  var me = this;
  var paper = jd.data.paper.overall;
  var items = jd.data.items;
  //var students = jd.data.students;
  var person = [];
  var okItems = [];
  var unexpBubbles = [];
  var bigBubbles = [];
  var dupBubbles = [];
  var i = 0;
  var j;
  items.sort(compFunc);
  var tmp1;
  var tmp2;
  var dupItems = [];
  var dupStudents = [];
  var unexp_items = [];
  var unexp_students = [];
  var sItems = []
  for(i = 0; i < items.length; i++) {
      tmp1 = items[i];
      sItems.push({
          diff : tmp1.difficulty_logit,
          dim : tmp1.dimension,
          outfit : tmp1.outfit,
          se : tmp1.standarderror,
          item : tmp1.item
      });
      if(tmp1.standarderror > 0.75 || /*tmp1.outfit < 0.5 || */tmp1.outfit > 1.5) {
          unexpBubbles.push({value : [tmp1.outfit, tmp1.difficulty_logit, tmp1.standarderror, tmp1.item],
                   itemStyle : {normal : {label:{show:true,position:'inside',
                                formatter:function(value) {
                                    return value.value[3];
                                }
                   }} }
                  });
          if(tmp1.outfit > 1.5) {
              unexp_items.push(tmp1.item);
          } else {
              bigBubbles.push(tmp1.item);
          }
      } else {
          /*okItems.push({value : [tmp1.outfit, tmp1.difficulty_logit, tmp1.standarderror, tmp1.item],
                   itemStyle : {normal : {label:{show:true,position:'inside',
                                formatter:function(value) {
                                    return value.value[3];
                                }
                   }} }
                  });*/
      }
  }
  sItems.sort(function(a,b) { if(a.diff < b.diff) return -1; else return 1;});
  var tmpDup, tmpDupStr, initDupStr, tmpBubbles;
  var foundks = []
  for(i = 0; i < sItems.length; i++) {
      tmp1 = sItems[i];
      if(tmp1.se > 0.75 || /*tmp1.outfit < 0.5 || */tmp1.outfit > 1.5) {
          continue;
      }
      tmpDup = [tmp1.item];
      tmpBubbles = [{value : [tmp1.outfit, tmp1.diff, tmp1.se, tmp1.item],
                   itemStyle : {normal : {label:{show:true,position:'inside',
                                formatter:function(value) {
                                    return value.value[3];
                                }
          }} }
      }];
      for(j=i+1; j < sItems.length; j++) {
          tmp2 = sItems[j];
          if(tmp1.dim == tmp2.dim && Math.abs(tmp2.diff -tmp1.diff)<0.05) {
              if(Math.abs(tmp2.outfit-tmp1.outfit) < 0.03) {
                  //dupItems.push(tmp1.item +' & '+ tmp2.item);
                  tmpDup.push(tmp2.item);
                  foundks.push(j);
                  tmpBubbles.push({value : [tmp2.outfit, tmp2.diff, tmp2.se, tmp2.item],
                   itemStyle : {normal : {label:{show:true,position:'inside',
                                formatter:function(value) {
                                    return value.value[3];
                                }
                   }} }
                  });
              }
          } else {
              break;
          }
      }
      if(tmpDup.length>1) {
          initDupStr = dupItems.join(", ");
          tmpDupStr = tmpDup.join(" & ");
          if(initDupStr.indexOf(tmpDupStr) < 0) {
              dupItems.push('題目（'+tmpDupStr+'）');
              dupBubbles = dupBubbles.concat(tmpBubbles);
          }
      } else if(foundks.indexOf(i)<0) {
          okItems = okItems.concat(tmpBubbles);
      }
  }
  i = 0;
  //students.sort(compPerson);
  var bubbleHtml = [{header:"試卷模型評估", content:""}];
  if(jd.success == false) {
      bubbleHtml[0].content += '<div style="color:red;font-weight:bold">本次數據不符合Rasch模型，Rasch分析結果不可信</div>';
      /*if(jd.error.indexOf(' outfit ') > 0) {
          bubbleHtml[0].content += '擬合度Outfit在正常區間[0.5~1.5]的項目低於95%</div>';
      } else {
          bubbleHtml[0].content += '標準誤小於0.75的項目低於95%</div>';
      }*/
      bubbleHtml[0].content += '<div style="font-weight:bold;color:#5aa9dd">註1. 由Rasch模型分析出來的異常項目，是否真的需要修正或者刪除，則需要測評分析者進一步分析項目的內容與答題狀況。若項目本身符合出題的要求，並在學生的最近發展區之間，不存在獨立於背景材料的經驗性項目等，這說明即使Rasch模型檢驗出異常項目，也不需要對其修改或刪除，研究者可以將項目暫時保留下來，看看在下一次測試中，是否還會出現類似情況。</div>';
  } else {
      bubbleHtml[0].content += '<div>此試卷數據與Rasch模型符合，分析有效性高。</div>';
      bubbleHtml[0].content += '<div style="color:#5aa9dd;font-weight:bold">註1. 此分析可以作為參考幫助老師快速發現試卷中的異常，老師可通過進一步查看題目和學生具體情況決定是否需要改善相關試題質量和確認學生情況</div>';

      bubbleHtml[0].content += '<div style="font-weight:bold;color:#5aa9dd">註2. 由Rasch模型分析出來的異常項目，是否真的需要修正或者刪除，則需要測評分析者進一步分析項目的內容與答題狀況。若項目本身符合出題的要求，並在學生的最近發展區之間，不存在獨立於背景材料的經驗性項目等，這說明即使Rasch模型檢驗出異常項目，也不需要對其修改或刪除，研究者可以將項目暫時保留下來，看看在下一次測試中，是否還會出現類似情況。</div>';
  }
  if(dupItems.length) {
      bubbleHtml.push({header:"重復考查", content:""});
      bubbleHtml[1].content += '<div>題目重復考查學生同一種能力: <span style="color:#dd626e">'+dupItems.join(', ')+'</span></div>';
  }
  /*if(dupStudents.length) {
      bubbleHtml += '<p style="color:green">There are duplicated students : '+dupStudents.join(', ')+'</p>';
  }*/
  if(unexp_items.length || bigBubbles.length) {
       bubbleHtml.push({header:"題目考查誤差", content:""});
       var pos = bubbleHtml.length-1;
       if(bigBubbles.length) {
           bubbleHtml[pos].content += '<div>不能對學生能力做出很好的估計和區分: <span style="color:#dd626e">'+bigBubbles.join(', ')+'</span></div>';
           bubbleHtml[pos].content += '<div style="margin-bottom:20px">可能原因: 題目過於簡單，學生的通過率過高，對學生能力的估計就不精確；或者題目題意不明導致答題結果與其實際能力有偏差</div>';
       }
       if(unexp_items.length) {
           bubbleHtml[pos].content += '<div>題目結果不符合預期: <span style="color:#dd626e">'+unexp_items.join(', ')+'</span></div>';
           bubbleHtml[pos].content += '<div>可能原因: 能力高的學生做錯或者能力低的學生做對</div>';
       }
  }
  /*if(unexp_students.length) {
      bubbleHtml += '<p style="color:green">There are unexpected students : '+unexp_students.join(', ')+'</p>';
  }*/
  //$("#chart_description").html(bubbleHtml);
  
  me.render = function() {
    var bLen = bubbleHtml.length;
    if(bLen > 0) {
        var tabobj = new ZTabs("#chart_description", bLen);
        var hds = [];
        var conts = []
        for(var i = 0; i < bLen; i++) {
            hds.push(bubbleHtml[i].header);
            conts.push(bubbleHtml[i].content);
        }
        tabobj.setTabs(hds, conts);
    } else {
        var tabobj = new ZTabs("#chart_description");
    }

    tabobj.init();
    tabobj.render(function(index) {
        if(myChart) {
            myChart.setOption(me.options[index],true);
        }
    });
  }

  me.optionbubble = {
      title : { 
          text : '題目質量分析',
          textStyle : {
              color:'#4d4d4d'
          }
      },
      tooltip : {
          backgroundColor: 'rgba(255,0,255,0.5)',
          trigger: 'axis',
          show : false,
          showDelay : 0,
          axisPointer:{
              show: false,
              type : 'cross',
              lineStyle: {
                  type : 'dashed',
                  width : 0
              }
          },
          formatter: function (params,ticket,callback) {
              //console.log(params)
              var res = params[0]+' - '+params[2][3]+'<br/>';
              if(params[2][2] > 0.75) {
                  res += "標準誤("+params[2][2]+")過大";
              } else if(params[2][0] < 0.5 || params[2][0] > 1.5) {
                  res += "擬合度("+params[2][0].toFixed(2)+")異常";
              } else {
                  res += "("+params[2][0]+","+params[2][1]+") : "+params[2][2];
              }
              return res;
          }
      },
      legend: {
          selectedMode : false,
          x : 'right',
          padding : [5,30,0,0],
          data:['正常題目','異常題目']
      },
      xAxis : [
          {
              type : 'value',
              position : 'bottom',
              name : '擬合度',
              nameTextStyle : {
                  color : '#4d4d4d'
              },
              splitNumber: 10,
              min : 0,
              max : 2.5,
              scale: true,
              splitLine : false,
              splitArea : {
                  show : true,
                  areaStyle : {
                      color : ['#fbd8e0','#eeeff6','#eeeff6','#eeeff6','#eeeff6','#eeeff6','#eeeff6','#fbd8e0','#fbd8e0','#fbd8e0']
                  }
              },
              axisLabel : {
                  formatter : function(v) {
                      if(v == 0.75) {
                          return '符合預期';
                      } else if(v == 2) {
                          return '不符合預期';
                      }
                  }
              },
              axisLine : {
                  onZero : false
              }
          }
      ],
      yAxis : [
          {
              type : 'value',
              name : '題目難度',
              nameTextStyle : {
                  color : '#4d4d4d'
              },
              splitLine : false,
              splitArea : false,
              scale: true,
              max : Math.floor(sItems[sItems.length-1].diff+1),
              min : Math.floor(sItems[0].diff-1),
              axisLabel : {
                  formatter : function(value) {
                      if(value == Math.floor(sItems[0].diff-1)) {
                          return "易";
                      } else if(value == Math.floor(sItems[sItems.length-1].diff+1)) {
                          return "難";
                      }
                  }
              }

          }
      ],
      series : [
          {
              name:'正常題目',
              type:'scatter',
              //symbol : 'emptyCircle',
              itemStyle : {
                  normal : {
                      //color : '#3dc4c3'
                      color : 'rgba(61,196,195,0.6)'
                  }
              },
              tooltip : {
                  show : false
              },
              symbolSize: function (value){
                  return Math.max(10, Math.min(50, value[2] * 25));
              },
              data: okItems
          },
          {
              name:'異常題目',
              type:'scatter',
              //symbol : 'emptyCircle',
              itemStyle : {
                  normal : {
                      //color : '#dd626e'
                      color : 'rgba(221,98,110,0.6)'
                  }
              },
              symbolSize: function (value){
                  return Math.max(10,Math.min(50, value[2] * 25));
              },
              data: unexpBubbles.concat(dupBubbles)
          }
      ]
  };
  me.optionDupBubble = {
      title : { 
          text : '題目質量分析',
          textStyle : {
              color:'#4d4d4d'
          }
      },
      tooltip : {
          backgroundColor: 'rgba(255,0,255,0.5)',
          trigger: 'axis',
          showDelay : 0,
          show : false,
          axisPointer:{
              show: false,
              type : 'cross',
              lineStyle: {
                  type : 'dashed',
                  width : 1
              }
          },
          formatter: function (params,ticket,callback) {
              //console.log(params)
              var res = params[0]+' - '+params[2][3]+'<br/>';
              res += "("+params[2][0]+","+params[2][1]+") : "+params[2][2];
              return res;
          }
      },
      legend: {
          selectedMode : false,
          x : 'right',
          padding : [5,30,0,0],
          data:['重復題目']
      },
      xAxis : [
          {
              type : 'value',
              position : 'bottom',
              name : '擬合度',
              splitNumber: 10,
              splitLine : false,
              min : 0.0,
              max : 2.5,
              scale: true,
              splitArea : {
                  show : true,
                  areaStyle : {
                      color : ['#fbd8e0','#eeeff6','#eeeff6','#eeeff6','#eeeff6','#eeeff6','#eeeff6','#fbd8e0','#fbd8e0','#fbd8e0']
                  }
              },
              axisLabel : {
                  formatter : function(v) {
                      if(v == 0.75) {
                          return '符合預期';
                      } else if(v == 2) {
                          return '不符合預期';
                      }
                  }
              },
              axisLine : {
                  onZero : false
              }
          }
      ],
      yAxis : [
          {
              type : 'value',
              name : '題目難度',
              nameTextStyle : {
                  color : '#4d4d4d'
              },
              splitLine : false,
              splitArea : false,
              scale: true,
              max : Math.floor(sItems[sItems.length-1].diff+1),
              min : Math.floor(sItems[0].diff-1),
              axisLabel : {
                  formatter : function(value) {
                      if(value == Math.floor(sItems[0].diff-1)) {
                          return "易";
                      } else if(value == Math.floor(sItems[sItems.length-1].diff+1)) {
                          return "難";
                      }
                  }
              }
          }
      ],
      series : [
          {
              name:'重復題目',
              type:'scatter',
              itemStyle : {
                  normal : {
                      //color : '#dd626e'
                      color : 'rgba(221,98,110,0.6)'
                  }
              },
              symbolSize: function (value){
                  return Math.max(10, Math.min(50, value[2] * 25));
              },
              data: dupBubbles
          }
      ]
  };
  me.optionBig = {
      title : { 
          text : '題目質量分析',
          textStyle : {
              color:'#4d4d4d'
          }
      },
      color : ['green', 'blue'],
      tooltip : {
          backgroundColor: 'rgba(255,0,255,0.5)',
          trigger: 'axis',
          showDelay : 0,
          show : false,
          axisPointer:{
              show: false,
              type : 'cross',
              lineStyle: {
                  type : 'dashed',
                  width : 0
              }
          },
          formatter: function (params,ticket,callback) {
              //console.log(params)
              var res = params[0]+' - '+params[2][3]+'<br/>';
              res += "("+params[2][0]+","+params[2][1]+") : "+params[2][2];
              return res;
          }
      },
      legend: {
          selectedMode : false,
          x : 'right',
          padding : [5,30,0,0],
          data:['異常誤差']
      },
      xAxis : [
          {
              name : '擬合度',
              type : 'value',
              position : 'bottom',
              //name : '拟合度',
              splitNumber: 10,
              splitLine : false,
              min : 0.0,
              max : 2.5,
              scale: true,
              splitArea : {
                  show : true,
                  areaStyle : {
                      color : ['#fbd8e0','#eeeff6','#eeeff6','#eeeff6','#eeeff6','#eeeff6','#eeeff6','#fbd8e0','#fbd8e0','#fbd8e0']
                  }
              },
              axisLabel : {
                  formatter : function(v) {
                      if(v == 0.75) {
                          return '符合預期';
                      } else if(v == 2) {
                          return '不符合預期';
                      }
                  }
              },
              axisLine : {
                  onZero : false
              }
          }
      ],
      yAxis : [
          {
              type : 'value',
              name : '題目難度',
              nameTextStyle : {
                  color : '#4d4d4d'
              },
              splitLine : false,
              splitArea : false,
              scale: true,
              max : Math.floor(sItems[sItems.length-1].diff+1),
              min : Math.floor(sItems[0].diff-1),
              axisLabel : {
                  formatter : function(value) {
                      if(value == Math.floor(sItems[0].diff-1)) {
                          return "易";
                      } else if(value == Math.floor(sItems[sItems.length-1].diff+1)) {
                          return "難";
                      }
                  }
              }
          }
      ],
      series : [
          {
              name:'異常誤差',
              type:'scatter',
              itemStyle : {
                  normal : {
                      //color : '#dd626e'
                      color : 'rgba(221,98,110,0.6)'
                  }
              },
              symbolSize: function (value){
                  return Math.max(10, Math.min(50, value[2] * 25));
              },
              data: unexpBubbles
          }
      ]
  };
  me.options = [me.optionbubble];
  if(dupBubbles.length) {
      me.options.push(me.optionDupBubble);
  }
  if(unexpBubbles.length) {
      me.options.push(me.optionBig);
  }
  me.getOption = function() {
      return me.optionbubble;
  };
  me.getDupOption = function() {
      return me.optionDupBubble;
  };
  me.getOptionBig = function() {
      return me.optionBig;
  }
}
