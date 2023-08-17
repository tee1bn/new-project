<?php

$router = [
	'' => 'home',
	'home' => 'home',
	'bet-converter' => 'home',
	// 'tips' => 'TipsController',
	'supportmessages' => 'crud/TicketCrudController',
	'w' => 'home',
	'user' => 'UserController',			//this is used to build all urls of the user dashboard
	'withdrawals' => 'WithdrawalsController',
	'deposits' => 'DepositController',
	'media' => 'crud/MediaController',

	'document' 			=> 'DocumentController',
	'support' 			=> 'SupportController',

	'user-profile'		=> 'UserProfileController',
	'register' 			=> 'RegisterController',
	'login' 			=> 'LoginController',
	'verify' 			=> 'VerificationController',
	'shop' 				=> 'shopController',
	'error' 			=> 'ErrorController',

	'test' => 'test/home',

	'pg' 	=> 'PagesController',
	'demo' 	=> 'DemoController',
	'embed' 	=> 'EmbedController',
	'blog' 	=> 'BlogController',


	'company' => 'api/CompanyController',

	'cms_api' => 'CmsApiController',
	'convert' 	=> 'ConvertController',
	'convertbetcodes' 	=> 'ConvertController',

	'c' 	=> 'ConversionsController',

	'guest' 	=> 'GuestController',
	'terms' 	=> 'TermsController',
	'genealogy' => 'GenealogyController',
	'report' 	=> 'ReportsController',
	'ref' 		=> 'ReferralController', //referral link handler
	'r' 		=> 'ReferralController', //referral link handler
	'forgot-password' 	=> 'forgotPasswordController',

	'auto-match' => 'AutoMatchingController',	//this handles routine checks and commssions

	'settings' => 'SettingsController',
	'testing' => 'testingController',


	/* campaign */
	'category_crud' => 'crud/CategorySpoof',
	'campaign_crud' => 'crud/CampaignCrudController',
	'campaign_execution' => 'crud/CampaignExecution',





	'ticket_crud' => 'crud/TicketCrudController',
	'cms_crud' => 'crud/CmsCrud',
	'user_doc_crud' => 'crud/UserDocCrudController',
	'package_crud' => 'crud/PackageCrudController',
	'tips_crud' => 'crud/TipsCrudController',


	#wallets
	'accounts' => "wallet/AccountsController",
	'journals' => "wallet/JournalsController",
	'trial-balance' => "wallet/TrialBalanceController",
	'withrawals' => 'WithdrawalsController',

	/*
	*/

	#admin
	'admin-dashboard' => 'AdminDashboardController',
	'admin' => 'AdminController',
	'admin-profile' => 'AdminProfileController',
	'admin-products' => 'AdminProductsController',

	'api' => 'api/APIRouteController',
	'i' => 'RouteController',
	'factory' => 'api/FactoryController',



	"meet" => "MeetingController"
];
