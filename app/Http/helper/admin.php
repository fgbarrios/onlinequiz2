<?php 

# Application default date format

function app_date_format($date)
{
	return date('m-d-Y h:i:s A',strtotime($date));
}

# Application encode method

function app_encode($data)
{
	return base64_encode($data);
}

# Application decode method

function app_decode($data)
{
	return base64_decode($data);
}

# Print details with preview

function aa($data)
{
	echo "<pre>";
	print_r($data);
	echo "</pre>";
}

# Print details with preview and exit

function ae($data)
{
	echo "<pre>";
	print_r($data);
	echo "</pre>";
	exit();
}

?>