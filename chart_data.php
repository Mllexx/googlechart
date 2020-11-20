<?php
$c = new ChartData();

if(isset($_POST['user_id'])){
  $user = $_POST['user_id'];
  $startdate = $_POST['startdate'];
  $enddate = $_POST['enddate'];
  ## GET USER DATA
  $d = $c->getActivityLog($user,$startdate,$enddate,'minutes');
  echo json_encode($d);
}
$staff = $c->getStaffList();

/**
 * Data Processing Class
*/
class ChartData{

   const DB_HOST = 'localhost';
   const DB_USER = 'root';
   const DB_PASS = '';
   const DB_NAME = 'googlecharts';

   private function dbCon(){
      $pdo =  new PDO('mysql:host='.self::DB_HOST.';dbname='.self::DB_NAME.'',self::DB_USER,self::DB_PASS);
      return $pdo;
   }

   public function getStaffList(){
      $dbcon = $this->dbCon();
      $query = "select idclinician as id, clinician_id as code from clinician;";
      $resultset = $dbcon->query($query);
      $data = $resultset->fetchAll(PDO::FETCH_CLASS);
      return $data;
   }

   public function getActivityLog($userid,$startdate,$enddate,$timeunit='hours'){
      $dbcon = $this->dbCon();
      $st = $dbcon->prepare('Call GetUserActivityLog(?,?,?)');
      $st->bindParam(1,$userid,PDO::PARAM_STR);
      $st->bindParam(2,$startdate,PDO::PARAM_STR);
      $st->bindParam(3,$enddate,PDO::PARAM_STR);
      $st->execute();
      $datatable = [['Activity','Hours']];

      while($row = $st->fetch(PDO::FETCH_ASSOC)) {
        $row = (object)$row;
        ## convert all period to minutes
        $time = explode(':',$row->length);
        $time_in_minutes = ((int)$time[0]*60)+((int)$time[1]);
        $time_units = $time_in_minutes; ## DEFAULT
        if($timeunit==='hours'){
          $time_units = round(($time_in_minutes/60),2);
        }
        if($timeunit==='seconds'){
          $time_units = round(($time_in_minutes*60),2);
        }
        $i = [$row->activity,$time_units];
        array_push($datatable,$i);
      }
      return $datatable;
  }
}
