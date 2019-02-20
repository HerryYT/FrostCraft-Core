
<?php
namespace core\SQL;
class Client{
public static function addConexion(){
  return new \mysqli("ip","usuario","pswd","db");
}
public static function stopConexion(){
  return self::addConexion()->close();
}


}


 ?>
