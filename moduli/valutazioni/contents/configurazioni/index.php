<?php
CoreHelper::includeJqueryUi();

$modulo = Modulo::getCurrentModule();

//viene caricato il template specifico per la pagina
$tpl = ffTemplate::factory($modulo->module_theme_dir . DIRECTORY_SEPARATOR . "tpl");
$tpl->load_file("configurazioni.html", "main");

$tpl->set_var("globals", $cm->oPage->get_globals(GET_GLOBALS_EXCLUDE_LIST));

$tpl->set_var("active_tab", isset($_REQUEST["gotab"]) ? $_REQUEST["gotab"] : 0);

//***********************
//Adding contents to page
$cm->oPage->addContent($tpl);