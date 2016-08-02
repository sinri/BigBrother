<?php
/**
Big Brother System - The Ministry of Love
================================================================
"War is peace", "Freedom is slavery" and "Ignorance is strength"

Index, make the history
*/
date_default_timezone_set("Asia/Shanghai"); 

require_once('SinriPDO.php');

require_once('bb_love.php');
require_once('bb_peace.php');
require_once('bb_plenty.php');
require_once('bb_truth.php');

echo "This server is " . BigBrotherPeace::getConfig('client_name').PHP_EOL;

