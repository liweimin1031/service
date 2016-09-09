function Tabs(target) {
    var me = this;

    me.num = 2; //initial tabs = 2

    me.renderTo = target;

    me.setTabNum = function(num) {
        me.num = num;
    }
    //set the content to tab of index
    me.setTabContent = function(index, content) {
        
    }
    me.render = function() {

        var html = '';
        /*html += '<div class="ui-tabs">'+
        	'<ul class="ui-tabs-nav">'+
        		'<li><a href="#tabs-1">Tab One</a></li>'+
        		'<li><a href="#tabs-2">Tab Two</a></li>'+
        		'<li><a href="#tabs-3">Tab Three</a></li>'+
        	'</ul>'+
        	'<div id="tabs-1" class="ui-tabs-panel">'+
        		'<p>Content one.</p>'+
        	'</div>'+
        	'<div id="tabs-2" class="ui-tabs-panel">'+
        		'<p>Content two.</p>'+
        	'</div>'+
        	'<div id="tabs-3" class="ui-tabs-panel">'+
        		'<p>Content three.</p>'+
        	'</div>'+
        '</div>';*/

        html += '<div style="width: 500px; margin: 0 auto; padding: 120px 0 40px;">'+
            '<ul class="tabs" data-persist="true">'+
                '<li><a href="#view1">Lorem</a></li>'+
                '<li><a href="#view2">Using other templates</a></li>'+
                '<li><a href="#view3">Advanced</a></li>'+
            '</ul>'+
            '<div class="tabcontents">'+
                '<div id="view1">'+
                    '<b>Lorem Issum</b>'+
                    '<p>Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...</p>'+
                   
                '</div>'+
                '<div id="view2">'+
                    '<b>Switch to other templates</b>'+
                    '<p>Open this page with Notepad, and update the CSS link to:</P>'+
                    '<p>template1 ~ 6.</p>                '+
                '</div>'+
                '<div id="view3">'+
                    '<b>Advanced</b>'+
                    '<p>If you expect a more feature-rich version of the tabber, you can use the advanced version of the script, '+
                        '<a href="http://www.menucool.com/jquery-tabs">McTabs - jQuery Tabs</a>:</p>'+
                    '<ul>'+
                        '<li>URL support: a hash id in the URL can select a tab</li>'+
                        '<li>Bookmark support: select a tab via bookmark anchor</li>'+
                        '<li>Select tabs by mouse over</li>'+
                        '<li>Auto advance</li>'+
                        '<li>Smooth transitional effect</li>'+
                        '<li>Content can retrieved from other documents or pages through Ajax</li>'+
                        '<li>... and more</li>     '+
                    '</ul>'+
                '</div>'+
            '</div>'+
        '</div>';

        $(this.renderTo).html(html);
        //$('.ui-tabs').tabs();
    }
}
