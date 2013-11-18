<?php

$extensionClassPath = t3lib_extMgm::extPath('geckoboard') . 'Classes/';
return array(
	'tx_geckoboard_task_geckopusher' => $extensionClassPath . 'Task/GeckoPusher.php',
	'tx_geckoboard_task_geckopusheradditionalfields' => $extensionClassPath . 'Task/GeckoPusherAdditionalFields.php',
);

?>