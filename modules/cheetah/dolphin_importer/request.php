<?php
/***************************************************************************
* Date Released		: December 8, 2020
* Last Updated		: December 8, 2020
*
* Copywrite			: (c) 2020 by Dean J. Bassett Jr.
* Website			: https://www.cheetahwsb.com
*
* Product Name		: Dolphin Importer
* Product Version	: 1.0.0
*
* IMPORTANT: This is a commercial product made by Dean J. Bassett Jr.
* and cannot be modified other than personal use.
*
* This product cannot be redistributed for free or a fee without written
* permission from Dean J. Bassett Jr.
*
* You may use the product on one dolphin website only. You need to purchase
* additional copies if you intend to use this on other websites.
***************************************************************************/

require_once(CH_DIRECTORY_PATH_INC . 'profiles.inc.php');

check_logged();

ch_import('ChWsbRequest');
ChWsbRequest::processAsAction($GLOBALS['aModule'], $GLOBALS['aRequest']);
