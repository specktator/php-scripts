<?php
/*
	Description: Change all tables and all columns from latin* to utf8 collation
	
 */
error_reporting(E_ALL);
$db = mysql_connect('localhost','username','password');
if(!$db) echo "Cannot connect to the database - incorrect details";

mysql_select_db('project'); $result=mysql_query('show tables');
echo "<pre>";
while($tables = mysql_fetch_array($result))
{
  foreach ($tables as $key => $table)
  {
    echo "---------$table------------ \n";
    $res = mysql_query("SHOW FULL COLUMNS FROM $table");
    while ($columns = mysql_fetch_assoc( $res ) )
    {
    	print_r($columns);
	    foreach ($columns as $ckey => $val)
	    {
	      if( preg_match('/latin/', $columns['Collation']) != false )
	      {
	      	echo "changing $val \n";
	      	$query = "ALTER TABLE $table CHANGE ".$columns['Field']." ".$columns['Field']." ".$columns['Type']." CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT ".($columns['Default'] ? $columns['Default'] : 'NULL');
	      	mysql_query($query)  or die("Can't change table: $table column: $column Collation \n");
	      }
	    }
    }
  }
}
echo "The collation of your database has been successfully changed!";
echo "</pre>";
?>
