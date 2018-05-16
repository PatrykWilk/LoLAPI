<?php
    // Connect to database
	include('../_include/dbconnection.inc');
    $connection =  $conn;

    $request_method=$_SERVER["REQUEST_METHOD"];

    switch($request_method)
	{
		case 'GET':
			// Retrive Products
			if(!empty($_GET["id"]))
			{
				$id=intval($_GET["id"]);
				get_players($id);
			}
			else
			{
				get_players();
			}
			break;
		default:
			// Invalid Request Method
			header("HTTP/1.0 405 Method Not Allowed");
			break;
    }
    
    function get_players()
	{
		global $connection;
		$query="SELECT * FROM Players";
		$response=array();
		$result=mysqli_query($connection, $query);
		while($row=mysqli_fetch_assoc($result))
		{
            $response[]=$row;
		}
		header('Content-Type: application/json');
        echo json_encode($response);
    }
    
    // function get_players($id=0)
    // {
    //     global $connection;
    //     $query="SELECT * FROM Players";
    //     if($id != 0)
    //     {
    //         $query.=" WHERE id=".$id." LIMIT 1";
    //     }
    //     $response=array();
    //     $result=mysqli_query($connection, $query);
    //     while($row=mysqli_fetch_array($result))
    //     {
    //         $response[]=$row;
    //     }
    //     header('Content-Type: application/json');
    //     echo json_encode($response);
    // }
    
?>