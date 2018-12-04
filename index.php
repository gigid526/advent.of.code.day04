<?php
$lines = file(__DIR__ . "/input.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$records = array_map(function ($line) {
    $matches = null;
    preg_match('/\[([0-9 \-]+:([0-9]+))\] (wakes up|falls asleep|Guard #([0-9]+) begins shift)/', $line, $matches);
    return [
        'ts' => $matches[1],
        'minute' => (int) $matches[2],
        'guardID' => isset($matches[4]) ? (int) $matches[4] : null
    ];
}, $lines);
usort($records, function ($a, $b) {
    return strcmp($a['ts'], $b['ts']);
});
// The first puzzle
$guards = [];
$guardID = null;
$lastMinute = null;
foreach ($records as $record) {
    if ($record['guardID'] !== null) {
        $guardID = $record['guardID'];
        if (isset($guards[$guardID]) === false) {
            $guards[$guardID] = ['id' => $guardID, 'sum' => 0, 'minutes' => array_fill(0, 60, 0)];
        }    
    } else if ($lastMinute !== null) {
        $guards[$guardID]['sum'] += $record['minute'] - $lastMinute;
        for ($i = $lastMinute; $i < $record['minute']; $i++) {
            $guards[$guardID]['minutes'][$i]++;
        }
        $lastMinute = null;
    } else {
        $lastMinute = $record['minute'];
    }
}
usort($guards, function ($a, $b) {
    return $b['sum'] - $a['sum'];
});
var_dump($guards[0]);
// The second puzzle
$max = -1;
$guardID = null;
$minute = null;
foreach ($guards as $guard) {
    if (max($guard['minutes']) > $max) {
        $max = max($guard['minutes']);
        $guardID = $guard['id'];
        $minute = array_search($max, $guard['minutes']);
    } 
}
var_dump($max, $guardID, $minute, $guardID * $minute);
