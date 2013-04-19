<HTML>
<HEAD>
 <TITLE>Data Logger demo</TITLE>
 <STYLE type='text/css'>
  table td {white-space: nowrap;}
 </STYLE>
</HEAD> 
<BODY>
<FORM method='get'>
<?php
  include('../../Sources/yocto_api.php');
  include('../../Sources/yocto_datalogger.php');

  // Use explicit error handling rather than exceptions
  yDisableExceptions();

  // Setup the API to use the VirtualHub on local machine
  if(yRegisterHub('http://127.0.0.1:4444/',$errmsg) != YAPI_SUCCESS) {
      die("Cannot contact VirtualHub on 127.0.0.1");
  }

  @$serial = $_GET['serial'];
  if ($serial != '') {
      // Check if a specified module is available online
      $logger = yFindDataLogger("$serial.dataLogger");   
      if (!$logger->isOnline()) { 
          die("Module not connected (check serial and USB cable)");
      }
  } else {
      // or use any connected module suitable for the demo
      $logger = yFirstDataLogger();
      if(is_null($logger)) {
          die("No data logger connected (check USB cable)");
      } else {
          $serial = $logger->module()->get_serialnumber();
      }
  }

  if($logger->isOnline()) {
      @$run = $_GET['run'];
      @$itv = $_GET['itv'];
      @$time = $_GET['time'];
      if($itv == '') $itv = '1';
      if($run == '') {
          // Main page: display controllers and result frames
          Print("<H1>Data Logger demo</H1>");
          Print("Module to use: <input name='serial' value='$serial'><br><br>");

          // Handle recorder on/off state
          @$state = $_GET['state'];
          if($state != '') {
              $logger->set_timeUTC(time());
              $logger->set_recording($state == '1' ? Y_RECORDING_ON : Y_RECORDING_OFF);
          }
          $isOn = ($logger->get_recording() ? 'checked' : '');
          $isOff = ($logger->get_recording() ? '' : 'checked');
          Print("Data logger recording state: ".
                "<input type='radio' name='state' value='1' $isOn>On ".
                "<input type='radio' name='state' value='0' $isOff>Off<br><br>\n");
          $showStreams = ($itv == '0' ? 'checked' : '');
          $itvs = Array(1,5,15,60);
          Print("Dispay interval between measures: ".
                "<input type='radio' name='itv' value='0' $showStreams>As recorded\n");
          for($i = 0; $i < sizeof($itvs); $i++) {
              $val = $itvs[$i];
              $chk = ($itv == $val ? 'checked' : '');
              Print(" - <input type='radio' name='itv' value='$val' $chk>$val min");
          }
          Print("\n<br><br><input type='submit' value='Submit and reload'><br><br>\n");

          // IFrame to display the stream list
          Print("<iframe id='listframe' width='100%' height=300 src='?serial=$serial&run=list&itv=$itv'>");
          Print("</iframe><br>");

          // IFrame to display the data set
          Print("<iframe id='dataframe' width='100%' height=500></iframe>");
      } else if($run == 'list' && $itv == '0') {
          // Dump list of available streams
          if($logger->get_dataStreams($streams) == YAPI_SUCCESS) {
              Print("Available data streams in the data logger:<br>");
              Print("<table border=1>\n<tr><th>Run</th><th>Relative time</th>".
                    "<th>UTC time</th><th>Measures interval</th></tr>\n");
              foreach($streams as $stream) {
                  $run  = $stream->get_runIndex();
                  $time = $stream->get_startTime();
                  $utc  = $stream->get_startTimeUTC();
                  $utc  = ($utc == 0 ? '' : date("Y-m-d H:i:s", $utc));
                  $itv  = $stream->get_dataSamplesInterval();
                  Print("<tr><td>#$run</td><td>$time [s]</td><td>$utc</td><td>$itv [s]</td>".
                        "<td><a href='javascript:show($run,$time)'>view</a></td></tr>\n");
              }
              Print("</table><br>\n");
              Print("<script language='javascript1.5' type='text/JavaScript'>\n");
              Print("function show(run, time)\n");
              Print("{ var frame = window.parent.document.getElementById('dataframe');\n");
              Print("  frame.src = '?serial=$serial&run='+run+'&time='+time+'&itv=0',1000; }\n");
              Print("</script>\n");
          }
      } else if($run == 'list') {
          // Dump list of available runs
          Print("Available data runs in the data logger:<br>");
          Print("<table border=1>\n<tr><th>Run</th><th>Start time (UTC)</th><th>Duration</th></tr>\n");
          $current = $logger->get_currentRunIndex();
          for($run = $logger->get_oldestRunIndex(); $run <= $current; $run++) {
              $datarun = $logger->get_dataRun($run);
              if($datarun != null) {
                  $duration = ceil($datarun->get_duration() / 60);
                  $utc  = $datarun->get_startTimeUTC();
                  $utc  = ($utc == 0 ? '' : date("Y-m-d H:i:s", $utc));
                  if($duration > 0) {
                      Print("<tr><td>#$run</td><td>$utc</td><td>$duration [min]</td>".
                            "<td><a href='javascript:show($run)'>view</a></td></tr>\n");
                  }
              }
          }
          Print("</table><br>\n");
          Print("<script language='javascript1.5' type='text/JavaScript'>\n");
          Print("function show(run)\n");
          Print("{ var frame = window.parent.document.getElementById('dataframe');\n");
          Print("  frame.src = '?serial=$serial&run='+run+'&itv=$itv',1000; }\n");
          Print("</script>\n");
      } else if($itv == '0') {
          // Dump selected stream
          $stream = new YDataStream($logger, $run, $time);
          $utc    = $stream->get_startTimeUTC();
          $itv    = $stream->get_dataSamplesInterval();
          $names  = $stream->get_columnNames();
          $values = $stream->get_dataRows();
          if(sizeof($names) > 0) {
              Print("<table border=1>\n<tr><th>time</th>");
              foreach($names as $name) Print("<th>$name</th>");
              foreach($values as $row) {
                  $when = ($utc > $time ? date("Y-m-d H:i:s", $utc) : "+$time [s]");
                  Print("</tr>\n<tr><td>$when</td>");
                  foreach($row as $val) Print("<td>$val</td>");
                  $time += $itv; 
                  $utc += $itv;
              }
              Print("</tr>\n</table>\n");
          }
      } else {
          // Dump selected run
          $itv = 60 * $itv;
          $datarun = $logger->get_dataRun($run);
          $datarun->set_valueInterval($itv);
          $utc   = $datarun->get_startTimeUTC();
          $names = $datarun->get_measureNames();
          $count = $datarun->get_valueCount();
          if($count > 0) {
              Print("<table border=1>\n<tr><th>time</th>");
              foreach($names as $name) Print("<th colspan='3'>$name</th>");
              Print("</tr>\n<tr><th></th>");
              foreach($names as $name) Print("<th>min</th><th>average</th><th>max</th>");
              $time  = 0;
              for($i = 0; $i < $count; $i++) {
                  if($datarun->get_averageValue($names[0], $i) == Y_MINVALUE_INVALID) continue;
                  $when = ($utc > $time ? date("Y-m-d H:i:s", $utc) : "+$time [s]");
                  Print("</tr>\n<tr><td>$when</td>");
                  foreach($names as $name) {
                      $minVal = $datarun->get_minValue($name, $i);
                      $avgVal = $datarun->get_averageValue($name, $i);
                      $maxVal = $datarun->get_maxValue($name, $i);
                      Print("<td>$minVal</td><td>$avgVal</td><td>$maxVal</td>");
                  }
                  $time += $itv; 
                  $utc += $itv;
              }
              Print("</tr>\n</table>\n");
          }
      }
  }
?>  
</FORM>
</BODY>
</HTML> 
