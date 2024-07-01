<?php
// Event List
$Json  = array ();
// Query that retrieves events
$Query = "SELECT * FROM evenement ORDER BY id" ;

// Connect to the database
try  {
	$Db  = new  PDO ( 'mysql: host = localhost; dbname = fullcalendar','root','');
} catch (Exception $e ) {
	exit ('Unable to connect to database'');
}
			// Query execution
			$result = $Db->query($Query) or die (print_r ($Db->errorInfo ()));

			// Send the result to success
			echo json_encode($result->fetchAll(PDO::FETCH_ASSOC));

?>