<?php

declare(strict_types=1);

/**
 * Läs av rutt-information och anropa funktion baserat på angiven rutt
 * @param Route $route Rutt-information
 * @param array $postData Indata för behandling i angiven rutt
 * @return Response
 */
function compilations(Route $route): Response
{

    try {
        if (count($route->getParams()) === 2 && $route->getMethod() === RequestMethod::GET) {
            return hamtaSammanstallning($route->getParams()[0], $route->getParams()[1]);
        }
    } catch (Exception $exc) {
        return new Response($exc->getMessage(), 400);
    }

    return new Response("Okänt anrop", 400);
}

/**
 * Hämtar en sammanställning av uppgiftsposter i ett angivet datumintervall
 * @param string $from
 * @param string $tom
 * @return Response
 */
function hamtaSammanstallning(string $from, string $tom): Response
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
    // koppla till databas
    $db = connectDb();
    // skicka fråga

    $stmt = $db->prepare('SELECT aktivitet_id, aktivitet,
 SEC_TO_TIME(SUM(TIME_TO_SEC(varaktighet))) AS time
FROM uppgifter 
INNER JOIN aktiviteter ON aktiviteter.id=aktivitet_id
WHERE datum BETWEEN :from AND :tom
GROUP BY aktivitet_id, aktivitet
ORDER BY TIME ');

$rows=$stmt->execute(['from' => $fromDate->format('Y-m-d'), 'tom' => $tomDate->format("Y-m-d")]);

$rows=$stmt->fetchall();
    // kontrollera svar
    $rader=[];
    foreach ($rows as $row) {
        $post=new stdClass();
        $post->activityId=$row['aktivitet_id'];
        $post->activity=$row['aktivitet'];
        $post->time=substr($row['time'], 0, -3);
        $rader[]=$post;
        
    }
    $retur=new stdClass();
    $retur->tasks=$rader;

    return new Response($retur);
}