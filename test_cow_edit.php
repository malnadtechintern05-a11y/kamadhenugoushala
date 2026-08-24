<?php
require 'config/config.php';
require 'config/database.php';
require 'includes/functions.php';

$pdo = getDBConnection();

$id = 1;
$stmt = $pdo->prepare("SELECT * FROM cows WHERE id = :id");
$stmt->execute([':id' => $id]);
$cow = $stmt->fetch();

$old = [
    'name'            => 'Ganga',
    'breed'           => 'Gir',
    'age'             => 5,
    'gender'          => 'Female',
    'color'           => 'White',
    'weight_kg'       => 400,
    'health_status'   => 'Healthy',
    'adoption_status' => 'Available',
    'description'     => 'Test desc',
    'whatsapp_number' => '1234567890',
    'whatsapp_message'=> 'Hello this is custom!',
    'is_featured'     => 1,
    'image'           => 'test.jpg'
];

$stmt = $pdo->prepare("
    UPDATE cows SET name=:name, breed=:breed, age=:age, gender=:gender, color=:color,
    weight_kg=:weight_kg, health_status=:health, adoption_status=:adoption, description=:desc,
    whatsapp_number=:whatsapp_number, whatsapp_message=:whatsapp_message, image=:image, is_featured=:featured
    WHERE id=:id
");
$result = $stmt->execute([
    ':name'     => $old['name'],
    ':breed'    => $old['breed'],
    ':age'      => $old['age'],
    ':gender'   => $old['gender'],
    ':color'    => $old['color'],
    ':weight_kg'=> $old['weight_kg'] !== '' ? (float)$old['weight_kg'] : null,
    ':health'   => $old['health_status'],
    ':adoption' => $old['adoption_status'],
    ':desc'     => $old['description'],
    ':whatsapp_number'  => $old['whatsapp_number'] !== '' ? $old['whatsapp_number'] : null,
    ':whatsapp_message' => $old['whatsapp_message'] !== '' ? $old['whatsapp_message'] : null,
    ':image'    => $old['image'],
    ':featured' => $old['is_featured'],
    ':id'       => $id
]);

var_dump($result);

$stmt = $pdo->prepare("SELECT whatsapp_message FROM cows WHERE id = :id");
$stmt->execute([':id' => $id]);
$cow = $stmt->fetch();
var_dump($cow);
