function OptionRank(jd) {
    var paper = jsonData.data.paper.overall;
    var students = jsonData.data.students;
    var rank = [];
    var len = 0;
    var sscores = [];
    for(var tt in students) {
        sscores[students[tt].student] = students[tt].score+'/'+paper.question_num;
    }
    for(var kk in paper.ranking) {
        var pos = paper.ranking[kk];
        if(!rank[pos]) {
            rank[pos] = [];
        }
        rank[pos].push(kk);
        len += 1;
    }
    var rankHtml = '';
    this.render = function() {
        var tabobj = new ZTabs("#chart_description", 1);
        tabobj.setTabs(['年級排名'], ['']);
        tabobj.init();
        tabobj.render();
    };
    //$("#chart_description").html(rankHtml);
    this.optionrank = '';
    if(jd.data.rasch !== false) {
        this.optionrank += '<div style="width:100%;height:220px;">';
        this.optionrank += '<div style="margin-top:10px;margin-bottom:10px;width:49%;float:left"><span style="color:#4d4d4d;font-weight:bold;font-size:18px;">最高正確率</span>';
        this.optionrank += '<table style="width:100%">';
        this.optionrank += '<tr><th style="width:40%">題目</th><th>答對人數/總數</th><th>所占比例</th></tr>';
        len = students.length;
        var scale = [];
        var rowstr = [];
        var cr, ratio;
        for(var i in paper.correct_rate) {
            cr = paper.correct_rate[i];
            ratio = (cr*100.0/len).toFixed(2);
            if(scale.length == 0) {
                scale.push(cr);
                rowstr.push('<tr><td>'+i+'</td><td>'+cr+'/'+len+'</td><td>'+ratio+'%</td></tr>');
            } else {
                if(scale[0] < cr) {
                    scale.splice(0, 0, cr);
                    rowstr.splice(0, 0, '<tr><td>'+i+'</td><td>'+cr+'/'+len+'</td><td>'+ratio+'%</td></tr>');
                } else if(scale.length>1 && scale[1] < cr) {
                    scale.splice(1, 0, cr);
                    rowstr.splice(1, 0, '<tr><td>'+i+'</td><td>'+cr+'/'+len+'</td><td>'+ratio+'%</td></tr>');
                } else {
                    scale.push(cr);
                    rowstr.push('<tr><td>'+i+'</td><td>'+cr+'/'+len+'</td><td>'+ratio+'%</td></tr>');
                }
            }
        }
        this.optionrank += rowstr[0]+rowstr[1].replace('tr', 'tr style="background-color:#f1f1f1"')+rowstr[2];
        this.optionrank += '</table>';
        this.optionrank += '</div>';
        this.optionrank += '<div style="margin-top:10px;margin-bottom:10px;margin-left:15px;width:49%;float:left"><span style="margin-top:10px;color:#4d4d4d;font-weight:bold;font-size:18px;">最低正確率</span>';
        this.optionrank += '<table style="width:100%">';
        this.optionrank += '<tr><th style="width:40%">題目</th><th>答對人數/總數</th><th>所占比例</th></tr>';
        scale = [];
        rowstr = [];
        for(i in paper.incorrect_rate) {
            cr = paper.incorrect_rate[i];
            ratio = (cr*100.0/len).toFixed(2);
            if(scale.length == 0) {
                scale.push(cr);
                rowstr.push('<tr><td>'+i+'</td><td>'+cr+'/'+len+'</td><td>'+ratio+'%</td></tr>');
            } else {
                if(scale[0] < cr) {
                    scale.splice(0, 0, cr);
                    rowstr.splice(0, 0, '<tr><td>'+i+'</td><td>'+cr+'/'+len+'</td><td>'+ratio+'%</td></tr>');
                } else if(scale.length>1 && scale[1] < cr) {
                    scale.splice(1, 0, cr);
                    rowstr.splice(1, 0, '<tr><td>'+i+'</td><td>'+cr+'/'+len+'</td><td>'+ratio+'%</td></tr>');
                } else {
                    scale.push(cr);
                    rowstr.push('<tr><td>'+i+'</td><td>'+cr+'/'+len+'</td><td>'+ratio+'%</td></tr>');
                }
            }
        }
        this.optionrank += rowstr[2]+rowstr[1].replace('tr', 'tr style="background-color:#f1f1f1"')+rowstr[0];
        this.optionrank += '</table>';
        this.optionrank += '</div>';
        this.optionrank += '</div>';
    }

    this.optionrank += '<div style="font-size:18px;color:#4d4d4d;font-weight:bold">年級排名</div>';
    this.optionrank += '<table style="width:100%">';
    this.optionrank += '<tr><th style="width:50%">學生</th><th style="width:25%">分數/總分</th><th>排名</th></tr>';
    var k = 0;
    for(i = 1; i <= len; i++) {
        if(!rank[i]) {
            continue;
        }
        for(var j = 0; j < rank[i].length; j++) {
            this.optionrank += '<tr';
            if(k%2 == 1) {
                this.optionrank += ' style="background-color:#f1f1f1"';
            }
            this.optionrank += '><td>'+rank[i][j]+'</td><td>'+sscores[rank[i][j]]+'</td><td>'+i+'</td></tr>';
            k++;
        }
    }
    this.optionrank += '</table>';
    this.getOption = function() {
        return this.optionrank;
    }
}
