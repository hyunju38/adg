<?php

use yellotravel\asl\airportsDataGenerator;
use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\CliMenuBuilder;

require 'vendor/autoload.php';

$art = <<<ART
                 /\
                |{}|
         _______/^^\______
        /       |  |       \
       `====----.  .----====`
                ||||
                 ||
              ,--||--,
              '--<>--'
             Yello Travel
     GENERATING DATA FOR API SERVER!
ART;

$menu = (new CliMenuBuilder)
    ->addAsciiArt($art)
    ->addLineBreak('-')
    ->setTitle('Data Generator')
    ->addItem('Airports data generate!', function(){
        airportsDataGenerator::generate();
    })
    ->addLineBreak('-')
    ->setForegroundColour('yellow')
    ->setBackgroundColour('black')
    ->build();

$menu->open();
