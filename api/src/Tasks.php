<?php

declare(strict_types=1);
require_once __DIR__ . '/activities.php';

/**
 * Hämtar en lista med alla uppgifter och tillhörande aktiviteter 
 * Beroende på indata returneras en sida eller ett datumintervall
 * @param Route $route indata med information om vad som ska hämtas
 * @return Response
 */
function tasklists(Route $route): Response
{

    try {
        if (count($route->getParams()) === 1 && $route->getMethod() === RequestMethod::GET) {
            return hamtaSida($route->getParams()[0]);
        }
        if (count($route->getParams()) === 2 && $route->getMethod() === RequestMethod::GET) {
            return hamtaDatum($route->getParams()[0], $route->getParams()[1]);
        }
    } catch (Exception $exc) {
        return new Response($exc->getMessage(), 400);
    }

    return new Response("Okänt anrop", 400);
}

/**
 * Läs av rutt-information och anropa funktion baserat på angiven rutt
 * @param Route $route Rutt-information
 * @param array $postData Indata för behandling i angiven rutt
 * @return Response
 */
function tasks(Route $route, array $postData): Response
{
    //    return new Response("Tasks");
    try {
        if (count($route->getParams()) === 1 && $route->getMethod() === RequestMethod::GET) {
            return hamtaEnskildUppgift($route->getParams()[0]);
        }
        if (count($route->getParams()) === 0 && $route->getMethod() === RequestMethod::POST) {
            return sparaNyUppgift($postData);
        }
        if (count($route->getParams()) === 1 && $route->getMethod() === RequestMethod::PUT) {
            return uppdateraUppgift($route->getParams()[0], $postData);
        }
        if (count($route->getParams()) === 1 && $route->getMethod() === RequestMethod::DELETE) {
            return raderaUppgift($route->getParams()[0]);
        }
    } catch (Exception $exc) {
        return new Response($exc->getMessage(), 400);
    }
}

/**
 * Hämtar alla uppgifter för en angiven sida
 * @param string $sida
 * @return Response
 */
function hamtaSida(string $sida): Response
{
    // kontrollera indata
    $sidnummer = filter_var($sida, FILTER_VALIDATE_INT);

    if ($sidnummer === false) {
        $retur = new stdClass();
        $retur->error = ['bad request', 'ogiltigt sidnummer'];

        return new Response($retur, 400);

    } elseif ($sidnummer < 1) {
        $retur = new stdClass();
        $retur->error = ['bad request', 'sidnummer ska vara större än noll'];

        return new Response($retur, 400);
    }


    //hämta antal poster
    $settings = new Settings();
    $posterPerSida = $settings->recordsPerPage;
    //koppla databas
    $db = connectDb();

    // skicka fråga om antalo poster
    $result = $db->query("SELECT COUNT(*) FROM uppgifter");
    $antalRader = $result->fetchColumn();

    $antalSidor = ceil($antalRader / $posterPerSida);

    // kontrollera begära sidan

    if ($sidnummer > $antalSidor) {
        $retur = new stdClass();
        $retur->error = ['bad request', "Det finns bara $antalSidor"];
        return new Response($retur, 400);
    }
    // skicka fråga för aktuell sida

    // returnera svar
}

/**
 * Hämtar alla poster mellan angivna datum
 * @param string $from
 * @param string $tom
 * @return Response
 */
function hamtaDatum(string $from, string $tom): Response
{
    // kontrollera indata
    $fromDate = DateTimeImmutable::createFromFormat("Y-m-d", $from);
    $tomDate = DateTimeImmutable::createFromFormat("Y-m-d", $tom);


    $err = [];
    if ($fromDate === false) {
        $err[] = "ogiltigt från datum";
    } elseif ($fromDate->format('Y-m-d') !== $from) {
        $err[] = "ogiltigt format på från datum";
    }

    if ($tomDate === false) {
        $err[] = "ogiltigt till datum";
    } elseif ($tomDate->format('Y-m-d') !== $tom) {
        $err[] = "ogiltigt format på till datum";
    }

    if (count($err) === 0 && $fromDate->format('Y-m-d') > $tomDate->format('Y-m-d')) {
        $err[] = "Från datum ska vara mindre än till datun";
    }

    if (count($err) > 0) {
        array_unshift($err, 'bad request');
        $retur = new stdClass();
        $retur->error = $err;
        return new Response($retur, 400);
    }

    // Koppla databas
    $db = connectDb();


    // skicka fråga
    $stmt = $db->prepare('SELECT uppgifter.id, aktivitet_id, datum, varaktighet, aktivitet, beskrivning
FROM uppgifter
INNER JOIN aktiviteter ON aktiviteter.id=aktivitet_id
WHERE datum BETWEEN :from AND :to
ORDER BY datum');
    $stmt->execute(['from' => $fromDate->format('Y-m-d'), 'to' => $tomDate->format("Y-m-d")]);

    // Kontrollera svar och retunera data
    $retur = [];

    foreach ($stmt->fetchAll() as $row) {
        $post = new stdClass();
        $post->id = $row['id'];
        $post->activtyId = $row['aktivitet_id'];
        $post->date = $row['datum'];
        $post->time = $row['varaktighet'];
        $post->activity = $row['aktivitet'];
        $post->description = $row['beskrivning'];
        $retur[] = $post;

    }
    return new Response($retur);
}

/**
 * Hämtar en enskild uppgiftspost
 * @param string $id Id för post som ska hämtas
 * @return Response
 */
function hamtaEnskildUppgift(string $id): Response
{
     // Kontrollera indata
    $taskId = filter_var($id, FILTER_VALIDATE_INT);

    if ($taskId === false) {
        $retur = new stdClass();
        $retur->error = ['Bad request', 'Ogiltigt uppgiftsid'];

        return new Response($retur, 400);
    }

    // Koppla databas
    $db = connectDb();

    // Hämta post
    $stmt = $db->prepare('SELECT uppgifter.id, aktivitet_id, datum, varaktighet,aktivitet, beskrivning 
FROM uppgifter
INNER JOIN aktiviteter ON aktiviteter.id=aktivitet_id
WHERE uppgifter.id=:id');
    $stmt->execute(['id' => $taskId]);

    // Returnera svar
    $row = $stmt->fetch();
    if (!$row) {
        $retur = new stdClass();
        $retur->error = ['Bad request', "Angivet id ($taskId) finns inte i databasen"];

        return new Response($retur, 400);
    }
    $retur = new stdClass();
    $retur->id = $row['id'];
    $retur->date = $row['datum'];
    $retur->time = substr($row['varaktighet'], 0, 5);
    $retur->activityId = $row['aktivitet_id'];
    $retur->activity = $row['aktivitet'];
    $retur->description = $row['beskrivning'];

    return new Response($retur);
}




/**
 * Sparar en ny uppgiftspost
 * @param array $postData indata för uppgiften
 * @return Response
 */
function sparaNyUppgift(array $postData): Response
{

    // kontrollera indata
    $indataErr = kontrolleraIndata($postData);
    if (count($indataErr) > 0) {
        $retur = new stdClass();
        $retur->error = array_merge(['Bad request'], $indataErr);
        return new Response($retur, 400);
    }
    if (!array_key_exists('description', $postData)) {
        $postData['description'] = "";
    } else {
        $postData['description'] = htmlentities($postData['description']);
    }
    // raderar action från postdata så att vi kan använda den vi insert-frågan
    unset($postData['action']);
    // koppla databas
    $db = connectDb();
    // skicka fråga
    try {
        $stmt = $db->prepare('INSERT INTO uppgifter (aktivitet_id, datum, varaktighet, beskrivning)
    VALUES(:activityId, :date, :time, :description)');
        $stmt->execute($postData);
        // returnera svar
        $nyttId = $db->lastInsertId();
        $retur = new stdClass();
        $retur->id = $nyttId;
        $retur->meddelande = ['Spara lyckades'];

        return new Response($retur);
    } catch (Exception $e) {
        $retur = new stdClass();
        $retur->error = ['Bad request', 'Fel vid spara (felaktigt aktivitetsID)'];
        return new Response($retur, 400);
    }
}

/**
 * Uppdaterar en angiven uppgiftspost med ny information 
 * @param string $id id för posten som ska uppdateras
 * @param array $postData ny data att sparas
 * @return Response
 */
function uppdateraUppgift(string $id, array $postData): Response
{
    // kontrollera indata
    $indataErr = kontrolleraIndata($postData);
    if (count($indataErr) > 0) {
        $retur = new stdClass();
        $retur->error = array_merge(['Bad request'], $indataErr);
        return new Response($retur, 400);
    }
    if (!array_key_exists('description', $postData)) {
        $postData['description'] = "";
    } else {
        $postData['description'] = htmlentities($postData['description']);

    }
    $taskId = filter_var($id, FILTER_VALIDATE_INT);

    if ($taskId === false) {
        $retur = new stdClass();
        $retur->error = ['Bad request', 'Ogiltigt id'];
        return new Response($retur, 400);

    }
    $postData['id'] = $taskId;
    unset($postData['action']);
    // koppla databas
    $db = connectDb();
    try {
        //skicka fråga
        $stmt = $db->prepare('UPDATE uppgifter SET
      datum=:date, varaktighet=:time, aktivitet_id=:activityId, beskrivning=:description
      WHERE id=:id');

        $stmt->execute($postData);


        // kontrollera svar och returnera medelande
        if ($stmt->rowCount() === 0) {
            $retur = new stdClass();
            $retur->result = false;
            $retur->message = ["Uppdatera misslyckades", "inga rader uppdaterades"];
        } else {
            $retur = new stdClass();
            $retur->result = true;
            $retur->message = ["Uppdatera lyckades", "{$stmt->rowCount()}rader uppdaterades"];
        }
        return new Response($retur);
    } catch (Exception $e) {
        $retur = new stdClass();
        $retur->error = ['Bad request', 'Fel vid spara (felaktigt aktivitetsID)'];
        return new Response($retur, 400);
    }
}
/**
 * Raderar en uppgiftspost
 * @param string $id Id för posten som ska raderas
 * @return Response
 */
function raderaUppgift(string $id): Response
{

}
/**
 *indata arrayen ska innehålla följande
 * - date som YYYY-mm-dd
 * - time som HH:MM
 * - activityID som heltal
 * - description som text 
 *@param array $postData
 *@retur array
 */


function kontrolleraIndata(array $postData): array
{
    $returArray = [];
    if (array_key_exists("date", $postData)) {
        $datum = DateTimeImmutable::createFromFormat('Y-m-d', $postData['date']);
        if ($datum === false) {
            $returArray[] = "Ogiltigt datum";
        } elseif ($datum->format('Y-m-d') !== $postData['date']) {
            $returArray[] = "ogiltigt datum format";

        } elseif ($datum->format('Y-m-d') > date('Y-m-d')) {
            $returArray[] = "Datum får inte vara i framtiden";
        }
    } else {
        $returArray[] = "Datum ('date') saknas";
    }

    // indatakontroller får varaktighet ($postData['time'])
    if (array_key_exists('time', $postData)) {
        $varaktighet = DateTimeImmutable::createFromFormat('H:i', $postData['time']);
        if ($varaktighet === false) {
            $returArray[] = "ogiltigt varaktighet";
        } elseif ($varaktighet->format("H:i") !== $postData['time']) {
            $returArray[] = "Ogiltigt todangivelse för varaktighet";
        } elseif ($postData['time'] > "08:00" || $postData['time'] < "00:15") {
            $returArray[] = "varaktigheten ska vara mindre än 8 timmar eller mer än 15 minuter";
        } elseif (!in_array(substr($postData['time'], -2), ["00", "15", "30", "45"])) {
            $returArray[] = "ange varaktigheten i jämna 15 minuter";
        }

    } else {
        $returArray[] = "varaktighet ('time') saknas";
    }
    //indatakontroll för aktivitetsID ($postData['aktivitetsid'])
    if (array_key_exists('activityId', $postData)) {
        $activityId = filter_var($postData['activityId'], FILTER_VALIDATE_INT);
        if ($activityId === false) {
            $returArray[] = "ogiltigt aktivitetsId";
        } elseif ($activityId < 1) {
            $returArray[] = "AktivitetsId('activityId') ska vara större än noll";
        }
    } else {
        $returArray[] = "aktivitet ('activityId') saknas";

    }
    return $returArray;

}