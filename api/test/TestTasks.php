<?php

declare (strict_types=1);
require_once __DIR__ . '/../src/tasks.php';

/**
 * Funktion för att testa alla aktiviteter
 * @return string html-sträng med resultatet av alla tester
 */
function allaTaskTester(): string {
// Kom ihåg att lägga till alla testfunktioner
    $retur = "<h1>Testar alla uppgiftsfunktioner</h1>";
    $retur .= test_HamtaEnUppgift();
    $retur .= test_HamtaUppgifterSida();
    $retur .= test_RaderaUppgift();
    $retur .= test_SparaUppgift();
    $retur .= test_UppdateraUppgifter();
    return $retur;
}

/**
 * Tester för funktionen hämta uppgifter för ett angivet sidnummer
 * @return string html-sträng med alla resultat för testerna 
 */
function test_HamtaUppgifterSida(): string {
    $retur = "<h2>test_HamtaUppgifterSida</h2>";
    try {
        $retur .= "<p class='error'>Inga tester implementerade</p>";
    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    }

    return $retur;
}

/**
 * Test för funktionen hämta uppgifter mellan angivna datum
 * @return string html-sträng med alla resultat för testerna
 */
function test_HamtaAllaUppgifterDatum(): string {
    $retur = "<h2>test_HamtaAllaUppgifterDatum</h2>";
    try {
        $retur .= "<p class='error'>Inga tester implementerade</p>";
    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    }

    return $retur;
}

/**
 * Test av funktionen hämta enskild uppgift
 * @return string html-sträng med alla resultat för testerna
 */
function test_HamtaEnUppgift(): string {
    $retur = "<h2>test_HamtaEnUppgift</h2>";
    // testa -1
try {
        $svar=hamtaEnskildUppgift("-1");
        if($svar->getStatus()===400) {
            $retur .="<p class='ok'>hämta aktivitet med id=-1 returnerade 400, som förväntat</p>";
        } else {
            $retur .="<p class='error'>Hämta aktivitet med id=-1 returnerade {$svar->getStatus()}, 400 förväntades</p>";
            } 
            
            // testa sju
         $svar=hamtaEnskildAktivitet("sju");
        if($svar->getStatus()===400) {
            $retur .="<p class='ok'>hämta aktivitet med id=sju returnerade 400, som förväntat</p>";
        } else {
            $retur .="<p class='error'>Hämta aktivitet med id=sju returnerade {$svar->getStatus()}, 400 förväntades</p>";
        }




    // testa uppgift som inte finns
    $db=connectDb();
    $sistaPost=$db->query('SELECT MAX(id) FROM uppgifter')->fetchColumn();

    $svar=hamtaEnskildAktivitet((string) ($sistaPost +1));
        if($svar->getStatus()===400) {
            $retur .="<p class='ok'>hämta aktivitet med id=" .$sistaPost +1 ."returnerade 400, som förväntat</p>";
        } else {
            $retur .="<p class='error'>Hämta aktivitet med id=" .$sistaPost +1 ." returnerade {$svar->getStatus()}, 400 förväntades</p>";
        }
    // Testa uppgift som finns
     $svar=hamtaEnskildAktivitet((string) ($sistaPost));
        if($svar->getStatus()===200) {
            $retur .="<p class='ok'>hämta aktivitet med id=" .$sistaPost ."returnerade 200, som förväntat</p>";
        } else {
            $retur .="<p class='ok'>Hämta aktivitet med id=" .$sistaPost  ." returnerade {$svar->getStatus()}, 200 förväntades</p>";
        }
        
    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    }

    return $retur;
}

/**
 * Test för funktionen spara uppgift
 * @return string html-sträng med alla resultat för testerna
 */
function test_SparaUppgift(): string {
    $retur = "<h2>test_SparaUppgift</h2>";
    $db=connectDb();
        try {
            //skapa transaktion för att inte fylla databasen med skräp
            $db->beginTransaction();

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
   
        $aktivitetsId= $db->query('SELECT MAX(id) FROM aktiviteter') -> fetchColumn();
        $postData['date'] = date('Y-m-d', strtotime('yesterday'));
        $postData['activityId'] = $aktivitetsId +1;
        $svar = sparaNyUppgift($postData);
        if ($svar->getStatus()===400) {
            $retur .="<p class='ok'> hämta post med felaktigt indata returnerade 400 som förväntat</p>";
        } else {
            $retur .="<p class='error'> något gick fel meddelandet säger:<br> {$svar->getstatus()}</p>";
        }


    $postData['activityId'] = $aktivitetsId;
    unset($postData['description']);
     $svar = sparaNyUppgift($postData);
        if ($svar->getStatus()===200) {
            $retur .="<p class='ok'> hämta post med felaktigt indata returnerade 200 som förväntat</p>";
        } else {
            $retur .="<p class='error'> något gick fel meddelandet säger:<br> {$svar->getstatus()}</p>";
        }
    } catch (Exception $ex) {
        
    } finally {
        $db->rollBack(); // ångrar vad som gjorts efter BeginTransaction();
    }

    return $retur;
}

/**
 * Test för funktionen uppdatera befintlig uppgift
 * @return string html-sträng med alla resultat för testerna
 */
function test_UppdateraUppgifter(): string {
    $retur = "<h2>test_UppdateraUppgifter</h2>";
    $db=connectDb();
    try {
      $db->beginTransaction();

   $aktivitetsID=$db->query('SELECT MAX(id) FROM uppgifter')->fetchColumn();
    $postData=[
        'date'=>date('Y-m-d', strtotime('yesterday')),
        'time'=>'01:00',
        'activityId'=>$aktivitetsID,
        'description'=>"test"
    ];
    $svar=sparaNyUppgift($postData);
    $id=$svar->getContent()->id;

    

    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    }

    return $retur;
    }
    

function test_KontrolleraIndata(): string {
    $retur = "<h2>test_KontrolleraIndata</h2>";

    try {
        $retur .= "<p class='error'>Inga tester implementerade</p>";
    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    }
    

    return $retur;
}

/**
 * Test för funktionen radera uppgift
 * @return string html-sträng med alla resultat för testerna
 */
function test_RaderaUppgift(): string {
    $retur = "<h2>test_RaderaUppgift</h2>";

    try {
        $retur .= "<p class='error'>Inga tester implementerade</p>";
    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    }

    return $retur;
}
