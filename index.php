<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

define('DATABASE', 'sk2545');
define('USERNAME', 'sk2545');
define('PASSWORD', 'gkMQzyEKO');
define('CONNECTION', 'sql2.njit.edu');

class Manage {
    public static function autoload($class) {
   include $class . '.php';
}
}

spl_autoload_register(array('Manage', 'autoload'));


$obj=new formhtml;
$obj=new main();

class dbConn{
   
       protected static $db;
           
       private function __construct() {
       try {
       self::$db = new PDO( 'mysql:host=' . CONNECTION .';dbname=' . DATABASE, USERNAME, PASSWORD );
       self::$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
       }
       catch (PDOException $e) {
				                   
       echo "Connection Error: " . $e->getMessage();
       }
}

public static function getConnection() {
        
      if (!self::$db) {
      new dbConn();
      }
		          
      return self::$db;
      }
}

abstract class collection {

protected $html;

    static public function create() {
    $model = new static::$modelName;
    return $model;
    }
    static public function findAll() {
    $db = dbConn::getConnection();
    $tableName = get_called_class();
    $sql = 'SELECT * FROM ' . $tableName;
    $statement = $db->prepare($sql);
    $statement->execute();
    $class = static::$modelName;
    $statement->setFetchMode(PDO::FETCH_CLASS,$class);
    $recordsSet =  $statement->fetchAll();
    return $recordsSet;
}

    static public function findOne($id) {
    $db = dbConn::getConnection();
    $tableName = get_called_class();
    $sql = 'SELECT * FROM ' . $tableName . ' WHERE id =' . $id;
    $statement = $db->prepare($sql);
    $statement->execute();
    $class = static::$modelName;
    $statement->setFetchMode(PDO::FETCH_CLASS, $class);
    $recordsSet =  $statement->fetchAll();
    //print_r($recordsSet);
    return $recordsSet[0];
    }
}

class accounts extends collection {
    protected static $modelName = 'account';
    }

class todos extends collection {
    protected static $modelName = 'todo';
}

abstract class model {

protected $tableName;

public function save()
    {
     if ($this->id != '') {
     $sql = $this->update();
     } else {
     $sql = $this->insert();
     }
     $db = dbConn::getConnection();
     $statement = $db->prepare($sql);
     $array = get_object_vars($this);
     foreach (array_flip($array) as $key=>$value){
     $statement->bindParam(":$value", $this->$value);
     }
     $statement->execute();
     $id = $db->lastInsertId();
     return $id;
     }

private function insert() 
    {      
     $modelName=get_called_class();
     $tableName = $modelName::getTablename();
     $array = get_object_vars($this);
     $columnString = implode(',', array_flip($array));
     $valueString = ':'.implode(',:', array_flip($array));
     print_r($columnString);
     $sql =  'INSERT INTO '.$tableName.' ('.$columnString.') VALUES ('.$valueString.')';
     return $sql;
     }
									
private function update() 
    {  
     $modelName=get_called_class();
     $tableName = $modelName::getTablename();
     $array = get_object_vars($this);
     $comma = " ";
     $sql = 'UPDATE '.$tableName.' SET ';
     foreach ($array as $key=>$value)
     {
     if( ! empty($value)) {
     $sql .= $comma . $key . ' = "'. $value .'"';
     $comma = ", ";
     }
     }
     $sql .= ' WHERE id='.$this->id;
     return $sql;
    }

public function delete() 
    {
     //echo"In delete";
     $db = dbConn::getConnection();
     $modelName=get_called_class();
     $tableName = $modelName::getTablename();
     $sql = 'DELETE FROM '.$tableName.' WHERE id ='.$this->id;
     $statement = $db->prepare($sql);
     //print_r($sql);
     $statement->execute();
     }
}
//

class account extends model {
    public $id;
    public $email;
    public $fname;
    public $lname;
    public $phone;
    public $birthday;
    public $gender;
    public $password;
    public static function getTablename(){
    $tableName='accounts';
    return $tableName;
    }
}

class todo extends model {
    public $id;
    public $owneremail;
    public $ownerid;
    public $createddate;
    public $duedate;
    public $message;
    public $isdone;
    public static function getTablename(){
	   $tableName='todos';
	   return $tableName;
    }
}


class main
{
public function __construct()
{

    $form = '<form method="post" enctype="multipart/form-data">';
    $form .= '<h2>Display all records from Accounts</h2>';
    $records = accounts::findAll();
    $html = formhtml::tableDisplayFunction($records);
    $form .='<left>'.$html.'</left><hr>'; 


    $id = 4;
    $records = accounts::findOne($id);
    $html = formhtml::tableDisplayFunction_1($records);
    $form .= '<h2>Select One Record </h2>';
    $form .="<h3>Record fetched with id - <i>".$id."</i></h3>";
    $form .= '<left>'.$html.'</left><hr>';

    $form .="<h2>Insert One Record</h2>";
    $record = new account();
    $record->email="new@mail.com";
    $record->fname="cristiano";
    $record->lname="ronaldo";
    $record->phone="648541618";
    $record->birthday="09-06-1987";
    $record->gender="male";
    $record->password="asowmsco2134";
    $lstId=$record->save();
    $records = accounts::findAll();
    print_r($lstId);
    $form .="<h3>Record inserted with id - <i>".$lstId."</i></h3>";
    $html = formhtml::tableDisplayFunction($records);
    $form .='<h3>After inserting the record - </h3>';
    $form .='<left>'.$html.'</left><hr>';
//      							    
    $form .= "<h2>Update a Record</h2>";
  //$id=30;
    $records = accounts::findOne($lstId);
    $record = new account();
    $record->id=$records->id;
    $record->email="email_Update@njit.edu";
    $record->fname="fname_Update";
    $record->lname="lname_Update";
    $record->gender="gender_Update";
    $record->save();
    $records = accounts::findAll();
    $form .="<h3>Updating record having id: <i>".$lstId."</i></h3>";
    $html = formhtml::tableDisplayFunction($records);
    $form .='<left>'.$html.'</left><hr>';

    $form .= "<h2>Delete One Record</h2>";
    $records = accounts::findOne($lstId);
    $record= new account();
    $record->id=$records->id;
      //print_r($records);
    $records->delete();
    $form .='<h3>Record with id: <i>'.$records->id.'</i> is deleted</h3>';
    $records = accounts::findAll();
    $html = formhtml::tableDisplayFunction($records);
    $form .='<h3>After Deleteing</h3>';
    $form .='<left>'.$html.'</left><br><hr>';
    print_r($form);
							  
//

    $form .= '<br><hr></br>';
    $form .= '<h2>Display all records in Todos Table</h2>';
    $records = todos::findAll();
    $html = formhtml::tableDisplayFunction($records); 
    $form .='<left>'.$html.'</left><hr>';
//    
    $id = 7;
    $records = todos::findOne($id);
    $html = formhtml::tableDisplayFunction_1($records);
    $form .='<h2> Display One Record</h2>';
    $form .='<h3> Record fetched with id: <i>'.$id.'</i></h3>';
    $form .='<left>'.$html.'</left><hr>';

//
    $form .="<h2>Insert a record</h2>";
    $record = new todo();
    $record->owneremail="test@mail.com";
    $record->ownerid=06;
    $record->createddate="09-03-2017";
    $record->duedate="10-11-2017";
    $record->message="New task";
    $record->isdone=1;
    $lstId=$record->save();
    $records = todos::findAll();
    echo"<h3>After Inserting</h3>";
    $form .="<h3>Record inserted with id - <i>".$lstId."</i></h3>";
    $html = formhtml::tableDisplayFunction($records);
    $form .='<h3>After inserting the record - </h3>';
    $form .='<left>'.$html.'</left><hr>';
//							
    $form .="<h2> Update record</h2>";
    $id=41;
    $records = todos::findOne($lstId);
    $record = new todo();
    $record->id=$records->id;
    $record->owneremail="updatetest@njit.edu";
    $record->message="Update Active record ";
    $record->save();
    $records = todos::findAll();
    $form .="<h3>Updating record having id: <i>".$lstId."</i></h3>";
    $html = formhtml::tableDisplayFunction($records);
    $form .='<left>'.$html.'</left><hr>';

    $form .= "<h2> Delete One Record</h2>";
    //print_r($lstId);
    $records = todos::findOne($lstId);
    $record= new todo();
    $record->id=$records->id;
    print_r($records);
    $records->delete();
    $form .='<h3>Record with id: <i>'.$records->id.'</i> is deleted</h3>';
    //echo "After Delete";
    $records = todos::findAll();
    $html = formhtml::tableDisplayFunction($records);
    $form .="<h3>After Deleteing</h3>";
    $form .='<left>'.$html.'</left><hr>'; 
    $form .='</form>';
    print_r($form);
 }
}

?>









































































