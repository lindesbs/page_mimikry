<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');
 
// add pdf to supported template file extensions
$GLOBALS['TL_CONFIG']['templateFiles'] .= ',pdf';

$GLOBALS['TL_HOOKS']['generatePage'][] = array('PageMimikry', 'mimikryGeneratePage');
