<!DOCTYPE html><!--
	To change this license header, choose License Headers in Project Properties.
	To change this template file, choose Tools | Templates
	and open the template in the editor.
	--> 
<html class="no-js">
	<head>
		<title>WPL Password Reset</title>
		<meta charset="UTF-8">
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<!-- <link rel="stylesheet" href="styles/main.30ac1ef3.css"> -->
		<?php include '../webservice/assets.php'; ?>
	<body ng-app="wplForgotPassword">
		<?php include "../webservice/top_menu.php"; ?>
		<div class="page-wrapper">
			<div class="section-wrapper login-section">

				<div class="spa clearfix" ng-controller="forgotPasswordController">
					<div class="section-title">Request Password Reset</div>
					<form id="reset_form" novalidate name="form">
						<div class="field-group col-1"> <div class="input-label label" for="email">Email Address</div> <input id="email" type="text" ng-model="user.email" required ng-minlength="2" ng-maxlength="45"> </div>
						<div class="buttons"> <button ng-click="requestReset()" ng-disabled="form.$invalid">Reset Password</button> </div>
					</form><!-- end form -->
	        
	        <div class="label forgot-link"><a href="login.php">Remembered Password?</a></div>
	        
	        <div class='result-message'>
	          <p>{{resultMessage.message}}</p>
	        </div>
				</div><!-- end .clearfix.spa -->

			</div><!-- end .login-wrapper -->

      
  </div><!-- end .page-wrapper -->

  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.23/angular.min.js"></script>
  <script src="js/lib/md5.js"></script>
  <script src="js/app/ForgotPasswordApp.js"></script>

