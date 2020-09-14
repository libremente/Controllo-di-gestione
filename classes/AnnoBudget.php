<?php
class AnnoBudget {
    public $id;
    public $descrizione;
    public $attivo;

    public function __construct($id = null) {
        if ($id !== null) {
            $db = ffDb_Sql::factory();

            $sql = "
                SELECT anno_budget.*
                FROM anno_budget
                WHERE anno_budget.ID = " . $db->toSql($id)
            ;
            $db->query($sql);
            if ($db->nextRecord()) {
                $this->id = $db->getField("ID", "Number", true);
                $this->descrizione = $db->getField("descrizione", "Text", true);
                if ($db->getField("attivo", "Text", true) == 1)
                    $this->attivo = 1;
                else
                    $this->attivo = 0;
            } else
                throw new Exception("Impossibile creare l'oggetto AnnoBudget con ID = " . $id);
        }
    }

    //restituisce array con tutti gli anni di budget ordinati in maniera decrescente
    public static function getAll() {
        $anni = array();

        $db = ffDB_Sql::factory();
        $sql = "SELECT anno_budget.*
                FROM anno_budget
                ORDER BY descrizione DESC";
        $db->query($sql);
        if ($db->nextRecord()) {
            do {
                $anno = new AnnoBudget();
                $anno->id = $db->getField("ID", "Number", true);
                $anno->descrizione = $db->getField("descrizione", "Text", true);
                if ($db->getField("attivo", "Text", true) == 1)
                    $anno->attivo = 1;
                else
                    $anno->attivo = 0;
                $anni[] = $anno;
            }while ($db->nextRecord());
        }
        return $anni;
    }

    //restituisce l'ultimo anno definito
    public static function ultimoDefinito() {
        $anni_budget = AnnoBudget::getAll();
        //viene estratto l'ultimo anno attivo definito (il primo degli anni estratti con GetAll())
        foreach ($anni_budget AS $anno) {
            if ($anno->attivo == 1)
                return $anno;
        }
        return null;
    }

    //restituisce l'ultimo anno attivo alla data
    public static function ultimoAttivoInData($date = null) {
        //nel caso non sia passata la data viene utilizzata quella corrente
        if ($date == null)
            $anno_cercato = date("Y");
        else
            $anno_cercato = date("Y", strtotime($date));

        foreach (AnnoBudget::getAll() as $anno) {
            //essendo gli anni ordinati in ordine crescente viene considerato il primo minore o uguale a quello ricercato
            if ($anno->attivo == 1 && $anno->descrizione <= $anno_cercato) {
                try {
                    return new AnnoBudget($anno->id);
                } catch (Exception $ex) {
                    ffErrorHandler::raise($ex->getMessage());
                }
            }
        }
        return null;
    }

    public function getAttivoPrecedente() {
        $end = false;
        //vengono ciclati tutti gli anni di budget
        //NB getAll restituisce gli anni con ordinamento decrescente       
        foreach ($this->getAll() as $anno) {
            //nel caso in cui l'id dell'anno corrisponda a quello ricercato
            //viene restituito l'anno successivo (nel caso esista)
            if ($end == true) {
                if ($anno->attivo == 1) {
                    try {
                        return new AnnoBudget($anno->id);
                    } catch (Exception $ex) {
                        ffErrorHandler::raise($ex->getMessage());
                    }
                }
            } else if ($end == false && $anno->id == $this->id)
                $end = true;
        }
        //nel caso non siano stati trovati anni precedenti viene restiutito null
        return null;
    }

    //viene restituito un array dei piani attivi (con un piano con almeno un cdr di responsabilità dell'utente definito per l'anno)
    //TODO implementare controllo su cdr di responsabilità e di afferenza - invece di getAll utilizzare cdr di responsabilità dell'utentenell'anno
    public function getTipiPianoCdrAttiviUtente() {
        $tipi_piani_cdr_anno = array();
        $tipi_piano_cdr = TipoPianoCdr::getAll();
        foreach ($tipi_piano_cdr as $tipo_piano) {
            $piani = $this->getPianiCdr($tipo_piano);
            //se almeno uno dei piani del tipo verificato ha almeno un cdr viene restituito nell'array
            foreach ($piani as $piano) {
                if (count($piano->getCdr()) > 0) {
                    $tipi_piani_cdr_anno[] = $tipo_piano;
                }
            }
        }
        return $tipi_piani_cdr_anno;
    }

    public function getUltimoPianoIntrodottoAnniPrecedenti(TipoPianoCdr $tipo_piano = null) {
        $piani_cdr_prec = array();
        if ($tipo_piano == null) {
            $filters = array();
        } else {
            $filters = array("ID_tipo_piano_cdr" => $tipo_piano->id);
        }

        //vengono selezionati tutti i piani_cdr introdotti precedenti all'anno corrente
        foreach (PianoCdr::getAll($filters) as $piano) {
            if ($piano->data_introduzione !== null && strtotime($piano->data_introduzione) < strtotime(date($this->descrizione . "-01-01")))
                $piani_cdr_prec[] = $piano;
        }
        //se è stato trovato almeno un piano viene restituito il più recente (primo dell'array perchè getAll restituisce con ordinamento DESC)
        if (count($piani_cdr_prec) > 0)
            return $piani_cdr_prec[0];
        else
            return null;
    }

    //restituisce un array con i piani cdr che interessano l'anno, dal più recente al meno recente
    //array vuoto se nessun piano per l'anno
    public function getPianiCdr(TipoPianoCdr $tipo_piano = null) {
        $piani_cdr_anno = array();

        //il piano sarà attivo se data d'introduzione è compresa nell'anno
        //oppure se è l'ultimo piano definito prima del primo giorno dell'anno (compreso)
        if ($tipo_piano !== null)
            $filters = array("ID_tipo_piano_cdr" => $tipo_piano->id);
        else
            $filters = array();
        foreach (PianoCdr::getAll($filters) as $piano) {
            //se il piano ha data introduzione nell'anno
            if (
                $piano->data_introduzione !== null &&
                strtotime($piano->data_introduzione) >= strtotime(date($this->descrizione . "-01-01")) &&
                strtotime($piano->data_introduzione) <= strtotime(date($this->descrizione . "-12-31"))
            ) {
                $piani_cdr_anno[] = $piano;
            }
        }
        //se per l'anno corrente non sono stati trovati piani viene considerato l'ultimo definito
        if (count($piani_cdr_anno) == 0) {
            $ultimo_piano_anni_prec = $this->getUltimoPianoIntrodottoAnniPrecedenti($tipo_piano);
            if ($ultimo_piano_anni_prec != null)
                $piani_cdr_anno[] = $ultimo_piano_anni_prec;
        }
        return $piani_cdr_anno;
    }

}
