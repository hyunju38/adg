<?php
namespace tests;

require_once dirname(__FILE__).'/../vendor/autoload.php';

function ajaxSpy()
{
    $spyCurlExec = new \phpmock\spy\Spy('boilerplate', "curl_exec", function(){
        return 'OK';
    });
    $spyCurlExec->enable();

    $spyCurlGetinfo = new \phpmock\spy\Spy('boilerplate', "curl_getinfo", function(){
        return 200;
    });
    $spyCurlGetinfo->enable();
}
ajaxSpy();
