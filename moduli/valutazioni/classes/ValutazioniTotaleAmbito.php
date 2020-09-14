<?php
class ValutazioniTotaleAmbito extends Entity {
    protected static $tablename = "valutazioni_totale_ambito";

    public static function factoryFromTotaleAmbito($id_totale, $id_ambito) {
        $db = ffDb_Sql::factory();
        $sql = "
            SELECT * FROM valutazioni_totale_ambito
            WHERE 
              valutazioni_totale_ambito.ID_totale = ".$db->toSql($id_totale)." AND
              valutazioni_totale_ambito.ID_ambito = ".$db->toSql($id_ambito)."
        ";

        $db->query($sql);

        if ($db->nextRecord()) {
            do {
                $totale_ambito = new ValutazioniTotaleAmbito();
                $totale_ambito->id = $db->getField("ID", "Number", true);
                $totale_ambito->id_totale = $db->getField("ID_totale", "Number", true);
                $totale_ambito->id_ambito = $db->getField("ID_ambito", "Number", true);

                return $totale_ambito;
            } while ($db->nextRecord());
        }
        throw new Exception("Impossibile creare l'oggetto ValutazioniTotaleAmbito con id_totale = " . $id_totale . " e id_ambito = " . $id_ambito);
    }

    public function insert() {
        $db = ffDb_Sql::factory();
        $sql = "
                INSERT INTO valutazioni_totale_ambito(
                    ID_totale, 
                    ID_ambito
                )
                VALUES (
                    ".$db->toSql($this->id_totale).", 
                    ".$db->toSql($this->id_ambito)."
                )
            ";

        if ($db->execute($sql)){
            return $db->getInsertID(true);
        }
        throw new Exception("Impossibile inserire l'oggetto ValutazioniTotaleAmbito nel DB.");
    }

    public function canDelete() {
        $ambito = new ValutazioniAmbito($this->id_ambito);
        if(!$ambito->canDelete()) {
            return false;
        }
        return true;
    }

    public function delete() {
        if($this->canDelete()) {
            $db = ffDb_Sql::factory();
            $sql = "
                DELETE FROM valutazioni_totale_ambito
                WHERE valutazioni_totale_ambito.ID = ".$db->toSql($this->id)."
            ";

            if (!$db->execute($sql)) {
                throw new Exception("Impossibile eliminare l'oggetto ValutazioniTotaleAmbito con ID='" . $this->id . "' dal DB");
            }
            return true;
        }
        return false;
    }
}