<!DOCTYPE html>
<html>
    <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" type="text/css" href="design.css">
            <link rel="stylesheet" href="https://bootswatch.com/4/superhero/bootstrap.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>   
    <script type="text/javascript" src="chart.js"></script>
    </head>
    <body>     
    <div id="a"></div>
    <div id="aligned">
        <div class="label">Aligned</div>
        <div class="chart_container">
            <canvas id="chart1"></canvas>
            <!-- <canvas id="chart1" float="left" width="400" height="400"></canvas> -->
        </div>
        <div class="chart_container">
            <canvas id="chart1"></canvas>
            <!-- <canvas id="chart2" float="right" width="400" height="400"></canvas> -->
        </div>
    </div>
    <script>plot()</script> 
        <!-- <canvas id="chart1" float="left" width="400" height="400"></canvas>
        <div id="chart2" float="right" width="400" height="400"></div> -->
    </body>

</html>