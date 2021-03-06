<?php
$user = LoggedUser::Instance();
if (isset ($_REQUEST["keys[ID]"])) {
    try {      
        $progetto = new ProgettiProgetto($_REQUEST["keys[ID]"]);
    }
    catch (Exception $ex){
        ffErrorHandler::raise("Errore nel passaggio dei parametri");
    }
}

$oRecord = ffRecord::factory($cm->oPage);
$oRecord->id = "progetto-indicatore";
$oRecord->title = "Indicatore";
$oRecord->resources[] = "progetto-indicatore";
$oRecord->src_table  = "progetti_progetto_indicatore";

$oField = ffField::factory($cm->oPage);
$oField->id = "ID_progetti_progetto_indicatore";
$oField->base_type = "Number";
$oField->data_source = "ID";
$oRecord->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "descrizione";
$oField->base_type = "Text";
$oField->label = "Descrizione";
$oField->required = true;
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "valore_atteso";
$oField->base_type = "Text";
$oField->label = "Valore atteso";
$oField->required = true;
$oRecord->addContent($oField);

$oRecord->insert_additional_fields["ID_progetto"] = new ffData($progetto->id, "Number");
$oRecord->insert_additional_fields["time_modifica"] = new ffData(date("Y-m-d H:i:s"), "Datetime");
$oRecord->insert_additional_fields["record_attivo"] = new ffData(1, "Number");

$cm->oPage->addContent($oRecord);