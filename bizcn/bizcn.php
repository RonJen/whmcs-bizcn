<?php
/**
 * 文件来源：https://whmcs.im WHMCS中文网
 * WHMCS SDK Sample Registrar Module
 *
 * Registrar Modules allow you to create modules that allow for domain
 * registration, management, transfers, and other functionality within
 * WHMCS.
 *
 * This sample file demonstrates how a registrar module for WHMCS should
 * be structured and exercises supported functionality.
 *
 * Registrar Modules are stored in a unique directory within the
 * modules/registrars/ directory that matches the module's unique name.
 * This name should be all lowercase, containing only letters and numbers,
 * and always start with a letter.
 *
 * Within the module itself, all functions must be prefixed with the module
 * filename, followed by an underscore, and then the function name. For
 * example this file, the filename is "registrarmodule.php" and therefore all
 * function begin "registrarmodule_".
 *
 * If your module or third party API does not support a given function, you
 * should not define the function within your module. WHMCS recommends that
 * all registrar modules implement Register, Transfer, Renew, GetNameservers,
 * SaveNameservers, GetContactDetails & SaveContactDetails.
 *
 * For more information, please refer to the online documentation.
 *
 * @see https://developers.whmcs.com/domain-registrars/
 *
 * @copyright Copyright (c) WHMCS Limited 2017
 * @license https://www.whmcs.com/license/ WHMCS Eula
 */
if ( !defined( "WHMCS" ) ) {
	die( "This file cannot be accessed directly" );
}
!defined('DEBUG') && define('DEBUG',false);//Toggle Debug
include 'bizcn.isoap.class.php';
use \WHMCS\Domains\DomainLookup\ResultsList;
use \WHMCS\Domains\DomainLookup\SearchResult;
// Require any libraries needed for the module to function.
// require_once __DIR__ . '/path/to/library/loader.php';
//
// Also, perform any initialization required by the service's library.
/**
 * Define module related metadata
 *
 * Provide some module information including the display name and API Version to
 * determine the method of decoding the input values.
 *
 * @return array
 */
function bizcn_MetaData() {
	return array(
		'DisplayName' => 'Bizcn Registrar Module for WHMCS',
		'APIVersion' => '2.0 Beta 1',
	);
}
/**
 * Define registrar configuration options.
 *
 * The values you return here define what configuration options
 * we store for the module. These values are made available to
 * each module function.
 *
 * You can store an unlimited number of configuration settings.
 * The following field types are supported:
 *  * Text
 *  * Password
 *  * Yes/No Checkboxes
 *  * Dropdown Menus
 *  * Radio Buttons
 *  * Text Areas
 *
 * @return array
 */
function bizcn_getConfigArray() {
	return array(
		"Username" => array(
			"Type" => "text",
			"Size" => "20",
			"Description" => "Enter your username here"
		),
		"Password" => array(
			"Type" => "password",
			"Size" => "20",
			"Description" => "Enter your password here"
		),
		"Platform" => array(
			"FriendlyName" => "Platform",
			"Type" => "dropdown", # Dropdown Choice of Options
			"Options" => "bizcn,cnobin",
			"Description" => "Please choose your platform.",
			"Default" => "bizcn"
		),
		"TestMode" => array(
			"Type" => "yesno"
		),
		"DebugMode" => array(
			"Type" => "yesno"
		),
	);
}
/**
 * Register a domain.
 *
 * Attempt to register a domain with the domain registrar.
 *
 * This is triggered when the following events occur:
 * * Payment received for a domain registration order
 * * When a pending domain registration order is accepted
 * * Upon manual request by an admin user
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function bizcn_RegisterDomain( $params ) {
	$username = $params["Username"];
	$password = $params["Password"];
	$platform = $params["Platform"];
	$testmode = $params["TestMode"];
	$debugmode = $params["DebugMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
	$regperiod = $params["regperiod"];
	$nameserver1 = $params["ns1"];
	$nameserver2 = $params["ns2"];
    $nameserver3 = $params["ns3"];
    $nameserver4 = $params["ns4"];
	# Registrant Details
	$RegistrantFirstName = $params["firstname"];
	$RegistrantLastName = $params["lastname"];
	# add by beeyon 2010-07-22 get "dom_org" value from companyname
	$RegistrantCompanyName = $params["companyname"];
	if(empty($RegistrantCompanyName))
		$RegistrantCompanyName=$RegistrantFirstName." ".$RegistrantLastName;
	$RegistrantAddress1 = $params["address1"];
	$RegistrantAddress2 = $params["address2"];
	$RegistrantCity = $params["city"];
	$RegistrantStateProvince = $params["state"];
	$RegistrantPostalCode = $params["postcode"];
	$RegistrantCountry = $params["country"];
	$RegistrantEmailAddress = $params["email"];
	$RegistrantPhone = $params["phonenumber"];
	# Admin Details
	$AdminFirstName = $params["adminfirstname"];
	$AdminLastName = $params["adminlastname"];
	$AdminAddress1 = $params["adminaddress1"];
	$AdminAddress2 = $params["adminaddress2"];
	$AdminCity = $params["admincity"];
	$AdminStateProvince = $params["adminstate"];
	$AdminPostalCode = $params["adminpostcode"];
	$AdminCountry = $params["admincountry"];
	$AdminEmailAddress = $params["adminemail"];
	$AdminPhone = $params["adminphonenumber"];

	$DnsIp1=$params["dns_ip1"];
	$DnsIp2=$params["dns_ip2"];
	# Put your code to register domain here

$user=array(
	'name'=>$username
	,'password'=>$password
);
	$_params=array(
		"user"=>$user,
		"platform"=>$platform,
		"testmode"=>$testmode,
		"debugmode"=>$debugmode,
		"module"=>"addDomainService",
		'paras'=>array(
			'create'=>array(
				"domainname"=>$sld.".".$tld,
				"term"=>$regperiod,
				"dns_host1"=>$nameserver1, 
				"dns_host2"=>$nameserver2,  
				"dom_org"=>$RegistrantCompanyName,//$RegistrantFirstName." ".$RegistrantLastName, modify by beeyon 2010-07-22
				"dom_fn"=>$RegistrantFirstName,
				"dom_ln"=>$RegistrantLastName, 
				"dom_adr1"=>$RegistrantAddress1 . ' ' . $RegistrantAddress2,
				"dom_ct"=>$RegistrantCity , 
				"dom_st"=>$RegistrantStateProvince, 
				"dom_co"=>$RegistrantCountry, 
				"dom_ph"=> '+86.' . $RegistrantPhone, 
				"dom_fax"=>'+86.' .$RegistrantPhone, 
				"dom_pc"=>$RegistrantPostalCode,
				"dom_em"=>$RegistrantEmailAddress, 
		
				"admi_fn"=>$AdminFirstName,
				"admi_ln"=>$AdminLastName, 
				"admi_adr1"=>$AdminAddress1. ' ' .$AdminAddress2,
				"admi_ct"=>$AdminCity , 
				"admi_st"=>$AdminStateProvince, 
				"admi_co"=>$AdminCountry, 
				"admi_ph"=>'+86.' .$AdminPhone, 
				"admi_fax"=>'+86.' .$AdminPhone, 
				"admi_pc"=>$AdminPostalCode,
				"admi_em"=>$AdminEmailAddress, 
		
				"tech_fn"=>$AdminFirstName,
				"tech_ln"=>$AdminLastName, 
				"tech_adr1"=>$AdminAddress1. ' ' .$AdminAddress2,
				"tech_ct"=>$AdminCity , 
				"tech_st"=>$AdminStateProvince, 
				"tech_co"=>$AdminCountry, 
				"tech_ph"=>'+86.' .$AdminPhone, 
				"tech_fax"=>'+86.' .$AdminPhone, 
				"tech_pc"=>$AdminPostalCode,
				"tech_em"=>$AdminEmailAddress, 
		
				"bill_fn"=>$AdminFirstName,
				"bill_ln"=>$AdminLastName, 
				"bill_adr1"=>$AdminAddress1. ' ' .$AdminAddress2,
				"bill_ct"=>$AdminCity , 
				"bill_st"=>$AdminStateProvince, 
				"bill_co"=>$AdminCountry, 
				"bill_ph"=>'+86.' .$AdminPhone, 
				"bill_fax"=>'+86.' .$AdminPhone, 
				"bill_pc"=>$AdminPostalCode,
				"bill_em"=>$AdminEmailAddress, 
		
				"dns_ip1"=>'8.8.8.8',
				"dns_ip2"=>'8.8.8.9',
				)
			)
	);

	$result=wsdl($_params);
	$result=return_process($result);
	if($result['code']==200){
		return true;
	}
	else{
		return array('error'=>$result['msg']);
	}
}
/**
 * Initiate domain transfer.
 *
 * Attempt to create a domain transfer request for a given domain.
 *
 * This is triggered when the following events occur:
 * * Payment received for a domain transfer order
 * * When a pending domain transfer order is accepted
 * * Upon manual request by an admin user
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function bizcn_TransferDomain( $params ) {
	$username = $params["Username"];
	$password = $params["Password"];
	$platform = $params["Platform"];
	$testmode = $params["TestMode"];
	$debugmode = $params["DebugMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
	$regperiod = $params["regperiod"];
	$transfersecret = $params["transfersecret"];
	$nameserver1 = $params["ns1"];
	$nameserver2 = $params["ns2"];

	# Registrant Details
	$RegistrantFirstName = $params["firstname"];
	$RegistrantLastName = $params["lastname"];
	$RegistrantAddress1 = $params["address1"];
	$RegistrantAddress2 = $params["address2"];
	$RegistrantCity = $params["city"];
	$RegistrantStateProvince = $params["state"];
	$RegistrantPostalCode = $params["postcode"];
	$RegistrantCountry = $params["country"];
	$RegistrantEmailAddress = $params["email"];
	$RegistrantPhone = $params["phonenumber"];
	# Admin Details
	$AdminFirstName = $params["adminfirstname"];
	$AdminLastName = $params["adminlastname"];
	$AdminAddress1 = $params["adminaddress1"];
	$AdminAddress2 = $params["adminaddress2"];
	$AdminCity = $params["admincity"];
	$AdminStateProvince = $params["adminstate"];
	$AdminPostalCode = $params["adminpostcode"];
	$AdminCountry = $params["admincountry"];
	$AdminEmailAddress = $params["adminemail"];
	$AdminPhone = $params["adminphonenumber"];
	# Put your code to transfer domain here
	$_params=array(
		"username"=>$username,
		"password"=>md5($password),
		"platform"=>$platform,
		"testmode"=>$testmode,
		"tld"=>$tld,
		"sld"=>$sld,
		"regperiod"=>$regperiod,
		"transfersecret"=>$transfersecret,
		"nameserver1"=>$nameserver1,
		"nameserver2"=>$nameserver2,
		"RegistrantFirstName"=>$RegistrantFirstName,
		"RegistrantLastName"=>$RegistrantLastName,
		"RegistrantAddress1"=>$RegistrantAddress1,
		"RegistrantAddress2"=>$RegistrantAddress2,
		"RegistrantCity"=>$RegistrantCity,
		"RegistrantStateProvince"=>$RegistrantStateProvince,
		"RegistrantPostalCode"=>$RegistrantPostalCode,
		"RegistrantCountry"=>$RegistrantCountry,
		"RegistrantEmailAddress"=>$RegistrantEmailAddress,
		"RegistrantPhone"=>$RegistrantPhone,
		"AdminFirstName"=>$AdminFirstName,
		"AdminLastName"=>$AdminLastName,
		"AdminAddress1"=>$AdminAddress1,
		"AdminAddress2"=>$AdminAddress2,
		"AdminCity"=>$AdminCity,
		"AdminStateProvince"=>$AdminStateProvince,
		"AdminPostalCode"=>$AdminPostalCode,
		"AdminCountry"=>$AdminCountry,
		"AdminEmailAddress"=>$AdminEmailAddress,
		"AdminPhone"=>$AdminPhone
	);
	$result=com_71_call($_params,$testmode);
	if(empty($result))
	{
		return false;
	}
	if(substr($result,0,3)==200)
	{
		return true;
	}
	else
	{
		$error=$result;
	}
	# If error, return the error message in the value below
	$values["error"] = $error;
	return $values;
}
/**
 * Renew a domain.
 *
 * Attempt to renew/extend a domain for a given number of years.
 *
 * This is triggered when the following events occur:
 * * Payment received for a domain renewal order
 * * When a pending domain renewal order is accepted
 * * Upon manual request by an admin user
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function bizcn_RenewDomain( $params ) {
	$username = $params["Username"];
	$password = $params["Password"];
	$platform = $params["Platform"];
	$testmode = $params["TestMode"];
	$debugmode = $params["DebugMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
	$regperiod = $params["regperiod"];
	# Put your code to renew domain here
$user=array(
	'name'=>$username
	,'password'=>$password
);
	$_params=array(
		"user"=>$user,
		"platform"=>$platform,
		"testmode"=>$testmode,
		"debugmode"=>$debugmode,
		"module"=>'renewDomainService',
		'paras'=>array('renew'=>array(
			"domain"=>$sld.".".$tld,
			"term"=>$regperiod
		))
	);

	$result=wsdl($_params);
	$result=return_process($result);
	if($result['code']==200){
		return true;
	}
	else{
		return array('error'=>$result['msg']);
	}
}
/**
 * Fetch current nameservers.
 *
 * This function should return an array of nameservers for a given domain.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function bizcn_GetNameservers( $params ) {
	$username = $params["Username"];
	$password = $params["Password"];
	$platform = $params["Platform"];
	$testmode = $params["TestMode"];
	$debugmode = $params["DebugMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
	# Put your code to get the nameservers here and return the values below
	/*
	module:getdomaindns
	domainname:sld+tld
	成功返回：（最多6个）
	200 Command completed successfully
	nameserver1
	...
	nameserver6
	*/
$user=array(
	'name'=>$username
	,'password'=>$password
);
	$_params=array(
		"user"=>$user,
		"platform"=>$platform,
		"testmode"=>$testmode,
		"debugmode"=>$debugmode,
		"module"=>"infoDomainDnsService",
		'paras'=>array("domainname"=>$sld.".".$tld)
	);
	$result=wsdl($_params);
	$result=return_process($result);
	if($result['code']==200){
		$dnsinfo=$result['result']['dns_info']['dns_name'];
		for($i=0;$i<=5;$i++){
			$values["ns" . ($i+1)] = $dnsinfo[$i];
			}
		return $values;
	}
	else{
		return array('error'=>$result['msg']);
	}
}
/**
 * Save nameserver changes.
 *
 * This function should submit a change of nameservers request to the
 * domain registrar.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function bizcn_SaveNameservers( $params ) {
	$username = $params["Username"];
	$password = $params["Password"];
	$platform = $params["Platform"];
	$testmode = $params["TestMode"];
	$debugmode = $params["DebugMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
    $nameserver1 = $params["ns1"];
	$nameserver2 = $params["ns2"];
    $nameserver3 = $params["ns3"];
	$nameserver4 = $params["ns4"];
	$nameserver5 = $params["ns5"];
	$nameserver6 = $params["ns6"];
	# Put your code to save the nameservers here
	/*
	module:moddomaindns
	domainname:sld+tld
	dns_host:nameserver1
	...
	dns_host:nameserver6(有多少参数就带多少过去)
	*/


$user=array(
	'name'=>$username
	,'password'=>$password
);
	$_params=array(
		"user"=>$user,
		"platform"=>$platform,
		"testmode"=>$testmode,
		"debugmode"=>$debugmode,
		"module"=>"modDomainDnsService",
		'paras'=>array(
		'moddomaindns'=>array(
		"domainname"=>$sld.".".$tld,
		'dns_host'=>array(
		$nameserver1,
		$nameserver2,
		$nameserver3,
		$nameserver4,
		$nameserver5,
		$nameserver6,
		),
		))
	);
	$result=wsdl($_params);
	$result=return_process($result);
	if($result['code']==200){
		return true;
	}
	else{
		return array('error'=>$result['msg']);
	}
}
/**
 * Get the current WHOIS Contact Information.
 *
 * Should return a multi-level array of the contacts and name/address
 * fields that be modified.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function bizcn_GetContactDetails( $params ) {
	$username = $params["Username"];
	$password = $params["Password"];
	$platform = $params["Platform"];
	$testmode = $params["TestMode"];
	$debugmode = $params["DebugMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
	# Put your code to get WHOIS data here
	/*
	module:getcontactdetails
	domainname:sld+tld
	成功返回：
	200 Command completed successfully
	dom_fn：
	dom_ln：
	admi_fn:
	admi_ln:
	tech_fn:
	tech_ln:
	bill_fn:
	bill_ln:
	*/
$user=array(
	'name'=>$username
	,'password'=>$password
);
	$_params=array(
		"user"=>$user,
		"platform"=>$platform,
		"testmode"=>$testmode,
		"debugmode"=>$debugmode,
		"module"=>"infoDomainWhoisService",
		'paras'=>array('infoDomainWhois'=>array(
		"detailed"=>'true',
		"domainname"=>$sld.".".$tld
		))
	);
	$result=wsdl($_params);
	$result=return_process($result);
	if($result['code']==200){
		$arr_results=$result['result'];
	}
	else{
		$values["error"]=$result['msg'];
	}
	# Data should be returned in an array as follows
	$values["Registrant"]["First Name"] = $arr_results['dom_fn'];
	$values["Registrant"]["Last Name"] = $arr_results['dom_ln'];
	$values["Registrant"]["Organization"] = $arr_results['dom_org'];
	$values["Registrant"]["Address1"] = $arr_results['dom_adr1'];
	$values["Registrant"]["Address2"] = $arr_results['dom_adr2'];
	$values["Registrant"]["City"] = $arr_results['dom_ct'];
	$values["Registrant"]["State Province"] = $arr_results['dom_st'];
	$values["Registrant"]["Country"] = $arr_results['dom_co'];
	$values["Registrant"]["Phone"] = $arr_results['dom_ph'];
	$values["Registrant"]["Email Address"] = $arr_results['dom_em'];
	$values["Registrant"]["Postcode"] = $arr_results['dom_pc'];
	// end Registrant data
	$values["Admin"]["First Name"] = $arr_results['admi_fn'];
	$values["Admin"]["Last Name"] = $arr_results['admi_ln'];
	$values["Admin"]["Address1"] = $arr_results['admi_adr1'];
	$values["Admin"]["Address2"] = $arr_results['admi_adr2'];
	$values["Admin"]["City"] = $arr_results['admi_ct'];
	$values["Admin"]["State Province"] = $arr_results['admi_st'];
	$values["Admin"]["Country"] = $arr_results['admi_co'];
	$values["Admin"]["Phone"] = $arr_results['admi_ph'];
	$values["Admin"]["Email Address"] = $arr_results['admi_em'];
	$values["Admin"]["Postcode"] = $arr_results['admi_pc'];
	// end Admin data
	$values["Tech"]["First Name"] = $arr_results['tech_fn'];
	$values["Tech"]["Last Name"] = $arr_results['tech_ln'];
	$values["Tech"]["Address1"] = $arr_results['tech_adr1'];
	$values["Tech"]["Address2"] = $arr_results['tech_adr2'];
	$values["Tech"]["City"] = $arr_results['tech_ct'];
	$values["Tech"]["State Province"] = $arr_results['tech_st'];
	$values["Tech"]["Country"] = $arr_results['tech_co'];
	$values["Tech"]["Phone"] = $arr_results['tech_ph'];
	$values["Tech"]["Email Address"] = $arr_results['tech_em'];
	$values["Tech"]["Postcode"] = $arr_results['tech_pc'];
	// end Tech data
	$values["Bill"]["First Name"] = $arr_results['bill_fn'];
	$values["Bill"]["Last Name"] = $arr_results['bill_ln'];
	$values["Bill"]["Address1"] = $arr_results['bill_adr1'];
	$values["Bill"]["Address2"] = $arr_results['bill_adr2'];
	$values["Bill"]["City"] = $arr_results['bill_ct'];
	$values["Bill"]["State Province"] = $arr_results['bill_st'];
	$values["Bill"]["Country"] = $arr_results['bill_co'];
	$values["Bill"]["Phone"] = $arr_results['bill_ph'];
	$values["Bill"]["Email Address"] = $arr_results['bill_em'];
	$values["Bill"]["Postcode"] = $arr_results['bill_pc'];
	return $values;
}
/**
 * Update the WHOIS Contact Information for a given domain.
 *
 * Called when a change of WHOIS Information is requested within WHMCS.
 * Receives an array matching the format provided via the `GetContactDetails`
 * method with the values from the users input.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function bizcn_SaveContactDetails( $params ) {
	$username = $params["Username"];
	$password = $params["Password"];
	$platform = $params["Platform"];
	$testmode = $params["TestMode"];
	$debugmode = $params["DebugMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
	# Data is returned as specified in the GetContactDetails() function
	$dom_fn = $params["contactdetails"]["Registrant"]["First Name"];
	$dom_ln = $params["contactdetails"]["Registrant"]["Last Name"];
	$dom_org = $params["contactdetails"]["Registrant"]["Organization"];
	$dom_adr1 = $params["contactdetails"]["Registrant"]["Address1"];
	$dom_adr2 = $params["contactdetails"]["Registrant"]["Address2"];
	$dom_ct = $params["contactdetails"]["Registrant"]["City"];
	$dom_st = $params["contactdetails"]["Registrant"]["State Province"];
	$dom_co = $params["contactdetails"]["Registrant"]["Country"];
	$dom_ph = $params["contactdetails"]["Registrant"]["Phone"];
	$dom_em = $params["contactdetails"]["Registrant"]["Email Address"];
	$dom_pc = $params["contactdetails"]["Registrant"]["Postcode"];
	//end  registrant data
	$admi_fn = $params["contactdetails"]["Admin"]["First Name"];
	$admi_ln = $params["contactdetails"]["Admin"]["Last Name"];
	$admi_adr1 = $params["contactdetails"]["Admin"]["Address1"];
	$admi_adr2 = $params["contactdetails"]["Admin"]["Address2"];
	$admi_ct = $params["contactdetails"]["Admin"]["City"];
	$admi_st = $params["contactdetails"]["Admin"]["State Province"];
	$admi_co = $params["contactdetails"]["Admin"]["Country"];
	$admi_ph = $params["contactdetails"]["Admin"]["Phone"];
	$admi_em = $params["contactdetails"]["Admin"]["Email Address"];
	$admi_pc = $params["contactdetails"]["Admin"]["Postcode"];
	// end admin data
	$tech_fn = $params["contactdetails"]["Tech"]["First Name"];
	$tech_ln = $params["contactdetails"]["Tech"]["Last Name"];
	$tech_adr1 = $params["contactdetails"]["Tech"]["Address1"];
	$tech_adr2 = $params["contactdetails"]["Tech"]["Address2"];
	$tech_ct = $params["contactdetails"]["Tech"]["City"];
	$tech_st = $params["contactdetails"]["Tech"]["State Province"];
	$tech_co = $params["contactdetails"]["Tech"]["Country"];
	$tech_ph = $params["contactdetails"]["Tech"]["Phone"];
	$tech_em = $params["contactdetails"]["Tech"]["Email Address"];
	$tech_pc = $params["contactdetails"]["Tech"]["Postcode"];
	//end tech data
	$bill_fn = $params["contactdetails"]["Bill"]["First Name"];
	$bill_ln = $params["contactdetails"]["Bill"]["Last Name"];
	$bill_adr1 = $params["contactdetails"]["Bill"]["Address1"];
	$bill_adr2 = $params["contactdetails"]["Bill"]["Address2"];
	$bill_ct = $params["contactdetails"]["Bill"]["City"];
	$bill_st = $params["contactdetails"]["Bill"]["State Province"];
	$bill_co = $params["contactdetails"]["Bill"]["Country"];
	$bill_ph = $params["contactdetails"]["Bill"]["Phone"];
	$bill_em = $params["contactdetails"]["Bill"]["Email Address"];
	$bill_pc = $params["contactdetails"]["Bill"]["Postcode"];
	// end bill data
	if(empty($bill_fn))
	{
		$bill_fn=$admi_fn;
	}
	if(empty($bill_ln))
	{
		$bill_ln=$bill_fn;
	}

	# Put your code to save new WHOIS data here
	/*
	module:savecontactdetails
	domainname:sld+tld
	dom_fn：
	dom_ln：
	admi_fn:
	admi_ln:
	tech_fn:
	tech_ln:
	bill_fn:
	bill_ln:
	成功返回：
	200 Command completed successfully
	dom_fn：
	dom_ln：
	admi_fn:
	admi_ln:
	tech_fn:
	tech_ln:
	bill_fn:
	bill_ln:
	*/
	$domainname=$sld.".".$tld;
$user=array(
	'name'=>$username
	,'password'=>$password
);
	$_params=array(
		"user"=>$user,
		"platform"=>$platform,
		"testmode"=>$testmode,
		"debugmode"=>$debugmode,
		"module"=>"modDomainOwnerService",
		'paras'=>array(
			"modowner"=>array(
				"domainname"=>$domainname,
				"dom_adr1"=>$dom_adr1 . $dom_adr2,
				"dom_ct"=>$dom_ct, 
				"dom_st"=>$dom_st, 
				"dom_co"=>$dom_co, 
				"dom_ph"=>$dom_ph, 
				"dom_fax"=>$dom_ph, 
				"dom_pc"=>$dom_pc,
				"dom_em"=>$dom_em,)
			),
	 );
	$result=wsdl($_params);
	$result=return_process($result);
	if($result['code']==200){
		$_result='modOwn success';
	}
	else{
		$_result=$result['msg'];
		return array('error'=>$_result);
	}
	$_params=array(
		"user"=>$user,
		"platform"=>$platform,
		"testmode"=>$testmode,
		"debugmode"=>$debugmode,
		"module"=>"modDomainAdminService",
		'paras'=>array(
			"modadmin"=>array(
			"domainname"=>$domainname,	
			"admi_fn"=>$admi_fn,
			"admi_ln"=>$admi_ln, 
			"admi_adr1"=>$admi_adr1 . $admi_adr2,
			"admi_ct"=>$admi_ct , 
			"admi_st"=>$admi_st, 
			"admi_co"=>$admi_co, 
			"admi_ph"=>$admi_ph,
			"admi_fax"=>$admi_ph, 
			"admi_pc"=>$admi_pc,
			"admi_em"=>$admi_em, )
		),
	);
	$result=wsdl($_params);
	$result=return_process($result);
	if($result['code']==200){
		$_result.=',modAdmin success';
	}
	else{
		$_result.=','.$result['msg'];
		return array('error'=>$_result);
	}
	$_params=array(
		"user"=>$user,
		"platform"=>$platform,
		"testmode"=>$testmode,
		"debugmode"=>$debugmode,
		"module"=>"modDomainTechService",
		'paras'=>array(
			"modtech"=>array(
			"domainname"=>$domainname,
			"tech_fn"=>$tech_fn,
			"tech_ln"=>$tech_ln, 
			"tech_adr1"=>$tech_adr1 . ' ' . $tech_adr2,
			"tech_ct"=>$tech_ct , 
			"tech_st"=>$tech_st, 
			"tech_co"=>$tech_co, 
			"tech_ph"=>$tech_ph, 
			"tech_fax"=>$tech_ph, 
			"tech_pc"=>$tech_pc,
			"tech_em"=>$tech_em,)
		)
	 );
	$result=wsdl($_params);
	$result=return_process($result);
	if($result['code']==200){
		$_result.=',modTech success';
	}
	else{
		$_result.=','.$result['msg'];
		return array('error'=>$_result);
	}
	$_params=array(
		"user"=>$user,
		"platform"=>$platform,
		"testmode"=>$testmode,
		"debugmode"=>$debugmode,
		"module"=>"modDomainBillingService",
		'paras'=>array(
			"modbilling"=>array(
			"domainname"=>$domainname,
			"bill_fn"=>$bill_fn,
			"bill_ln"=>$bill_ln, 
			"bill_adr1"=>$bill_adr1 . ' ' . $bill_adr2,
			"bill_ct"=>$bill_ct , 
			"bill_st"=>$bill_st, 
			"bill_co"=>$bill_co, 
			"bill_ph"=>$bill_ph, 
			"bill_fax"=>$bill_ph, 
			"bill_pc"=>$bill_pc,
			"bill_em"=>$bill_em,
			)
		)
	);
	$result=wsdl($_params);
	$result=return_process($result);
	if($result['code']==200){
		$_result.=',modTech success';
	}
	else{
		$_result.=','.$result['msg'];
		return array('error'=>$_result);
	}
	return true;
}
/**
 * Check Domain Availability.
 *
 * Determine if a domain or group of domains are available for
 * registration or transfer.
 *
 * @param array $params common module parameters
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @see \WHMCS\Domains\DomainLookup\SearchResult
 * @see \WHMCS\Domains\DomainLookup\ResultsList
 *
 * @throws Exception Upon domain availability check failure.
 *
 * @return \WHMCS\Domains\DomainLookup\ResultsList An ArrayObject based collection of \WHMCS\Domains\DomainLookup\SearchResult results
 */
//TODO
function bizcn_CheckAvailability( $params ) {
	//$searchResult = new SearchResult( "testbykiyo0002", "com" );
	
	//print_r($searchResult);
	//exit;
	
	$username = $params["Username"];
	$password = $params["Password"];
	$platform = $params["Platform"];
	$testmode = $params["TestMode"];
	$debugmode = $params["DebugMode"];
	$sld = $params["sld"];
	$tlds = $params[ 'tldsToInclude'];
	!is_array($tlds) && $tlds=array($tlds);
	# Put your code to get the nameservers here and return the values below
	/*
	module:getdomaindns
	domainname:sld+tld
	成功返回：（最多6个）
	200 Command completed successfully
	nameserver1
	...
	nameserver6
	*/
	
$user=array(
	'name'=>$username,
	'password'=>$password
);
	$results = new ResultsList();
	foreach($tlds as $k=>$v){
		$_params=array(
			"user"=>$user,
			"platform"=>$platform,
			"testmode"=>$testmode,
			"debugmode"=>$debugmode,
			"module"=>"domainService",
			"method"=>"checkPremium",
			'paras'=>array(
				"domainname"=>$sld.$v,
				),
		);
		try{
			$result=wsdl($_params);
			$result=return_process($result);
			if($result['code']==200){
				$domain = $result['result']['domains']['domain'];//return domain search result

				$searchResult = new SearchResult($sld, $v);//new WHMCS searchResult Object
				if($domain['!avail']==1){
					$status = SearchResult::STATUS_NOT_REGISTERED;
				}
				else{
					$status = SearchResult::STATUS_REGISTERED;
				}
				if($domain['premium']=='true'){
					$searchResult->setPremiumDomain( true );
					$searchResult->setPremiumCostPricing(
						array(
							'register' => $domain[ 'price' ],
							'renew' => $domain[ 'price' ],
							'CurrencyCode' => 'CNY',
						)
					);
				}
				$searchResult->setStatus($status);

				$results->append($searchResult);
			}
			else{
				return array('error'=>$result['msg']);
			}
		}
		catch (\Exception $e) {
			return array(
				"error" => $e->getMessage()
			);
    	}
		
	}
	//print_r($result);
	return $results;
	exit;
	if($result['code']==200){
		$domains = $result['result']['domains'];
		for($i=0;$i<=5;$i++){
			$values["ns" . ($i+1)] = $dnsinfo[$i];
			}
		return $values;
	}
	else{
		return array('error'=>$result['msg']);
	}

	// user defined configuration values
	$userIdentifier = $params[ 'API Username' ];
	$apiKey = $params[ 'API Key' ];
	$testMode = $params[ 'Test Mode' ];
	$accountMode = $params[ 'Account Mode' ];
	$emailPreference = $params[ 'Email Preference' ];
	$additionalInfo = $params[ 'Additional Information' ];
	// availability check parameters
	$searchTerm = $params[ 'searchTerm' ];
	$punyCodeSearchTerm = $params[ 'punyCodeSearchTerm' ];
	$tldsToInclude = $params[ 'tldsToInclude' ];
	$isIdnDomain = ( bool )$params[ 'isIdnDomain' ];
	$premiumEnabled = ( bool )$params[ 'premiumEnabled' ];
	// Build post data
	$postfields = array(
		'username' => $userIdentifier,
		'password' => $apiKey,
		'testmode' => $testMode,
		'domain' => $sld . '.' . $tld,
		'searchTerm' => $searchTerm,
		'tldsToSearch' => $tldsToInclude,
		'includePremiumDomains' => $premiumEnabled,
	);
	try {
		$api = new ApiClient();
		$api->call( 'CheckAvailability', $postfields );
		$results = new ResultsList();
		foreach ( $api->getFromResponse( 'domains' ) as $domain ) {
			// Instantiate a new domain search result object
			$searchResult = new SearchResult( $domain[ 'sld' ], $domain[ 'tld' ] );
			// Determine the appropriate status to return
			if ( $domain[ 'status' ] == 'available' ) {
				$status = SearchResult::STATUS_NOT_REGISTERED;
			} elseif ( $domain[ 'statis' ] == 'registered' ) {
				$status = SearchResult::STATUS_REGISTERED;
			} elseif ( $domain[ 'statis' ] == 'reserved' ) {
				$status = SearchResult::STATUS_RESERVED;
			} else {
				$status = SearchResult::STATUS_TLD_NOT_SUPPORTED;
			}
			$searchResult->setStatus( $status );
			// Return premium information if applicable
			if ( $domain[ 'isPremiumName' ] ) {
				$searchResult->setPremiumDomain( true );
				$searchResult->setPremiumCostPricing(
					array(
						'register' => $domain[ 'premiumRegistrationPrice' ],
						'renew' => $domain[ 'premiumRenewPrice' ],
						'CurrencyCode' => 'CNY',
					)
				);
			}
			// Append to the search results list
			$results->append( $searchResult );
		}
		return $results;
	} catch ( \Exception $e ) {
		return array(
			'error' => $e->getMessage(),
		);
	}
}
/**
 * Domain Suggestion Settings.
 *
 * Defines the settings relating to domain suggestions (optional).
 * It follows the same convention as `getConfigArray`.
 *
 * @see https://developers.whmcs.com/domain-registrars/check-availability/
 *
 * @return array of Configuration Options
 */
//TODO
function bizcn_DomainSuggestionOptions() {
	return array(
		'includeCCTlds' => array(
			'FriendlyName' => 'Include Country Level TLDs',
			'Type' => 'yesno',
			'Description' => 'Tick to enable',
		),
	);
}
/**
 * Get Domain Suggestions.
 *
 * Provide domain suggestions based on the domain lookup term provided.
 *
 * @param array $params common module parameters
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @see \WHMCS\Domains\DomainLookup\SearchResult
 * @see \WHMCS\Domains\DomainLookup\ResultsList
 *
 * @throws Exception Upon domain suggestions check failure.
 *
 * @return \WHMCS\Domains\DomainLookup\ResultsList An ArrayObject based collection of \WHMCS\Domains\DomainLookup\SearchResult results
 */
//TODO
function bizcn_GetDomainSuggestions( $params ) {
	$results = new ResultsList();
	return $results;
	// user defined configuration values
	$userIdentifier = $params[ 'API Username' ];
	$apiKey = $params[ 'API Key' ];
	$testMode = $params[ 'Test Mode' ];
	$accountMode = $params[ 'Account Mode' ];
	$emailPreference = $params[ 'Email Preference' ];
	$additionalInfo = $params[ 'Additional Information' ];
	// availability check parameters
	$searchTerm = $params[ 'searchTerm' ];
	$punyCodeSearchTerm = $params[ 'punyCodeSearchTerm' ];
	$tldsToInclude = $params[ 'tldsToInclude' ];
	$isIdnDomain = ( bool )$params[ 'isIdnDomain' ];
	$premiumEnabled = ( bool )$params[ 'premiumEnabled' ];
	$suggestionSettings = $params[ 'suggestionSettings' ];
	// Build post data
	$postfields = array(
		'username' => $userIdentifier,
		'password' => $apiKey,
		'testmode' => $testMode,
		'domain' => $sld . '.' . $tld,
		'searchTerm' => $searchTerm,
		'tldsToSearch' => $tldsToInclude,
		'includePremiumDomains' => $premiumEnabled,
		'includeCCTlds' => $suggestionSettings[ 'includeCCTlds' ],
	);
	try {
		$api = new ApiClient();
		$api->call( 'GetSuggestions', $postfields );
		$results = new ResultsList();
		foreach ( $api->getFromResponse( 'domains' ) as $domain ) {
			// Instantiate a new domain search result object
			$searchResult = new SearchResult( $domain[ 'sld' ], $domain[ 'tld' ] );
			// All domain suggestions should be available to register
			$searchResult->setStatus( SearchResult::STATUS_NOT_REGISTERED );
			// Used to weight results by relevance
			$searchResult->setScore( $domain[ 'score' ] );
			// Return premium information if applicable
			if ( $domain[ 'isPremiumName' ] ) {
				$searchResult->setPremiumDomain( true );
				$searchResult->setPremiumCostPricing(
					array(
						'register' => $domain[ 'premiumRegistrationPrice' ],
						'renew' => $domain[ 'premiumRenewPrice' ],
						'CurrencyCode' => 'USD',
					)
				);
			}
			// Append to the search results list
			$results->append( $searchResult );
		}
		return $results;
	} catch ( \Exception $e ) {
		return array(
			'error' => $e->getMessage(),
		);
	}
}
/**
 * Get registrar lock status.
 *
 * Also known as Domain Lock or Transfer Lock status.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return string|array Lock status or error message
 */
function bizcn_GetRegistrarLock( $params ) {
	$username = $params["Username"];
	$password = $params["Password"];
	$platform = $params["Platform"];
	$testmode = $params["TestMode"];
	$debugmode = $params["DebugMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
	# Put your code to get the lock status here
	/*
	module:getdomainlock
	domainname:sld+tld
	成功返回：
	200 Command completed successfully
	lock:true/false
	*/
$user=array(
	'name'=>$username
	,'password'=>$password
);
	$_params=array(
		"user"=>$user,
		"platform"=>$platform,
		"testmode"=>$testmode,
		"debugmode"=>$debugmode,
		"module"=>"infoDomainLockService",
		'paras'=>array("domainname"=>$sld.".".$tld)
	);
	$result=wsdl($_params);
	$result=return_process($result);
	if($result['code']==200){
		if($result['result']['lock']=='true')
			return "locked";
		else{
			return "unlocked";
			}
	}
	else{
		return "unlocked";
	}
}
/**
 * Set registrar lock status.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function bizcn_SaveRegistrarLock( $params ) {
	$username = $params["Username"];
	$password = $params["Password"];
	$platform = $params["Platform"];
	$testmode = $params["TestMode"];
	$debugmode = $params["DebugMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
	if ($params["lockenabled"]=="locked") {
		$lockstatus="lockDomainService";
	} else {
		$lockstatus="unLockDomainService";
	}
	$domainname=$sld.".".$tld;
	# Put your code to save the registrar lock here
	/*
	锁定module:lockdomain
	解锁module:unlockdomain
	domainname:sld+tld
	*/
$user=array(
	'name'=>$username
	,'password'=>$password
);
	$_params=array(
		"user"=>$user,
		"platform"=>$platform,
		"testmode"=>$testmode,
		"debugmode"=>$debugmode,
		"module"=>$lockstatus,
		'paras'=>array("domainname"=>$sld.".".$tld)
	);
	$result=wsdl($_params);
	$result=return_process($result);
	if($result['code']==200){
		return true;
	}
	else{
		return array('error'=>$result['msg']);
	}
}
/**
 * Get DNS Records for DNS Host Record Management.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array DNS Host Records
 */
function bizcn_GetDNS( $params ) {
	$username = $params["Username"];
	$password = $params["Password"];
	$platform = $params["Platform"];
	$testmode = $params["TestMode"];
	$debugmode = $params["DebugMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
    # Put your code here to get the current DNS settings - the result should be an array of hostname, record type, and address
	/*
	module:getdnsrecord
	domainname:sld+tld
	成功的话返回：
	200 Command completed successfully
	ns1|A|192.168.0.1
	ns2|A|192.168.0.2
	*/
$user=array(
	'name'=>$username
	,'password'=>$password
);
	$_params=array(
		"user"=>$user,
		"platform"=>$platform,
		"testmode"=>$testmode,
		"debugmode"=>$debugmode,
		"module"=>'infoDnsRecordService',
		'paras'=>array("domainname"=>$sld.".".$tld)
	);
	$result=wsdl($_params);
	$result=return_process($result);
	if($result['code']==200){
		if(is_array($result['result']['records']['record'])){
			$hostrecords = array();
			if(isset($result['result']['records']['record']['type'])){
				$result['result']['records']['record'] = array($result['result']['records']['record']);
			}
				foreach($result['result']['records']['record'] as $k => $v)
				{
					$hostrecords[]=array(
						"hostname"=>$v['host'],
						"type"=>$v['type'],
						"address"=>$v['value'],
						"priority"=>$v['mxlevel'],
					);
				}
		}
		else{
			return NULL;
			}
		return $hostrecords;
		
	}
	else{
		return array('error'=>$result['msg']);
	}
}
/**
 * Update DNS Host Records.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function bizcn_SaveDNS( $params ) {
	$username = $params["Username"];
	$password = $params["Password"];
	$platform = $params["Platform"];
	$testmode = $params["TestMode"];
	$debugmode = $params["DebugMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
	$dnsData=$params["dnsrecords"];
	
	$dnsRecords = array();
	foreach($dnsData as $k => $v){
		if($v['address']){
			$dnsRecords[]=array(
			'host'=>$v['hostname'],
			'type'=>$v['type'],
			'value'=>$v['address'],
			'mxlevel'=>intval($v['priority']),
			);
		}
	}
	$user=array(
		'name'=>$username,
		'password'=>$password);
if(count($dnsRecords)>0){
	$_params=array(
		"user"=>$user,
		"platform"=>$platform,
		"testmode"=>$testmode,
		"debugmode"=>$debugmode,
		"module"=>"updateDnszoneService",
		"paras"=>array(
			"update"=>array(
				"domainname"=>$sld.".".$tld,
				"record"=>$dnsRecords,
				)
			)
	);
}
else{
	$_params=array(
		"user"=>$user,
		"platform"=>$platform,
		"testmode"=>$testmode,
		"debugmode"=>$debugmode,
		"module"=>"delDnszoneServiceImpl",
		"paras"=>array(
				"domainname"=>$sld.".".$tld
			)
	);
}
	
	$result=wsdl($_params);
	$result=return_process($result);
	if($result['code']==200){
		return true;
	}
	else{
		return array('error'=>$result['msg']);
	}
}
/**
 * Enable/Disable ID Protection.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
//TODO
function bizcn_IDProtectToggle( $params ) {
	$username = $params["Username"];
	$password = $params["Password"];
	$platform = $params["Platform"];
	$testmode = $params["TestMode"];
	$debugmode = $params["DebugMode"];
	// domain parameters
	$sld = $params[ 'sld' ];
	$tld = $params[ 'tld' ];
	// id protection parameter
	$protectEnable = ( bool )$params[ 'protectenable' ];
$user=array(
	'name'=>$username
	,'password'=>$password
);
	$_params=array(
		"user"=>$user,
		"platform"=>$platform,
		"testmode"=>$testmode,
		"debugmode"=>$debugmode,
		"sld"=>$sld,
		"tld"=>$tld,
		"module"=>"domainService",
		'paras'=>array("domainname"=>$sld.".".$tld)
	);
	
	if ( $protectEnable ) {
		$_params["method"]="whoisProtectStart";
	} else {
		$_params["method"]="whoisProtectStop";
	}
	$result=wsdl($_params);
	$result=return_process($result);
	if($result['code']==200){
		$values["eppcode"] = $result['msg'];
	}
	else{
		return array('error'=>$result['msg']);
	}
    # If error, return the error message in the value below
    $values["error"] = $error;
    return $values;
}
/**
 * Request EEP Code.
 *
 * Supports both displaying the EPP Code directly to a user or indicating
 * that the EPP Code will be emailed to the registrant.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 *
 */
function bizcn_GetEPPCode( $params ) {
	$username = $params["Username"];
	$password = $params["Password"];
	$platform = $params["Platform"];
	$testmode = $params["TestMode"];
	$debugmode = $params["DebugMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];

$user=array(
	'name'=>$username
	,'password'=>$password
);
	$_params=array(
		"user"=>$user,
		"platform"=>$platform,
		"testmode"=>$testmode,
		"debugmode"=>$debugmode,
		"tld"=>$tld,
		"sld"=>$sld,
		"module"=>"getEppcodeServiceImpl",
		'paras'=>array("domainname"=>$sld.".".$tld)
	);
	$result=wsdl($_params);
	$result=return_process($result);
	if($result['code']==200){
		$values["eppcode"] = $result['msg'];
	}
	else{
		return array('error'=>$result['msg']);
	}
    # If error, return the error message in the value below
    $values["error"] = $error;
    return $values;
}
/**
 * Release a Domain.
 *
 * Used to initiate a transfer out such as an IPSTAG change for .UK
 * domain names.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function bizcn_ReleaseDomain( $params ) {
	return array('error'=>'features not supported');
}
/**
 * Delete Domain. (need unlock first)
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function bizcn_RequestDelete( $params ) {
	$username = $params["Username"];
	$password = $params["Password"];
	$platform = $params["Platform"];
	$testmode = $params["TestMode"];
	$debugmode = $params["DebugMode"];
	$sld = $params["sld"];
	$tld = $params["tld"];
	# Put your code to renew domain here
$user=array(
	'name'=>$username
	,'password'=>$password
);
	$_params=array(
		"user"=>$user,
		"platform"=>$platform,
		"testmode"=>$testmode,
		"debugmode"=>$debugmode,
		"module"=>'domainService',
		"method"=>'deldomain',
		'paras'=>array(
			"domainname"=>$sld.".".$tld,
		)
	);

	$result=wsdl($_params);
	$result=return_process($result);
	if($result['code']==200){
		return true;
	}
	else{
		return array('error'=>$result['msg']);
	}
}
/**
 * Register a Nameserver.
 *
 * Adds a child nameserver for the given domain name.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function bizcn_RegisterNameserver( $params ) {
	$username = $params["Username"];
	$password = $params["Password"];
	$platform = $params["Platform"];
	$testmode = $params["TestMode"];
	$debugmode = $params["DebugMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
    $nameserver = $params["nameserver"];
    $ipaddress = $params["ipaddress"];
    # Put your code to register the nameserver here
	/*
	module:createnameserver
	hostname:
	ip:
	*/
$user=array(
	'name'=>$username
	,'password'=>$password
);
	$_params=array(
		"user"=>$user,
		"platform"=>$platform,
		"testmode"=>$testmode,
		"debugmode"=>$debugmode,
		"module"=>"addNameserverService",
		'paras'=>array(
		'addnameserver'=>array(
			"hostname"=>$nameserver,
			"ip"=>$ipaddress,
			)
		)
	);
	$result=wsdl($_params);
	$result=return_process($result);
	if($result['code']==200){
		return true;
		
	}
	else{
		return array('error'=>$result['msg']);
	}
}
/**
 * Modify a Nameserver.
 *
 * Modifies the IP of a child nameserver.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function bizcn_ModifyNameserver( $params ) {
	$username = $params["Username"];
	$password = $params["Password"];
	$platform = $params["Platform"];
	$testmode = $params["TestMode"];
	$debugmode = $params["DebugMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
    $nameserver = $params["nameserver"];
    $currentipaddress = $params["currentipaddress"];
    $newipaddress = $params["newipaddress"];
    # Put your code to update the nameserver here
	/*
	module:modnameserver
	hostname:
	oldip：
	newip:
	*/
$user=array(
	'name'=>$username
	,'password'=>$password
);
	$_params=array(
		"user"=>$user,
		"platform"=>$platform,
		"testmode"=>$testmode,
		"debugmode"=>$debugmode,
		"tld"=>$tld,
		"sld"=>$sld,
		"module"=>"modNameserverService",
		"paras"=>array(
			'modnameserver'=>array(
				"hostname"=>$nameserver,
				"oldip"=>$currentipaddress,
				"newip"=>$newipaddress
				)
		),
	);
	$result=wsdl($_params);
	$result=return_process($result);
	if($result['code']==200){
		return true;
		
	}
	else{
		return array('error'=>$result['msg']);
	}
}
/**
 * Delete a Nameserver.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function bizcn_DeleteNameserver( $params ) {
	$username = $params["Username"];
	$password = $params["Password"];
	$platform = $params["Platform"];
	$testmode = $params["TestMode"];
	$debugmode = $params["DebugMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
    $nameserver = $params["nameserver"];
    # Put your code to delete the nameserver here

$user=array(
	'name'=>$username
	,'password'=>$password
);
	$_params=array(
		"user"=>$user,
		"platform"=>$platform,
		"testmode"=>$testmode,
		"debugmode"=>$debugmode,
		"tld"=>$tld,
		"sld"=>$sld,
		"module"=>"delNameserverService",
		"paras"=>array(
			"hostname"=>$nameserver
			),
	);
	$result=wsdl($_params);
	$result=return_process($result);
	if($result['code']==200){
		return true;
		
	}
	else{
		return array('error'=>$result['msg']);
	}
}
/**
 * Sync Domain Status & Expiration Date.
 *
 * Domain syncing is intended to ensure domain status and expiry date
 * changes made directly at the domain registrar are synced to WHMCS.
 * It is called periodically for a domain.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
//TODO
function bizcn_Sync( $params ) {
	$username = $params["Username"];
	$password = $params["Password"];
	$platform = $params["Platform"];
	$testmode = $params["TestMode"];
	$debugmode = $params["DebugMode"];
	$sld = $params["sld"];
	$tld = $params["tld"];
	# Put your code to renew domain here
$user=array(
	'name'=>$username
	,'password'=>$password
);
	$_params=array(
		"user"=>$user,
		"platform"=>$platform,
		"testmode"=>$testmode,
		"debugmode"=>$debugmode,
		"module"=>'domainService',
		"method"=>'deldomain',
		'paras'=>array(
			"domainname"=>$sld.".".$tld,
		)
	);

	$result=wsdl($_params);
	$result=return_process($result);
	if($result['code']==200){
		return array(
			'expirydate' => $result['expirydate'], // Format: YYYY-MM-DD
			'active' => ( bool )$result['active'], // Return true if the domain is active
			'expired' => ( bool )$result['expired'], // Return true if the domain has expired
			'transferredAway' => ( bool )$result['transferredaway'], // Return true if the domain is transferred out 
		);
	}
	else{
		return array('error'=>$result['msg']);
	}
}
/**
 * Incoming Domain Transfer Sync.
 *
 * Check status of incoming domain transfers and notify end-user upon
 * completion. This function is called daily for incoming domains.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
//TODO
function bizcn_TransferSync( $params ) {
	// user defined configuration values
	$userIdentifier = $params[ 'API Username' ];
	$apiKey = $params[ 'API Key' ];
	$testMode = $params[ 'Test Mode' ];
	$accountMode = $params[ 'Account Mode' ];
	$emailPreference = $params[ 'Email Preference' ];
	$additionalInfo = $params[ 'Additional Information' ];
	// domain parameters
	$sld = $params[ 'sld' ];
	$tld = $params[ 'tld' ];
	// Build post data
	$postfields = array(
		'username' => $userIdentifier,
		'password' => $apiKey,
		'testmode' => $testMode,
		'domain' => $sld . '.' . $tld,
	);
	try {
		$api = new ApiClient();
		$api->call( 'CheckDomainTransfer', $postfields );
		if ( $api->getFromResponse( 'transfercomplete' ) ) {
			return array(
				'completed' => true,
				'expirydate' => $api->getFromResponse( 'expirydate' ), // Format: YYYY-MM-DD
			);
		} elseif ( $api->getFromResponse( 'transferfailed' ) ) {
			return array(
				'failed' => true,
				'reason' => $api->getFromResponse( 'failurereason' ), // Reason for the transfer failure if available
			);
		} else {
			// No status change, return empty array
			return array();
		}
	} catch ( \Exception $e ) {
		return array(
			'error' => $e->getMessage(),
		);
	}
}
/**
 * Client Area Custom Button Array.
 *
 * Allows you to define additional actions your module supports.
 * In this example, we register a Push Domain action which triggers
 * the `registrarmodule_push` function when invoked.
 *
 * @return array
 */
//TODO
function bizcn_ClientAreaCustomButtonArray() {
	return array(
		//'Push Domain' => 'push',
	);
}
/**
 * Client Area Allowed Functions.
 *
 * Only the functions defined within this function or the Client Area
 * Custom Button Array can be invoked by client level users.
 *
 * @return array
 */
//TODO
function bizcn_ClientAreaAllowedFunctions() {
	return array(
		//'Push Domain' => 'push',
	);
}
/**
 * Example Custom Module Function: Push
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
//TODO
function bizcn_push( $params ) {
	// user defined configuration values
	$userIdentifier = $params[ 'API Username' ];
	$apiKey = $params[ 'API Key' ];
	$testMode = $params[ 'Test Mode' ];
	$accountMode = $params[ 'Account Mode' ];
	$emailPreference = $params[ 'Email Preference' ];
	$additionalInfo = $params[ 'Additional Information' ];
	// domain parameters
	$sld = $params[ 'sld' ];
	$tld = $params[ 'tld' ];
	// Perform custom action here...
	return 'Not implemented';
}
/**
 * Client Area Output.
 *
 * This function renders output to the domain details interface within
 * the client area. The return should be the HTML to be output.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return string HTML Output
 */
//TODO
function bizcn_ClientArea( $params ) {
	$output = '';
	return $output;
}

function wsdl($parameters){
if(isset($parameters['module'])){$module = $parameters['module'];}else{$module = '';}
if(isset($parameters['method'])){$method = $parameters['method'];}else{$method = '';}
if(isset($parameters['testmode'])){$testmode = ($parameters['testmode']=='on'?true:false);}else{$testmode = false;}
if(isset($parameters['debugmode'])){$debugmode = ($parameters['debugmode']=='on'?true:false);}else{$debugmode = false;}
if(isset($parameters['platform'])){$platform = ($parameters['platform']=='cnobin'?'cnobin':'bizcn');}else{$platform = 'bizcn';}
#print_r($parameters);
if($testmode==true){
	if($platform=='cnobin'){define('WSDL_URL','https://test.cnobin.com/rrpservices/');}
	else{define('WSDL_URL','https://test.bizcn.com/rrpservices/');}
	}
else{
	if($platform=='cnobin'){define('WSDL_URL','https://www.cnobin.com/rrpservices/');}
	else{define('WSDL_URL','https://www.bizcn.com/rrpservices/');}
}
#print_r($parameters);
#echo '<br>'. "\n";
if($module && !$method){
	switch($module){
		case 'checkDomainsService':#Check-Domain
			$method = 'checkDomains';
			break;
		case 'addDomainService':#Add Domain
			$method = 'addDomain';
			break;
		case 'renewDomainService':#renew domain
			$method = 'renewDomain';
			break;
		case 'modDomainOwnerService':#modify domain owner
			$method = 'modOwner';
			break;
		case 'modDomainAdminService':#modify administrator of domain
			$method = 'modAdmin';
			break;
		case 'modDomainTechService':#modify domain tech
			$method = 'modTech';
			break;
		case 'modDomainBillingService':#modify billing of domain
			$method = 'modBilling';
			break;
		case 'modDomainPasswdService':#modify domain pass
			$method = 'modPasswd';
			break;
		case 'lockDomainService':#lock domain
			$method = 'lockDomain';
			break;
		case 'unLockDomainService':#unlock domain
			$method = 'unLockDomain';
			break;
		case 'addNameserverService':#add NS
			$method = 'addNameserver';
			break;
		case 'modNameserverService':#Mod NS
			$method = 'modNameserver';
			break;
		case 'delNameserverService':#Del NS
			$method = 'delNameserver';
			break;
		case 'infoDomainWhoisService':#Get Domain info
			$method = 'infoDomainWhois';
			break;
		case 'addDnsDomainService':#Order DNS service
			$method = 'addDnsDomain';
			break;
		case 'checkDomainExistService':#Check is Domain in Our database
			$method = 'checkDomainExist';
			break;
		case 'addDnsRecordService':#Add DNS record
			$method = 'addDnsRecord';
			break;
		case 'modDnsRecordService':#Mod DNS record
			$method = 'modDnsRecord';
			break;
		case 'delDnsRecordService':#Del DNS record
			$method = 'delDnsRecord';
			break;
		case 'delDnszoneServiceImpl':#Del DNS Zone
			$method = 'delDnszone';
			break;
		case 'refreshDnszoneService':#Refresh DNS record
			$method = 'refreshDnszone';
			break;
		case 'addUrlForwardService':#Add URL forward
			$method = 'addUrlForward';
			break;
		case 'modUrlForwardService':#Mod URL forward
			$method = 'modUrlForward';
			break;
		case 'delUrlForwardService':#Del URL forward
			$method = 'delUrlForward';
			break;
		case 'infoDomainDnsService':#Get NS
			$method = 'infoDomainDns';
			break;
		case 'infoDomainLockService':#Get is Domain lock
			$method = 'infoDomainLock';
			break;
		case 'infoDnsRecordService':#Get DNS record
			$method = 'infoDnsRecord';
			break;
		case 'updateDnszoneService':#Update DNS record
			$method = 'updateDnszone';
			break;
		case 'modDomainDnsService':#MOD NS server
			$method = 'modDomainDns';
			break;
		case 'getEppcodeServiceImpl':#Get Epp ocde
			$method = 'getEppcode';
			break;
		default:
			$values['code'] = 500;
			$values["msg"] = 'No specified method';
			return $values;
		break;
	}
}
elseif(!$module){
	$values['code'] = 500;
	$values["msg"] = 'No specified method';
	return $values;
}

$client = new bizcnsoap_client(WSDL_URL.$module.'?wsdl', true);
$client->soap_defencoding = 'UTF-8';
$client->decode_utf8 = false;
$err = $client->getError();
if ($err) {
	if(DEBUG==true || $debugmode == true){echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';}
	else{
		# If error, return the error message in the value below
		$values["msg"] = $err;
		return $values;	
	}
}
$client->setUseCurl(0);
#if(is_array($parameters)){$parameters['user']=$user;}
$user=$parameters['user'];
$paras=$parameters['paras'];
$paras['user']=$user;
$result = $client->call($method,$paras);
if ($client->fault) {
	if(DEBUG==true || $debugmode == true){
		echo '<h2>Fault</h2><pre>';
		print_r($result);
		echo '</pre>';}
		echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
		echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
		echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->debug_str, ENT_QUOTES) . '</pre>';
		# If error, return the error message in the value below
		$values["code"] = 500;
		$values["msg"] = 'API Fault';
		return $values;	
} else {
	$err = $client->getError();
	if ($err) {
	if(DEBUG==true || $debugmode == true){
		echo '<h2>Error</h2><pre>' . $err . '</pre>';
		}
		# If error, return the error message in the value below
		$values["msg"] = $err;
		return $values;
	} else {
	if(DEBUG==true || $debugmode == true){
		echo '<h2>Result</h2><pre>';
		print_r($result);
		echo '</pre>';
		}
if(DEBUG==true || $debugmode == true){
	echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
	echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
	echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->debug_str, ENT_QUOTES) . '</pre>';
	}
		# If success, return the error message in the value below
		if(isset($result['response']) && sizeof($result['response'])>1){
			$values['code'] = $result['response']['result']['!code'];
			$values['msg'] = $result['response']['result']['msg'];
			$values['result'] = array_slice($result['response'],0);
			}
		else{
			$values['code'] = 500 ;
			$values['msg'] = 'unknown error';
			if(isset($result['response']['result'])){
			$values['code'] = $result['response']['result']['!code'];
			$values['msg'] = $result['response']['result']['msg'];
				}
			if(isset($result['result'])){
			$values['code'] = $result['result']['!code'];
			$values['msg'] = $result['result']['msg'];}
		}
		return $values;
	}
}
}

function return_process($result){
	if(empty($result))
	{
		$error="return is null";
		# If error, return the error message in the value below
		$values["msg"] = $error;
		$values["code"] = 500;
		return $values;
	}
	else{
		return $result;
	}
}
