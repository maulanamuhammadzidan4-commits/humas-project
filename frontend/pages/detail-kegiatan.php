<?php
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$target = 'detail_dudi.php';

if ($id !== false && $id !== null && $id > 0) {
    $target .= '?id=' . $id;
}

header('Location: ' . $target, true, 302);
exit;