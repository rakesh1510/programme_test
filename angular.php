<DOCTYPE html>
<html ng-app="">
    <head>
        <meta CHARSET="UTF-8">
        <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>
		<title ng-bind="total"></title>
    </head>
    <body>
        Hello Angular
		<div ng-bind="total"></div>
		<?php
			$name = "Rakesh Prajapati";
		?>
                <div  ng-init="total='<?php echo $name; ?>'"></div>
    </body>
</html>