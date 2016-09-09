<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ECharts">
    <meta name="author" content="kener.linfeng@gmail.com">
    <title>LAS Reporting Sample</title>

    <link rel="shortcut icon" href="../jslib/echarts/doc/asset/ico/favicon.png">

    <link href="../jslib/echarts/doc/asset/css/font-awesome.min.css" rel="stylesheet">
    <link href="../jslib/echarts/doc/asset/css/bootstrap.css" rel="stylesheet">
    <link href="../jslib/echarts/doc/asset/css/carousel.css" rel="stylesheet">
    <link href="../jslib/echarts/doc/asset/css/echartsHome.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="../jslib/echarts/doc/example/www/js/echarts.js"></script>
    <script src="../jslib/echarts/doc/asset/js/codemirror.js"></script>
    <script src="../jslib/echarts/doc/asset/js/javascript.js"></script>

    <link href="../jslib/echarts/doc/asset/css/codemirror.css" rel="stylesheet">
    <link href="../jslib/echarts/doc/asset/css/monokai.css" rel="stylesheet">
    <link href="../reporting/css/option.css" rel="stylesheet">
</head>

<body>
    <!-- Fixed navbar -->
    <div class="navbar navbar-default navbar-fixed-top" role="navigation" id="head"></div>

    <div class="container-fluid">
        <div class="row-fluid example">
            <div id="graphic" class="col-md-8" style="width:880px">
                <div id="id_filters" style="display:none">
                    <span id="pkmap-label" class="text-primary">選擇學生:</span>
                    <select style="margin-bottom:20px" id="pkmap-select"></select>
                    <span id="dimension-label" class="text-primary">選擇范畴:</span>
                    <select style="margin-bottom:20px" id="dimension-select"></select>
                </div>
                <div id="group_filters" style="display:none">
                    <span id="group-label" class="text-primary">選擇分組:</span>
                    <select style="margin-bottom:20px" id="group-select"></select>
                </div>
                <div id="main" class="main"></div>
                <div id="main2" class="main" style="display:none">
                    <div id="main2_left" style="width:49%;height:100%;float:left"></div>
                    <div id="main2_right" style="width:49%;float:left;height:100%"></div>
                </div>
                <div id="submain" style="display:none;height:200px"></div>
            </div><!--/span-->
        </div><!--/row-->
        
    </div><!--/.fluid-container-->
    <div id="chart_description"></div>
    <div style="text-align:center;width:880px;color:#004282;padding-top:15px;">Powered by
      <img style="width:120px;margin-top:-15px" src="http://www.astri.org/skin/astri/images/astri-logo.png" >
    </div>

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="../jslib/echarts/doc/asset/js/jquery.min.js"></script>

<!--
    {literal}
    <script type="text/javascript">
        var token = {$token};
    </script>
    {/literal}
    <script src="tabs.js"></script>
-->
    <script type="text/javascript" src="../reporting/js/head.js"></script>
    <script src="../jslib/echarts/doc/asset/js/bootstrap.min.js"></script>
    <script src="../reporting/lib/file_get_json.php?token={$data_token}"></script>
    <script src="../reporting/js/ztabs.js"></script>
    <script src="../reporting/js/option.js"></script>
    <script src="../reporting/js/optiontop.js"></script>
    <script src="../reporting/js/optionbasic.js"></script>
    <script src="../reporting/js/optionpie.js"></script>
    <script src="../reporting/js/optionradar.js"></script>
    <script src="../reporting/js/optiondimension.js"></script>
    <script src="../reporting/js/optionbubble.js"></script>
    <script src="../reporting/js/optionpkmap.js"></script>
    <script src="../reporting/js/optionmci.js"></script>
    <script src="../reporting/js/optionrank.js"></script>
    <script src="../reporting/js/optiongroups.js"></script>
    <script src="../reporting/js/optionabilitysum.js"></script>
    <script src="../reporting/js/report.js"></script>
    <link href="../reporting/css/ztabs.css" rel="stylesheet">
</body>
</html>
