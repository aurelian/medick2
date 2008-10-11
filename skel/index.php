<?php
// $Id: index.php 379 2006-03-18 17:36:03Z aurelian $

// 
// This file is part of cFields project
// auto-generated on 2008 Mar 11 16:52:31 with medick version: 0.4.1
// 

// complete path to medick boot.php file.
include_once('/W/Devel/medick/exp/medick2/boot.php');

// complete path to cFields.xml
// and environment to load
$d= new Dispatcher(
          ContextManager::load(
            '/W/Devel/medick/exp/medick2/config/cfields.xml',
            'localhost')
        );
$d->dispatch();

