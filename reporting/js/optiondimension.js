function OptionDimension(jd) {
  var me = this;
  var paper = jd.data.paper.overall;
  var dimensionHtml = '';
  
  var dimcategory = [];
  var dimension = [];
  for(var i in paper.dimension.distribution) {
      var perc = (100*paper.dimension.distribution[i]/paper.question_num).toFixed(2)+'%';
      dimcategory.push(i+'('+perc+')');
      dimension.push({value : paper.dimension.distribution[i],name : i+'('+perc+')'});
  }
  //$("#chart_description").html(dimensionHtml);

  me.render = function() {
    return;
    var hds = ['試卷範疇分布'];
    var conts = [dimensionHtml]
    var tabobj = new ZTabs("#chart_description", 1);
    tabobj.setTabs(hds, conts);
    tabobj.init();
    tabobj.render();
  }

  me.optiondimension = {
      title : {
          text: '試卷範疇分布',
          textStyle : {
              color : '#4d4d4d'
          }
      },
      tooltip : {
          trigger: 'item',
          formatter: "{a} <br/>{b} : {c} ({d}%)"
      },
      legend: {
          show : false,
          orient : 'vertical',
          x : 'right',
          padding : [5,25,0,0],
          data:dimcategory
      },
      toolbox: {
          show : false,
          feature : {
              /*mark : {show: true},
              dataView : {show: true, readOnly: false},
              magicType : {
                  show: true, 
                  type: ['pie', 'funnel'],
                  option: {
                      funnel: {
                          x: '25%',
                          width: '50%',
                          funnelAlign: 'left',
                          max: 1548
                      }
                  }
              },
              restore : {show: true},*/
              saveAsImage : {show: true}
          }
      },
      calculable : false,
      series : [
          {
              name:'Distribution',
              type:'pie',
              radius : '55%',
              center: ['50%', '60%'],
              data:dimension
          }
      ]
  };
  me.getOption = function() {
      return me.optiondimension;
  }
}
