<?php

declare (strict_types=1);
require_once __DIR__ . '/funktioner.php';

/**
 * Läs av rutt-information och anropa funktion baserat på angiven rutt
 * @param Route $route Rutt-information
 * @param array $postData Indata för behandling i angiven rutt
 * @return Response
 */
function activities(Route $route, array $postData): Response {
    try {
        if (count($route->getParams()) === 0 && $route->getMethod() === RequestMethod::GET) {
            return hamtaAllaAktiviteter();
        }
        if (count($route->getParams()) === 1 && $route->getMethod() === RequestMethod::GET) {
            return hamtaEnskildAktivitet($route->getParams()[0]);
        }
        if (isset($postData["activity"]) && count($route->getParams()) === 0 &&
                $route->getMethod() === RequestMethod::POST) {
            return sparaNyAktivitet((string) $postData["activity"]);
        }
        if (count($route->getParams()) === 1 && $route->getMethod() === RequestMethod::PUT) {
            return uppdateraAktivitet( $route->getParams()[0],  $postData["activity"]);
        }
        if (count($route->getParams()) === 1 && $route->getMethod() === RequestMethod::DELETE) {
            return raderaAktivetet($route->getParams()[0]);
        }
    } catch (Exception $exc) {
        return new Response($exc->getMessage(), 400);
    }

    return new Response("Okänt anrop", 400);
}

/**
 * Returnerar alla aktiviteter som finns i databasen
 * @return Response
 */
function hamtaAllaAktiviteter(): Response {
    // koppla mot databas
    $db=connectDb();
    // Hämta alla aktiviteter
    $result=$db->query("SELECT id, aktivitet FROM aktiviteter");
    //skapa retur
    $retur=[];
    foreach ($result as $post) {
        $rad=new stdClass();
        $rad->id=$post['id'];
        $rad->activity=$post['aktivitet'];
        $retur[]=$rad;
        }
        // returnera svar
        return new Response(["activities"=>$retur]);
    }
/**
 * Returnerar en enskild aktivitet som finns i databasen
 * @param string $id Id för aktiviteten
 * @return Response
 */
function hamtaEnskildAktivitet(string $id): Response {
     // Kontrollera indata
    $aktivitetsid=filter_var($id, FILTER_VALIDATE_INT);

    if($aktivitetsid===false) {
        $retur=new stdClass();
        $retur->error=["Bad request", "Ogiltigt id"];
        return new Response($retur, 400);
    }
     // koppla mot databas
    $db=connectDb();
     // skicka fråga
     $stmt=$db->prepare("SELECT id, aktivitet FROM aktiviteter where id=:id");
    $result=$stmt->execute(['id'=>$aktivitetsid]);
     // hantera svar
    if($row=$stmt->fetch()) {
        $retur=new stdClass();
        $retur->$id=$row['id'];
        $retur->activity=$row['aktivitet'];
        return new Response($retur);
    } else {
        $retur=new stdClass();
        $retur->error=['Bad request', "angivet id ($aktivitetsid) finns inte i databasen"];

        return new Response($retur, 400);
    }
     // returnera svar
}

/**
 * Lagrar en ny aktivitet i databasen
 * @param string $aktivitet Aktivitet som ska sparas
 * @return Response
 */
function sparaNyAktivitet(string $aktivitet): Response {
    // sanera indata
    $saneradAktivitet=htmlentities($aktivitet);


    // koppla mot databas
    $db=connectDb();

    try {
    $stmt=$db->prepare("INSERT INTO aktiviteter (aktivitet) VALUES (:aktivitet)");
    $svar=$stmt->execute([''=>$saneradAktivitet]);

    } catch (Exception $e) {
    $retur=new stdClass();
    $retur->error=["bad request", "kan inte skapa en aktivitet"];
    return new Response($retur, 400);
    }
    // skcika fråga


    // kontrollera resultat och returnera svar
    if($svar===true) {
        $retur=new stdClass();
        $retur->id=$db->lastInsertId();
        $retur->meddelande=['Spara lyckades', '1 post lades till'];
        return new Response($retur);
    } else {
        $retur=new stdClass();
        $retur->error=['Bad request', "Något gick fel vid spara", $stmt->errorInfo()];
        return new Response($retur, 400);
    }
}

/**
 * Uppdaterar angivet id med ny text
 * @param string $id Id för posten som ska uppdateras
 * @param string $aktivitet Ny text
 * @return Response
 */
function uppdateraAktivitet(string $id, string $aktivitet): Response {
    // kontrollera indata
    $kontrolleraID=filter_var($id, FILTER_VALIDATE_INT);
    $saneradAktivitet=htmlentities($aktivitet);


    if($kontrolleraID===false) {
        $retur=new stdClass();
        $retur->error=['Bad request' , 'Ogiltigt id'];
        return new Response($retur, 400);
    }
    // koppla databas

    $db =connectDb();
    $stmt=$db->prepare("UPDATE aktiviteter SET aktivitet=:aktivitet WHERE id=:id");
    $stmt->execute(['aktivitet'=>$saneradAktivitet, 'id'=>$kontrolleraID]);

    // skicka updaterin
    if($stmt->rowCount()===1) {
        $retur=new stdClass();
        $retur->result=true;
        $retur->meddelande=["Uppdatera lyckades", "1 rader uppdaterades"];
        return new Response($retur);

    } elseif ($stmt->rowCount()===0){ 
        $retur=new stdClass();
        $retur->result=false;
        $retur->meddelande=["Uppdatera misslyckades", "inga rader uppdaterades"];
        return new Response($retur);
    } else {
        $retur=new stdClass();
        $retur->result=true;
        $retur->meddelande=["Hoppsan","Uppdatera lyckades", $stmt->rowCount() . "rader uppdaterades"];
        return new Response($retur);
    }

    // kontrollera resutlat

    // 
}

/**
 * Raderar en aktivitet med angivet id
 * @param string $id Id för posten som ska raderas
 * @return Response
 */
function raderaAktivetet(string $id): Response {
// kontrollera indata
$kontrolleraId=filter_var($id, FILTER_VALIDATE_INT);

if ($kontrolleraId===false) {
    $retur=new stdClass();
    $retur->error=["bad request", "ogiltigt id"];
    return new Response($retur, 400);


}


$db=connectDb();
// skicka fråga

try {
    $stmt=$db->prepare("DELETE FROM aktiviteter WHERE id=:id");
    $stmt->execute(['id'=>$kontrolleraId]);
    
} catch (Exception $e) {
    $retur=new stdClass();
    $retur->error=["bad request", "kan inte radera en aktivitet"];
    return new Response($retur, 400);

}



// kontrollera svar 
if ($stmt->rowCount()>0) {
    $retur=new stdClass();
    $retur->result=true;
    $retur->meddelande=["Radera ltckades", $stmt->rowCount() . " poster raderades"];
    return new Response($retur);
} else {
    $retur=new stdClass();
    $retur->result=false;
    $retur->meddelande=["Radera misslyckades", "inga poster raderades"];
    return new Response($retur);
}



}