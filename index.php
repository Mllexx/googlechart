<?php require_once('chart_data.php'); ?>

<!doctype html>
<html class="no-js" lang="">

<head>
  <meta charset="utf-8">
  <title></title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta property="og:title" content="">
  <meta property="og:type" content="">
  <meta property="og:url" content="">
  <meta property="og:image" content="">

  <link rel="manifest" href="site.webmanifest">
  <link rel="apple-touch-icon" href="icon.png">
  <!-- Place favicon.ico in the root directory -->

  <link rel="stylesheet" href="css/normalize.css">
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" />
  <meta name="theme-color" content="#fafafa">
</head>

<body>

<div class="container-fluid">
<!-- Main Container -->
  <div class="row">
    <div class="col-sm">

      <div class="card">
        <h5 class="card-header primary">
          Google Charts [Pie Chart] Demo
        </h5>
        <div class="card-body">
          <h6 class="card-title">Chart Parameters:</h6>
          <!-- Actual Content -->
          <div class="row">
            <div class="col-lg-3 col-sm-12 col-xs-12">
              <!-- Drop Down & Labelling Section-->
              <form class="form" method="POST" action='chart_data.php'>
                <div class="form-group">
                    <label class="" for="user">Clinician ID:</label>
                    <select class="form-control chart-parameter" id="user">
                      <?php foreach($staff as $s){?>
                        <option value="<?php echo $s->id; ?>"><?php echo $s->code; ?></option>
                      <?php } ?>
                    </select>
                </div>

                  <!-- Start Date -->
                  <div class="form-group">
                      <label class="" for="start_date">Start Date:</label>
                      <input type="text" class="form-control datepicker chart-parameter" id="start_date" name="start_date"/>
                  </div>

                  <!-- End Date -->
                  <div class="form-group">
                      <label class="" for="end_date"> End Date:</label>
                      <input type="text" class="form-control datepicker chart-parameter" id="end_date" name="end_date"/>
                  </div>

              </form>
              <!-- Summary Table-->
              <table class="table table-dark" id="summary-table" style="display: none;">
                <tr><th>Total Duration:</th></tr>
                <tr><td></td></tr>
              </table>
            </div>
            <div class="col-lg-9 col-sm-12 col-xs-12">
              <!-- Chart Rendering Section -->
              <div id="piechart" style="width: 960px; height: 720px;"></div>
            </div>
          </div>
        </div>
      <div>
    </div>
  </div>

</div>


  <script src="js/vendor/modernizr-3.11.2.min.js"></script>
  <script src="js/plugins.js"></script>
  <script src="js/main.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous"></script>

  <!-- Google Charts -->
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    $(document).ready(function(){
      //Initialize datepickers
      $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
      });

      function drawPieChart() {
          let userid = $('#user').val();
          let startdate = $('#start_date').val();
          let enddate = $('#end_date').val();

          let dataString = $.ajax({
            method:'POST',
            data:{'user_id':userid,'startdate':startdate,'enddate':enddate},
            url: "chart_data.php",
            dataType: "json",
            async: false,
            error:function(){
              alert('An error occured, refresh the page and try again');
              return false;
            }
          }).responseText;
          //console.log(dataString);
          let jsonData = JSON.parse(dataString);
          let options = {
            title: 'Clinicians Activity Log',
            is3D: true,
            width: 950,
            height: 550
          };
          let data = new google.visualization.arrayToDataTable(jsonData.datatable);
          let chart = new google.visualization.PieChart(document.getElementById('piechart'));
          chart.draw(data, options);
          //ATTACH SUMMARY
          let $smry_text = jsonData.summary.hours+" Hours, "+jsonData.summary.minutes+" Minutes";
          $('#summary-table td').html($smry_text);
          $('#summary-table').show();
      }

      /* Event listener for user selector */
      $('.chart-parameter').change(function(){
        // Ensure the start date is provided
        if($('#start_date').val()===''){
          return false;
        }
        // Ensure the end date is provided
        if($('#end_date').val()===''){
          return false;
        }
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawPieChart);
      });

    });
  </script>
</body>

</html>