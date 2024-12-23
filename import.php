<?php
$pdo = require $_SERVER['DOCUMENT_ROOT'] . "/audi/db.php";
$brand = $pdo->prepare('insert into brand(id, name, url, bold) values(?, ?, ?, ?)');
$models = $pdo->prepare('insert into models(brand_id, name, url, has_Panorama) values(?, ?, ?, ?)');
$generations = $pdo->prepare('insert into generations(model_id, title, url, src, src2x, generationInfo, isNewAuto, isComingSoon, frames, sgroup, sgroupSalug, sgroupShort) values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
$images = $pdo->prepare('insert into images(generations_id, url) values(?, ?)');

$models_id = $pdo->prepare('select id from models where name = ?');
$generations_id = $pdo->prepare('select id from generations where title = ?');

$data = file_get_contents('Audi.json');
$json = json_decode($data, true);
$id = $json['id'];
$br_name = $json['name'];
$br_url = $json['url'];
$bold = $json['bold'];

$brand->execute([$id, $br_name, $br_url, $bold]);
foreach ($json['models'] as $mod) {
    $mod_name = $mod['name'];
    $mod_url = $mod['url'];
    $hasPanorama = $mod['hasPanorama'];

    $models->execute([$id, $mod_name, $mod_url, $hasPanorama]);
    $models_id->execute([$mod_name]);
    $mod_id = $models_id->fetch(PDO::FETCH_ASSOC);
    $mod_id = (int)$mod_id['id'];

    foreach ($mod['generations'] as $gen) {
        $title = $gen['title'];
        $src = $gen['src'];
        $src2x = $gen['src2x'];
        $gen_url = $gen['url'];
        $gen_info = $gen['generationInfo'];
        $isNewAuto = $gen['isNewAuto'];
        $isComingSoon = $gen['isComingSoon'];
        $frames = $gen['frames'];
        $group = $gen['group'];
        $groupSalug = $gen['groupSalug'];
        $groupShort = $gen['groupShort'];

        $generations->execute([$mod_id, $title, $gen_url, $src, $src2x, $gen_info, $isNewAuto, $isComingSoon, $frames, $group, $groupSalug, $groupShort]);
        $generations_id->execute([$title]);
        $gen_id = $generations_id->fetch(PDO::FETCH_ASSOC);
        $gen_id = $gen_id['id'];

        foreach ($gen['images'] as $image) {

            $images->execute([$gen_id, $image]);
        }
    }
}