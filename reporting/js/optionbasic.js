function OptionBasic(jd) {
  var me = this;
  var paper = jd.data.paper.overall;
  var items = jd.data.items;
  var students = jd.data.students;
  var basicHtml = [{header:"題目模型評估", content:""}];
  if(jd.success == false) {
      basicHtml[0].content += '<div style="color:red;font-weight:bold">本次數據不符合Rasch模型，Rasch分析結果不可信</div>';//，';
      /*if(jd.error.indexOf(' outfit ') > 0) {
          basicHtml[0].content += '擬合度Outfit在正常區間[0.5~1.5]的項目低於95%</div>';
      } else {
          basicHtml[0].content += '標準誤小於0.75的項目低於95%</div>';
      }*/
  } else {
      basicHtml[0].content += '<div style="color:#4d4d4d;font-weight:bold">本次數據符合Rasch模型，Rasch分析結果可信</div>';
  }
  //basicHtml[0].content += '<div style="font-weight:bold">学生能力题目难度对照图分析</div>';
  //basicHtml[0].content += '<div>学生平均能力 : '+paper.ability_mean+'(logit)</div>';
  //basicHtml[0].content += '<div>题目平均难度 : '+paper.difficulty_mean+'(logit)</div>';
  basicHtml[0].content += '<div style="color:#5aa9dd;font-weight:bold">註1. 此試卷試題總數是'+items.length+'題，答題人數是'+students.length+'人</div>';
  basicHtml[0].content += '<div style="color:#5aa9dd;font-weight:bold">註2. 此分析可以作為參考幫助老師快速發現試卷中的異常，老師可通過進一步查看題目和學生具體情況決定是否需要改善相關試題質量和確認學生情況</div>';
  basicHtml[0].content += '<div style="color:#5aa9dd;font-weight:bold">註3. 點擊柱狀圖可查看詳細信息</div>';
  
  var abilRange = paper.ability_range;
  var diffRange = paper.difficulty_range;
  var low = Math.min(abilRange[0], diffRange[0]);
  var lowStage = Math.max(low, -5.0);
  var high = Math.max(abilRange[1], diffRange[1]);
  var highStage = Math.min(high, 5.0);
  var basiccategory = [];
  me.ability = [];
  me.difficulty = [];
  me.abilStudents = [];
  me.diffItems = [];
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
      me.abilStudents.push([]);
      me.diffItems.push([]);
  }
  basiccategory.push(needle+"");
  me.ability.push(0);
  me.difficulty.push(0);
  me.abilStudents.push([]);
  me.diffItems.push([]);
  //diffItems = [], abilStudents = []
  var key;
  for(var t in items) {
      var dl = Math.max(Math.min(items[t].difficulty_logit,highStage),lowStage);
      key = parseInt((dl-lowStage)/step);
      me.difficulty[key]++;
      me.diffItems[key].push(items[t].item);
  }
  for(var p in students) {
      var sa = Math.max(Math.min(students[p].ability,highStage),lowStage);
      key = parseInt((sa-lowStage)/step);
      me.ability[key]--;
      me.abilStudents[key].push(students[p].student);
  }
  var iLines = [];
  var iPoints = [];
  var order1 = 1;
  var ki, klen, lowCover=false, highCover=false;
  klen = me.ability.length;
  //me.ability[0] and me.difficulty[0] won't be both 0
  for(ki = klen-1; ki > 0; ki --) {
      if(me.ability[ki] < 0 || me.difficulty[ki] > 0) {
          break;
      }
      me.ability.splice(ki,1);
      me.difficulty.splice(ki,1);
      basiccategory.splice(ki,1);
  }
  klen = me.ability.length;
  var minx = Math.min.apply(null, me.ability);
  var maxx = Math.max.apply(null, me.difficulty);
  var adj_factor = 1;
  var qe = -1*minx/maxx;
  if(qe > 10) {
      qe = parseInt(qe/10)*10;
      for(ki = 0; ki < klen; ki++) {
          me.difficulty[ki] = me.difficulty[ki]*qe;
      }
      adj_factor = qe;
  }
  maxx = Math.max.apply(null, me.difficulty);
  basicHtml.push({header:"題目分布分析", content:""});
  var upy, downy, leftx, rightx, ky;
  var itMsgs = [];
  var diMsgs = [];
  var descLabel;
  var dLabels = "AABCD";
  for(ki = 0; ki < klen; ki++) {
      descLabel = dLabels[order1];
      if (me.difficulty[ki] > 0) {
          lowCover = true;
          if(me.ability[ki] == 0) { // wasted items
              downy = ki-0.5;
              rightx = me.difficulty[ki];
              leftx = -rightx;
              upy = ki+0.5;
              for(ky = ki+1; ky < klen; ky++) {
                  if(me.ability[ky] == 0) {
                      if(rightx < me.difficulty[ky]) {
                          rightx = me.difficulty[ky];
                          leftx = -rightx;
                      }
                      upy = ky+0.5;
                  } else {
                      break;
                  }
              }
              leftx = Math.min(leftx, -2-adj_factor);
              leftx = Math.max(leftx, minx);
              iLines.push([{xAxis:leftx,yAxis:downy},{xAxis:rightx,yAxis:downy}]);
              iLines.push([{xAxis:rightx,yAxis:downy},{xAxis:rightx,yAxis:upy}]);
              iLines.push([{xAxis:rightx,yAxis:upy},{xAxis:leftx,yAxis:upy}]);
              iLines.push([{xAxis:leftx,yAxis:upy},{xAxis:leftx,yAxis:downy}]);
              iPoints.push({xAxis:Math.min(-0.5,leftx+1),yAxis:upy-0.5,value:order1});
              itMsgs[order1] = '<div style="color:#dd626e">'+descLabel+". 本次測試全體學生能力均高於此難度範圍的試題，不易區分學生能力，低難度的題目浪費</div>";
              order1++;
          }
          break;
      } else if(me.ability[ki] < 0) { // no cover items
          downy = ki-0.5;
          leftx = me.ability[ki];
          rightx = -leftx;
          upy = ki+0.5;
          for(ky = ki+1; ky < klen; ky++) {
              if(me.difficulty[ky] == 0) {
                  if(leftx > me.ability[ky]) {
                      leftx = me.ability[ky];
                      rightx = -leftx;
                  }
                  upy = ky+0.5;
              } else {
                  break;
              }
          }
          rightx = Math.max(rightx, 2+adj_factor);
          rightx = Math.min(rightx, maxx);
          iLines.push([{xAxis:leftx,yAxis:downy},{xAxis:rightx,yAxis:downy}]);
          iLines.push([{xAxis:rightx,yAxis:downy},{xAxis:rightx,yAxis:upy}]);
          iLines.push([{xAxis:rightx,yAxis:upy},{xAxis:leftx,yAxis:upy}]);
          iLines.push([{xAxis:leftx,yAxis:upy},{xAxis:leftx,yAxis:downy}]);
          iPoints.push({xAxis:Math.max(0.5,rightx-1),yAxis:upy-0.5,value:order1});
          itMsgs[order1] = '<div style="color:#dd626e">'+descLabel+". 本次測試題目難度分布寬度不能覆蓋低能力的學生，缺乏難度較低的題目</div>";
          order1++;
          break;
      }
  }
  for(ki = klen - 1; ki > 0; ki--) {
      descLabel = dLabels[order1];
      if (me.difficulty[ki] > 0) {
          highCover = true;
          if(me.ability[ki] == 0) { // wasted items
              downy = ki-0.5;
              rightx = me.difficulty[ki];
              leftx = -rightx;
              upy = ki+0.5;
              for(ky = ki-1; ky > 0; ky--) {
                  if(me.ability[ky] == 0) {
                      if(rightx < me.difficulty[ky]) {
                          rightx = me.difficulty[ky];
                          leftx = -rightx;
                      }
                      downy = ky-0.5;
                  } else {
                      break;
                  }
              }
              leftx = Math.min(leftx,-2-adj_factor);
              leftx = Math.max(leftx,minx);
              iLines.push([{xAxis:leftx,yAxis:downy},{xAxis:rightx,yAxis:downy}]);
              iLines.push([{xAxis:rightx,yAxis:downy},{xAxis:rightx,yAxis:upy}]);
              iLines.push([{xAxis:rightx,yAxis:upy},{xAxis:leftx,yAxis:upy}]);
              iLines.push([{xAxis:leftx,yAxis:upy},{xAxis:leftx,yAxis:downy}]);
              iPoints.push({xAxis:Math.min(leftx+1,-0.5),yAxis:upy-0.5,value:order1});
              itMsgs[order1] = '<div style="color:#dd626e">'+descLabel+". 本次測試全體學生能力均低於此難度範圍的試題，不易區分學生能力，高難度的題目浪費</div>";
              order1++;
          }
          break;
      } else if(me.ability[ki] < 0) {
          downy = ki-0.5;
          leftx = me.ability[ki];
          rightx = -leftx;
          upy = ki+0.5;
          for(ky = ki-1; ky > 0; ky--) {
              if(me.difficulty[ky] == 0) {
                  if(leftx > me.ability[ky]) {
                      leftx = me.ability[ky];
                      rightx = -leftx;
                  }
                  downy = ky-0.5;
              } else {
                  break;
              }
          }
          rightx = Math.max(rightx, 2+adj_factor);
          rightx = Math.min(rightx, maxx);
          iLines.push([{xAxis:leftx,yAxis:downy},{xAxis:rightx,yAxis:downy}]);
          iLines.push([{xAxis:rightx,yAxis:downy},{xAxis:rightx,yAxis:upy}]);
          iLines.push([{xAxis:rightx,yAxis:upy},{xAxis:leftx,yAxis:upy}]);
          iLines.push([{xAxis:leftx,yAxis:upy},{xAxis:leftx,yAxis:downy}]);
          iPoints.push({xAxis:Math.max(0.5,rightx-1),yAxis:upy-0.5,value:order1});
          itMsgs[order1] = '<div style="color:#dd626e">'+descLabel+". 本次測試題目難度分布寬度不能覆蓋高能力的學生，缺乏難度較高的題目</div>";
          order1++;
          break;
      }
  }
  if(lowCover && highCover) {
      basicHtml[1].content += '<div>本次測試題目難度分布寬度覆蓋了學生能力分布寬度，出題範圍適當</div>';
  }
  
  /*ki = klen>>1;
  var lowpart = 0, highpart = 0;
  var i;
  if(klen%2) {
      var qcnt = me.difficulty[ki-2]+me.difficulty[ki-1]+me.difficulty[ki]+me.difficulty[ki+1]+me.difficulty[ki+2];
      if(ki > 4) {
          qcnt += me.difficulty[ki-3] + me.difficulty[ki+3];
          for(i = 0; i < ki-3; i++) {
              lowpart += me.difficulty[i];
          }
          for(i = klen-1; i > ki+3; i--) {
              highpart += me.difficulty[i];
          }
      } else {
          for(i = 0; i < ki-2; i++) {
              lowpart += me.difficulty[i];
          }
          for(i = klen-1; i > ki+2; i--) {
              highpart += me.difficulty[i];
          }
      }
  } else {
      var qcnt = me.difficulty[ki-2]+me.difficulty[ki-1]+me.difficulty[ki]+me.difficulty[ki+1];
      if(ki > 4) {
          qcnt += me.difficulty[ki-3] + me.difficulty[ki+2];
          for(i = 0; i < ki-3; i++) {
              lowpart += me.difficulty[i];
          }
          for(i = klen-1; i > ki+2; i--) {
              highpart += me.difficulty[i];
          }
      } else {
          for(i = 0; i < ki-2; i++) {
              lowpart += me.difficulty[i];
          }
          for(i = klen-1; i > ki+1; i--) {
              highpart += me.difficulty[i];
          }
      }
  }
  //80% items distribute among +-2 logit
  if(qcnt*1.0/items.length >= 0.8) {
      basicHtml[1].content += '<div>題目難度分布均勻、合理</div>';
  } else {
      basicHtml[1].content += '<div>題目難度分布不均勻、不合理；';
      if(lowpart > highpart) {
          basicHtml[1].content += '低難度偏多</div>';
      } else {
          basicHtml[1].content += '高難度偏多</div>';
      }
  }*/
  if(itMsgs[1]) {
      basicHtml[1].content += itMsgs[1];
  }
  if(itMsgs[2]) {
      basicHtml[1].content += itMsgs[2];
  }

  basicHtml.push({header:"題目難度分析", content:""});
  if(diMsgs[1]) {
      basicHtml[2].content += diMsgs[1];
  }
  if(diMsgs[2]) {
      basicHtml[2].content += diMsgs[2];
  }
  if(Math.abs(paper.ability_mean - paper.difficulty_mean) < 0.5) {
      basicHtml[2].content += '<div>學生整體能力分布相對題目難度分布相符合，測驗對學生能力的區分度精確</div>';
  } else if(paper.ability_mean > paper.difficulty_mean) {
      basicHtml[2].content += '<div>學生整體能力分布相對高於題目難度分布，測驗對學生能力的區分度降低</div>';
  } else if(paper.ability_mean < paper.difficulty_mean) {
      basicHtml[2].content += '<div>學生整體能力分布相對低於題目難度分布，測驗對學生能力的區分度降低</div>';
  }
  
  //$("#chart_description").html(basicHtml);

  //refer to saveAsImage of echarts
  me.onclick = function(params) {
      //console.log('clicked');
      var html = '';
      var darr;
      if(!me.itemsDims) {
          var difflangs = ['','低','中','高'];
          me.itemsDims = {};
          me.itemsDiffs = {};
          for(var i = 0; i < items.length; i++) {
                me.itemsDims[items[i].item] = items[i].dimension;
                me.itemsDiffs[items[i].item] = difflangs[items[i].raw_difficulty];
          }
      }

      html += '<div style="z-index:1000;margin:200px 0px 0px 300px;position:relative;overflow-y:auto;max-height:500px;background:#fff;height:100%;width:400px;">';
      html += '<div style="margin:5px 0px 10px 5px;font-size:16px;text-align:left;background:#f1f1f1">';
      if(params.value === '') {
          return;
      } else if(params.seriesName == '學生能力') {
          html += '學生詳細信息</div>';
          html += '<table style="width:100%;border:0">';
          html += '<tr><th style="width:33%">學生</th><th style="width:33%">學生</th><th>學生</th></tr>';
          darr = me.abilStudents[params.dataIndex];
          var j = 0;
          var stl;
          for(var i = 0; i < darr.length; i+=3) {
              if(j%2) {
                  stl = 'background-color:#f1f1f1';
              } else {
                  stl = '';
              }
              html += '<tr><td style="width:33%;'+stl+'">'+darr[i]+'</td>';
              if(darr[i+1]) {
                  html += '<td style="width:33%;'+stl+'">'+darr[i+1]+'</td>';
              } else {
                  html += '<td style="'+stl+'"></td>';
              }
              if(darr[i+2]) {
                  html += '<td style="'+stl+'">'+darr[i+2]+'</td></tr>';
              } else {
                  html += '<td style="'+stl+'"></td></tr>';
              }
              j++;
          }
      } else if(params.seriesName == '題目難度') {
          html += '題目詳細信息</div>';
          html += '<table style="width:100%;border:0">';
          html += '<tr><th style="width:33%">題目</th><th style="width:33%">範疇</th><th>OQB難度</th></tr>';
          darr = me.diffItems[params.dataIndex];
          for(var i = 0; i < darr.length; i++) {
              if(i%2) {
                  html += '<tr><td style="width:33%;background-color:#f1f1f1">'+darr[i]+'</td><td style="background-color:#f1f1f1">'+me.itemsDims[darr[i]]+'</td><td style="background-color:#f1f1f1">'+me.itemsDiffs[darr[i]]+'</td></tr>';
              } else {
                  html += '<tr><td style="width:33%">'+darr[i]+'</td><td>'+me.itemsDims[darr[i]]+'</td><td>'+me.itemsDiffs[darr[i]]+'</td></tr>';
              }
          }
      } else {
          return;
      }
      html += '</table>';
      html += '</div>';
      var popLink = document.createElement('div');
      popLink.innerHTML = html;
      var downloadDiv = document.createElement('div');
      downloadDiv.id = echartsClickId;
      downloadDiv.style.cssText = 'position:fixed;'
                + 'z-index:999;'
                + 'display:block;'
                + 'top:0;left:0;'
                + 'background-color:rgba(33,33,33,0.5);'
                + 'text-align:center;'
                + 'width:100%;'
                + 'height:100%;';
                //+ 'line-height:'
                //+ document.documentElement.clientHeight + 'px;';

      downloadDiv.appendChild(popLink);
      document.body.appendChild(downloadDiv);
      popLink = null;
      downloadDiv = null;
      setTimeout(function (){
          var _d = document.getElementById(echartsClickId);
          if (_d) {
              _d.onclick = function () {
                  var d = document.getElementById(
                      echartsClickId
                  );
                  d.onclick = null;
                  d.innerHTML = '';
                  document.body.removeChild(d);
                  d = null;
              };
              _d = null;
          }
      }, 500);
      return;
  }

  me.render = function() {
    var bLen = basicHtml.length;
    if(bLen > 0) {
        var tabobj = new ZTabs("#chart_description", basicHtml.length);
        var hds = [];
        var conts = []
        for(var i = 0; i < bLen; i++) {
            hds.push(basicHtml[i].header);
            conts.push(basicHtml[i].content);
        }
        tabobj.setTabs(hds, conts);
    } else {
        var tabobj = new ZTabs("#chart_description");
    }
    tabobj.init();
    tabobj.render(function(index) {
      if(myChart) {
           myChart.setOption(me.options[index], true);
      }
    });
  };

  //option
  var dname = ['學生能力', '題目難度']
  me.optionbasic = {
      title : {
          //text: paper.title + ' Person Item Map',
          text: '學生能力/題目難度對比圖',
          textStyle : {
              color : '#4d4d4d'
          },
          x:'left'
      },
      legend : {
          selectedMode : false,
          x : 'right',
          padding : [5, 25, 0, 0],
          data : ['學生平均能力','題目平均難度']
      },
      tooltip : {
          show : false,
          trigger: 'item',
          backgroundColor: 'rgba(255,0,255,0.5)',
          textStyle : {
              color: 'yellow',
              decoration: 'none',
              fontFamily: 'Verdana, sans-serif',
              fontSize: 15,
              fontStyle: 'italic',
              fontWeight: 'bold'
          },
          formatter: function (params,ticket,callback) {
              //console.log(params)
              var res = params[0]+'接近'+params[1]+'(logit)的數目: <br/>';
              if(params[2] < 0) {
                res += -params[2];
              }else {
                res += params[2];
              }
              if(params[0] == dname[1]) {
                res += '('+me.diffItems[params[7]].join()+')';
              }
              return res;
          }
          //formatter: "Template formatter: <br/>{b}<br/>{a}:{c}<br/>{a1}:{c1}"
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
              type : 'value',
              min: minx-1-adj_factor,
              //max: maxx+2,
              splitArea : false,
              splitLine : false,
              axisLabel : {
                  //show : false,
                  textStyle : {
                      color : '#388ca9'// (3dc5c5+24537d)/2'
                  },
                  formatter : function(v) {
                     if(v == 0) {
                       return '（低 ｜易） ';
                     }
                  }
              }
          }
      ],
      yAxis : [
          {
              type : 'category',
              name : '學生能力（高）                       ',
              nameTextStyle: {
                  color : '#3dc5c5'
              },
              axisTick : false,
              position : 'left',
              splitArea : false,
              splitLine : false,
              //data : ['-3','-2','-1','0','1','2','3']
              data : basiccategory,
              axisLabel : {
                  show : false,
                  formatter : function(value) {
                      if(value == basiccategory[0]) {
                          return "低";
                      } else if(value == basiccategory[basiccategory.length-1]) {
                          return "高";
                      }
                  }
              }
          },
          {
              type : 'category',
              nameTextStyle: {
                  color : '#24537d'
              },
              name : '                        （難）題目難度',
              splitArea : false,
              splitLine : false,
              axisTick : false,
              //data : ['-3','-2','-1','0','1','2','3']
              data : basiccategory,
              position : 'right',
              axisLabel : {
                  show : false,
                  formatter : function(value) {
                      if(value == basiccategory[0]) {
                          return "易";
                      } else if(value == basiccategory[basiccategory.length-1]) {
                          return "難";
                      }
                  }
              }
          }
      ],
      series : [
          {
              name:dname[0],
              type:'bar',
              stack: 'sum',
              markLine : {
                  tooltip: {
                      show : false,
                      trigger : 'item',
                      formatter : function(value) {
                          return '學生平均能力';
                      }
                  },
                  symbolSize : 3,
                  symbol : ['circle','none'],
                  itemStyle:{
                      normal : {
                          borderWidth : 1.0,
                          color:'#b6dc71',
                          label : {
                              show : false,
                              formatter:function(value) {
                                  return '學生平均能力';
                              },
                              textStyle : {
                                  fontSize : 10
                              }
                          }
                      }
                  },
                  data : [[{xAxis:0,yAxis:paper.ability_mean-lowStage},{xAxis:minx-adj_factor,yAxis:paper.ability_mean-lowStage}]]
              },
              barWidth : basiccategory.length > 8 ? 10:20,
              itemStyle: {normal: {
                  color : '#3dc5c5',
                  label : {show: true, position:'left',
                          formatter:function(v) {
                            if(v.value <= -1) {
                                return -v.value;
                            } else {
                                return 0;
                            }
                          }
                  }
              }},
              data:me.ability
          },
          {
              name:dname[1],
              type:'bar',
              stack: 'sum',
              markLine : {
                  tooltip: {
                      show : false,
                      trigger : 'item',
                      formatter : function(value) {
                          return '題目平均難度';
                      }
                  },
                  symbolSize : 3,
                  symbol : ['circle','none'],
                  itemStyle:{
                      normal : {
                          color:'orange',
                          borderWidth : 1.0,
                          label : {
                              show : false,
                              formatter:function(value) {
                                  return '題目平均難度';
                              },
                              textStyle : {
                                  fontSize : 10
                              }
                          }
                      }
                  },
                  data : [[{xAxis:0,yAxis:paper.difficulty_mean-lowStage},{xAxis:maxx+1+adj_factor,yAxis:paper.difficulty_mean-lowStage}]]
              },
              itemStyle: {normal: {
                  color : '#24537d',
                  label : {show: true, position: 'right',
                               formatter : function(v) {
                                   if(v.value >= 1) {
                                       return parseInt(v.value/adj_factor);
                                   } else {
                                       return 0;
                                   }
                               }
                          }
              }},
              data:me.difficulty
          },
          {
              name : '題目平均難度',
              type : 'scatter',
              //data : [{xAxis:maxx+2,yAxis:paper.difficulty_mean}],
              data : [{xAxis:0.1,yAxis:0.1}],
              symbol : 'image://../reporting/images/ic_quizdifficulty.png',
              symbolSize : 1,
              itemStyle : {
                  normal : {
                      color : 'orange'
                  }
              },
              markPoint : {
                  tooltip: {
                      show : false,
                      formatter : function(value) {
                          return '題目平均難度';
                      }
                  },
                  symbol : 'image://../reporting/images/ic_quizdifficulty.png',
                  symbolSize : 0,
                  itemStyle : {
                      normal : {
                          label : {
                              show : false
                          }
                      }
                  },
                  data : [{xAxis:maxx+1+adj_factor,yAxis:paper.difficulty_mean-lowStage+0.3}]
              }
          },
          {
              name : '學生平均能力',
              data : [{xAxis:0.1,yAxis:0.1}],
              type : 'scatter',
              symbol : 'image://../reporting/images/ic_studentability.png',
              symbolSize : 1,
              itemStyle : {
                  normal : {
                      color : '#b6dc71'
                  }
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
                  data : [{xAxis:minx-1+(minx>>3),yAxis:paper.ability_mean-lowStage+0.5}]
              }
          }
      ]
  };
  me.optionitems = {
      title : {
          //text: paper.title + ' Person Item Map',
          text: '學生能力/題目難度對比圖',
          x:'left'
      },
      tooltip : {
          show : false,
          trigger: 'item',
          backgroundColor: 'rgba(255,0,255,0.5)',
          textStyle : {
              color: 'yellow',
              decoration: 'none',
              fontFamily: 'Verdana, sans-serif',
              fontSize: 15,
              fontStyle: 'italic',
              fontWeight: 'bold'
          },
          formatter: function (params,ticket,callback) {
              //console.log(params)
              var res = params[0]+'接近'+params[1]+'(logit)的數目: <br/>';
              if(params[2] < 0) {
                res += -params[2];
              }else {
                res += params[2];
              }
              if(params[0] == dname[1]) {
                res += '('+me.diffItems[params[7]].join()+')';
              }
              return res;
              setTimeout(function (){
                  // 仅为了模拟异步回调
                  callback(ticket, res);
              }, 1000)
              return 'loading';
          }
          //formatter: "Template formatter: <br/>{b}<br/>{a}:{c}<br/>{a1}:{c1}"
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
              type : 'value',
              min: minx-1-adj_factor,
              //max: maxx+2,
              splitArea : false,
              splitLine : false,
              axisLabel : {
                  //show : false,
                  textStyle : {
                      color : '#388ca9'// (3dc5c5+24537d)/2'
                  },
                  formatter : function(v) {
                     if(v == 0) {
                       return '（低 ｜易） ';
                     }
                  }
              }
          }
      ],
      yAxis : [
          {
              type : 'category',
              name : '學生能力（高）                       ',
              nameTextStyle: {
                  color : '#3dc5c5'
              },
              axisTick : false,
              splitArea : false,
              splitLine : false,
              position : 'left',
              //data : ['-3','-2','-1','0','1','2','3']
              data : basiccategory,
              axisLabel : {
                  show : false,
                  formatter : function(value) {
                      if(value == basiccategory[0]) {
                          return "低";
                      } else if(value == basiccategory[basiccategory.length-1]) {
                          return "高";
                      }
                  }
              }
          },
          {
              type : 'category',
              nameTextStyle: {
                  color : '#24537d'
              },
              name : '                        （難）題目難度',
              axisTick : false,
              splitArea : false,
              splitLine : false,
              //data : ['-3','-2','-1','0','1','2','3']
              data : basiccategory,
              position : 'right',
              axisLabel : {
                  show : false,
                  formatter : function(value) {
                      if(value == basiccategory[0]) {
                          return "易";
                      } else if(value == basiccategory[basiccategory.length-1]) {
                          return "難";
                      }
                  }
              }
          }
      ],
      series : [
          {
              name:dname[0],
              type:'bar',
              stack: 'sum',
              barWidth : basiccategory.length > 8 ? 10:20,
              itemStyle: {normal: {
                  color : '#3dc5c5',
                  label : {show: true, position:'left',
                           formatter : function(v) {
                               if(v.value < 0) {
                                   return -v.value;
                               } else {
                                   return 0;
                               }
                          }
                  }
              }},
              data:me.ability
          },
          {
              name:dname[1],
              type:'bar',
              stack: 'sum',
              itemStyle: {normal: {
                  color : '#24537d',
                  label : {show: true, position: 'right',
                           formatter:function(v) {
                               if(v.value > 0) {
                                   return parseInt(v.value/adj_factor);
                               } else {
                                   return 0;
                               }
                           }
                  }
              }},
              data:me.difficulty
          }
      ]
  };
  me.optionitems.series.push({
              type:'scatter',
              markLine : {
                  tooltip : {
                      show : false
                  },
                  symbol : 'empty',
                  symbolSize:0,
                  itemStyle:{
                      normal : {
                          borderWidth : 1,
                          lineStyle : {
                              //color : '#24537d',
                              color : '#dd626e',
                              type : 'dashed'
                          }
                      }
                  },
                  data : iLines
              },
              markPoint : {
                  tooltip: {
                      backgroundColor: 'rgba(255,100,200,0.5)',
                      show : false,
                      formatter : function(value) {
                          return itMsgs[value.value];
                      }
                  },
                  symbol : 'empty',
                  symbolSize : 0,
                  itemStyle : {
                      normal : {
                          borderWidth : 0,
                          label : {
                              textStyle : {
                                  //color : '#24537d',
                                  color : '#dd626e',
                                  fontSize : 20
                              },
                              formatter : function(value) {
                                  return dLabels[value.value];
                              }
                          }
                      },
                      emphasis : {
                          borderWidth : 0,
                          label : {
                              textStyle : {
                                  color : '#dd626e',
                                  fontSize : 20
                              }
                          }
                      }
                  },
                  data : iPoints
              },
              data:[{xAxis:0,yAxis:0,value:0}]
          });
  me.optiondiffs = {
      title : {
          //text: paper.title + ' Person Item Map',
          text: '學生能力/題目難度對比圖',
          x:'left'
      },
      legend : {
          selectedMode : false,
          x : 'right',
          padding : [5, 25, 0, 0],
          data : ['學生平均能力','題目平均難度']
      },
      tooltip : {
          show : false,
          trigger: 'item',
          backgroundColor: 'rgba(255,0,255,0.5)',
          textStyle : {
              color: 'yellow',
              decoration: 'none',
              fontFamily: 'Verdana, sans-serif',
              fontSize: 15,
              fontStyle: 'italic',
              fontWeight: 'bold'
          },
          formatter: function (params,ticket,callback) {
              //console.log(params)
              var res = params[0]+'接近'+params[1]+'(logit)的數目: <br/>';
              if(params[2] < 0) {
                res += -params[2];
              }else {
                res += params[2];
              }
              if(params[0] == dname[1]) {
                res += '('+me.diffItems[params[7]].join()+')';
              }
              return res;
              setTimeout(function (){
                  // 仅为了模拟异步回调
                  callback(ticket, res);
              }, 1000)
              return 'loading';
          }
          //formatter: "Template formatter: <br/>{b}<br/>{a}:{c}<br/>{a1}:{c1}"
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
              type : 'value',
              min: minx-1-adj_factor,
              //max: maxx+2,
              splitArea : false,
              splitLine : false,
              axisLabel : {
                  //show : false,
                  textStyle : {
                      color : '#388ca9'// (3dc5c5+24537d)/2'
                  },
                  formatter : function(v) {
                     if(v == 0) {
                       return '（低 ｜易） ';
                     }
                  }
              }
          }
      ],
      yAxis : [
          {
              type : 'category',
              name : '學生能力（高）                       ',
              nameTextStyle: {
                  color : '#3dc5c5'
              },
              axisTick : false,
              position : 'left',
              splitArea : false,
              splitLine : false,
              //data : ['-3','-2','-1','0','1','2','3']
              data : basiccategory,
              axisLabel : {
                  show : false,
                  formatter : function(value) {
                      if(value == basiccategory[0]) {
                          return "低";
                      } else if(value == basiccategory[basiccategory.length-1]) {
                          return "高";
                      }
                  }
              }
          },
          {
              type : 'category',
              nameTextStyle: {
                  color : '#24537d'
              },
              name : '                        （難）題目難度',
              axisTick : false,
              splitArea : false,
              splitLine : false,
              //data : ['-3','-2','-1','0','1','2','3']
              data : basiccategory,
              position : 'right',
              axisLabel : {
                  show : false,
                  formatter : function(value) {
                      if(value == basiccategory[0]) {
                          return "易";
                      } else if(value == basiccategory[basiccategory.length-1]) {
                          return "难";
                      }
                  }
              }
          }
      ],
      series : [
          {
              name:dname[0],
              type:'bar',
              stack: 'sum',
              markLine : {
                  tooltip: {
                      show : false,
                      trigger : 'item',
                      formatter : function(value) {
                          return '學生平均能力';
                      }
                  },
                  symbolSize : 3,
                  symbol : ['circle','none'],
                  itemStyle:{
                      normal : {
                          borderWidth : 1.0,
                          color:'#b6dc71',
                          label : {
                              show : false,
                              formatter:function(value) {
                                  return '學生平均能力';
                              },
                              textStyle : {
                                  fontSize : 10
                              }
                          }
                      }
                  },
                  data : [[{xAxis:0,yAxis:paper.ability_mean-lowStage},{xAxis:minx-adj_factor,yAxis:paper.ability_mean-lowStage}]]
              },
              barWidth : basiccategory.length > 8 ? 10:20,
              itemStyle: {normal: {
                  color : '#3dc5c5',
                  label : {show: true, position:'left',
                           formatter : function(v) {
                               if(v.value < 0) {
                                   return -v.value;
                               } else {
                                   return 0;
                               }
                          }
                  }
              }},
              data:me.ability
          },
          {
              name:dname[1],
              type:'bar',
              stack: 'sum',
              markLine : {
                  tooltip: {
                      show : false,
                      trigger : 'item',
                      formatter : function(value) {
                          return '題目平均難度';
                      }
                  },
                  symbolSize : 3,
                  symbol : ['circle','none'],
                  itemStyle:{
                      normal : {
                          color:'orange',
                          borderWidth : 1.0,
                          label : {
                              show : false,
                              formatter:function(value) {
                                  return '題目平均難度';
                              },
                              textStyle : {
                                  fontSize : 10
                              }
                          }
                      }
                  },
                  data : [[{xAxis:0,yAxis:paper.difficulty_mean-lowStage},{xAxis:maxx+1+adj_factor,yAxis:paper.difficulty_mean-lowStage}]]
              },
              itemStyle: {normal: {
                  color : '#24537d',
                  label : {show: true, position: 'right',
                           formatter : function(v) {
                               if(v.value > 0) {
                                   return parseInt(v.value/adj_factor);
                               } else {
                                   return 0;
                               }
                           }
                  }
              }},
              data:me.difficulty
          },
          {
              name : '題目平均難度',
              type : 'scatter',
              tooltip: {
                  show : false,
                  trigger : 'item',
                  formatter : function(value) {
                      return '題目平均難度';
                  }
              },
              data : [{xAxis:0.1,yAxis:0.1}],
              symbol : 'image://../reporting/images/ic_quizdifficulty.png',
              symbolSize : 0,
              itemStyle : {
                  normal : {
                      color : 'orange'
                  }
              },
              markPoint : {
                  tooltip: {
                      show : false,
                      formatter : function(value) {
                          return '題目平均難度';
                      }
                  },
                  symbol : 'image://../reporting/images/ic_quizdifficulty.png',
                  symbolSize : 0,
                  itemStyle : {
                      normal : {
                          label : {
                              show : false
                          }
                      }
                  },
                  data : [{xAxis:maxx+1+adj_factor,yAxis:paper.difficulty_mean-lowStage+0.3}]
              }
              //data : [[maxx+2,Math.round(paper.difficulty_mean)]]
          },
          {
              name : '學生平均能力',
              tooltip: {
                  show : false,
                  trigger : 'item',
                  formatter : function(value) {
                      return '學生平均能力';
                  }
              },
              type : 'scatter',
              data : [{xAxis:0.1,yAxis:0.1}],
              symbol : 'image://../reporting/images/ic_studentability.png',
              symbolSize : 0,
              itemStyle : {
                  normal : {
                      color : '#b6dc71'
                  }
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
                  data : [{xAxis:minx-1+(minx>>3),yAxis:paper.ability_mean-lowStage+0.5}]
              }
              //data : [[minx-2,Math.round(paper.ability_mean)]]
          }
      ]
  };
  /*me.optiondiffs.series.push({
              type:'scatter',
              markLine : {
                  tooltip : {
                      show : false
                  },
                  symbol : 'empty',
                  symbolSize:0,
                  itemStyle:{
                      normal : {
                          borderWidth : 1,
                          lineStyle : {
                              color:'#24537d',
                              type : 'dashed'
                          }
                      }
                  },
                  data : []
              },
              markPoint : {
                  tooltip: {
                      show : false,
                      formatter : function(value) {
                          return diMsgs[value.value];
                      }
                  },
                  symbol : 'empty',//'image://scale_a.png',
                  symbolSize : 0,
                  itemStyle : {
                      normal : {
                          borderWidth : 0,
                          color : '#4d4d4d',
                          label : {
                              textStyle : {
                                  color : '#4d4d4d',
                                  fontSize : 16
                              },
                              formatter:function(v) {
                                  return v.value;
                              }
                          }
                      }
                  },
                  data : [{xAxis:maxx+1.5, yAxis:(paper.ability_mean+paper.difficulty_mean)/2-lowStage, value:'A'}]
              },
              data:[{xAxis:0,yAxis:0,value:0}]
          });*/
  me.options = [me.optionbasic, me.optionitems, me.optiondiffs];
  me.getOption = function() {
      return me.optionbasic;
  }
  me.getOptionItems = function() {
      return me.optionitems;
  }
  me.getOptionDiffs = function() {
      return me.optiondiffs;
  }
}
