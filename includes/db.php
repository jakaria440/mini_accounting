<?php

$conn = new mysqli("localhost", "u471077073_al_barakah", "Al@barakah321", "u471077073_al_barakah");


if ($conn->connect_error) {
    die("সংযোগ ব্যর্থ হয়েছে: " . $conn->connect_error);
}
