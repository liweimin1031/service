function ZTabs(target, num) {
    var me = this;

    if(!num) {
        me.num = 2; //initial tabs = 2
    } else {
        me.num = num;
    }

    me.renderTo = target;
    me.headers = ["Tab 0", "Tab 1"];
    me.contents = ["Content 0", "Content 1"];
    me.initiated = false;
    me.id_tag = 'ztabs_';
    me.idc_tag = 'zcontent_';
    me.tabsHtml = '';
    me.activetab = 0;

    me.setTabNum = function(num) {
        me.num = num;
    }
    //init
    me.init = function() {
        //var me = this;
        me.tabsHtml += '<div class="ztabs">';
        var hdHtml = '<div class="tabs_region">';
        var contentHtml = '<div class="content_region">';
        for(var i = 0; i < me.num; i++) {
            hdHtml += '<div class="tabs_header';
            contentHtml += '<div class="tabs_content"';
            if(i == me.activetab) {
                hdHtml += '_active';
            } else {
                contentHtml += ' style="display:none" ';
            }
            hdHtml += '" id="'+me.id_tag+i+'" value='+i+'>'+me.headers[i]+'</div>';
            contentHtml += 'id="'+me.idc_tag+i+'">'+me.contents[i]+'</div>';
        }
        hdHtml += '</div>';
        contentHtml += '</div>';
        me.tabsHtml += hdHtml + contentHtml;
        me.tabsHtml += '</div>';
        me.initiated = true;
    }
    //set headers and contents to TABS
    me.setTabs = function(headers, contents) {
        if(headers && headers.length) {
            me.headers = headers;
        }
        if(contents && contents.length) {
            me.contents = contents;
        }
    }
    //set the content to tab of index
    me.setTabContent = function(index, content) {
        $("#"+me.idc_tag+index).val(content);
    }
    //TODO: to be changed
    me.setActiveTab = function(index) {
        return me.tabsHtml;
    }
    me.render = function(callback) {

        me.callback = callback;

        $(me.renderTo).html(me.tabsHtml);
        me.bindEvents();
    }
    me.switchTabs = function(index) {
        //var me = this;
        $("#"+me.id_tag+me.activetab).removeClass('tabs_header_active').addClass("tabs_header");
        $("#"+me.idc_tag+me.activetab).hide();
        $("#"+me.id_tag+index).removeClass("tabs_header").addClass("tabs_header_active");
        $("#"+me.idc_tag+index).show();
        if(me.callback) {
            me.callback(index);
        }
        me.activetab = index;
        me.bindEvents();
    }
    me.bindEvents = function() {
        for(var i = 0; i < me.num; i++) {
             $("#"+me.id_tag+i).unbind();
             if(me.activetab == i) {
                 continue;
             }
             $("#"+me.id_tag+i).bind("click", function() {
                 me.switchTabs(parseInt($(this).attr("value")));
             });
        }
    }
}
