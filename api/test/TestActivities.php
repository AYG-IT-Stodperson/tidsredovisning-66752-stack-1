<?php

declare (strict_types=1);
require_once '../src/Activities.php';

/**
 * Funktion för att testa alla aktiviteter
 * @return string html-sträng med resultatet av alla tester
 */
function allaActivityTester(): string {
    // Kom ihåg att lägga till alla funktioner i filen!
    $retur = "";
    $retur .= test_HamtaAllaAktiviteter();
    $retur .= test_HamtaEnAktivitet();
    $retur .= test_SparaNyAktivitet();
    $retur .= test_UppdateraAktivitet();
    $retur .= test_RaderaAktivitet();

    return $retur;
}

/**
 * Tester för funktionen hämta alla aktiviteter
 * @return string html-sträng med alla resultat för testerna 
 */
function test_HamtaAllaAktiviteter(): string {
    $retur = "<h2>test_HamtaAllaAktiviteter</h2>";
    try {
        $svar=hamtaAllaAktiviteter();
        if($svar->getStatus()===200) {
            $retur .="<p class='ok'>Hämta alla aktiviteter returnerade 200 som förväntat</p>";
            if(array_key_exists("activities", $svar->getContent())){
                $retur .= "<p class='ok'>" . count($svar->getContent()['activities']) . " poster returnerades </p>";
            
        } else {
            $retur .="<p class='error'>Arrayen medd poster  ('activities') saknas </p>";
        }
        }
     }catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    }


    return $retur;
}

/**
 * Tester för funktionen hämta enskild aktivitet
 * @return string html-sträng med alla resultat för testerna 
 */
function test_HamtaEnAktivitet(): string {
    $retur = "<h2>test_HamtaEnAktivitet</h2>";
    try {
        $svar=hamtaEnskildAktivitet("-1");
        if($svar->getStatus()===400) {
            $retur .="<p class='ok'>hämta aktivitet med id=-1 returnerade 400, som förväntat</p>";
        } else {
            $retur .="<p class='error'>Hämta aktivitet med id=-1 returnerade {$svar->getStatus()}, 400 förväntades</p>";
        }


        $svar=hamtaEnskildAktivitet("sju");
        if($svar->getStatus()===400) {
            $retur .="<p class='ok'>hämta aktivitet med id=sju returnerade 400, som förväntat</p>";
        } else {
            $retur .="<p class='error'>Hämta aktivitet med id=sju returnerade {$svar->getStatus()}, 400 förväntades</p>";
        }
        // id som som inte finns
        $alla = hamtaAllaAktiviteter();
        $poster = $alla->getContent()['activities'];
        $maxId=0;
        foreach($poster as $item) {
            if($item->id>$maxId) {
                $maxId=$item->id;
            }
        }
        $svar = hamtaEnskildAktivitet((string) ($maxId+1));
        if($svar->getStatus()===400) {
            $retur .="<p class='ok'> hämta post med id (".($maxId+1).") som inte finns returnerade 400 som förväntat</p>";
        } else {
            $retur .="<p class='error'> något gick fel, (".($maxId+1).") meddelandet säger:<br> {$svar->getstatus()}</p>";
        }

        // id som finns
        $id=$poster[0]->id;
        $svar = hamtaEnskildAktivitet((string) $id);
        if($svar->getStatus()===200) {
            $retur .="<p class='ok'> hämta post med id som finns ($id) returnerade 200 som förväntat</p>";
        } else {
            $retur .="<p class='error'> hätma post med id som finns ($id) returnerade {$svar->getstatus()}</p>";

        }
       

    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    }

    return $retur;
}

/**
 * Tester för funktionen spara aktivitet
 * @return string html-sträng med alla resultat för testerna 
 */
function test_SparaNyAktivitet(): string {
    $retur = "<h2>test_SparaNyAktivitet</h2>";

    try {
        // test där verifieringen misslyckades 400
        $postData=['date'=>'2020-12-35', // felaktigtdatum
        'time'=>'01:00',
        'activityId'=>-1,
        'description'=>"test"
        ];
        // Test medfelaktigt aktivitetsID 400
     $svar = sparaNyUppgift($postData);
        if($svar->getStatus()===400) {
            $retur .="<p class='ok'> hämta post med felaktigt indata returnerade 400 som förväntat</p>";
        } else {
            $retur .="<p class='error'> något gick fel meddelandet säger:<br> {$svar->getstatus()}</p>";
        }
        // test utan descrip

        //
        $retur .= "<p class='error'>Inga tester implementerade</p>";
    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    }

    return $retur;
}

/**
 * Tester för uppdatera aktivitet
 * @return string html-sträng med alla resultat för testerna 
 */
function test_UppdateraAktivitet(): string {
    $retur = "<h2>test_UppdateraAktivitet</h2>";

    try {
        $retur .= "<p class='error'>Inga tester implementerade</p>";
    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    }

    return $retur;
}

/**
 * Tester för funktionen radera aktivitet
 * @return string html-sträng med alla resultat för testerna 
 */
function test_RaderaAktivitet(): string {
    $retur = "<h2>test_RaderaAktivitet</h2>";
    try {
        $retur .= "<p class='error'>Inga tester implementerade</p>";
    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    }

    return $retur;
}
