<?php

	include('vendor/autoload.php');
	use CirclePHP\Circle;

	$circle = new Circle('ayvjLE2ErKrBcTeZoyvYfKb4');

	function title($title) {

		echo "<h2>{$title}</h2><hr />";
	}

	function print_a($var) {
		echo "<pre>";
		print_r($var);
		echo "</pre>";
	}

	title('Get Me');
	print_a($circle->me());

	title('Get Communities');
	print_a($circle->communities());

	//Settting the default community
	$circle->setCommunity(28893);

	title('Get default community');
	print_a($circle->getCommunity());

	title('Show community');
	print_a($circle->community(28893, 'dojotrial'));

	title('Get Space Groups');
	print_a($circle->spaceGroups());

	title('Show Space Group');
	print_a($circle->spaceGroup(68487));

	title('Get Space Group Members');
	print_a($circle->spaceGroupMembers(68487));
?>