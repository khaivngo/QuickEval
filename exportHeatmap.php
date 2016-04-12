<?php
header('Content-Type: application/excel');
header('Content-Disposition: attachment; filename="heatmap.csv"');

require_once "classes/DB.php";

// json_encode("wadwad");
// exit;


# $users = DB::instance()->run_query("SELECT * FROM usertype");

$marks = [
    (object) ["points" => [
            (object) ["x" => 23, "y" => 21],
            (object) ["x" => 2453, "y" => 44],
            (object) ["x" => 76, "y" => 7],
            (object) ["x" => 424, "y" => 6]
        ]
    ],
    (object) ["points" => [
            (object) ["x" => 34, "y" => 23],
            (object) ["x" => 66, "y" => 342],
            (object) ["x" => 45, "y" => 213],
            (object) ["x" => 67, "y" => 123]
        ]
    ],
    (object) ["points" => [
            (object) ["x" => 3123, "y" => 433],
            (object) ["x" => 42, "y" => 4],
            (object) ["x" => 23, "y" => 5],
            (object) ["x" => 244, "y" => 8988]
        ]
    ]
];

$output = "x - y" . PHP_EOL . PHP_EOL;
foreach ($marks as $mark) {
    foreach($mark->points as $point) {
        $output .= $point->x . " - " . $point->y . PHP_EOL;
    }
}


# add the data to the file
file_put_contents('php://output', $output);
