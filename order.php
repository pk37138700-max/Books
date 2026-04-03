<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['order'])) {

    $orders = simplexml_load_file("orders.xml");

    $newOrder = $orders->addChild("order");
    $newOrder->addChild("id", time());
    $newOrder->addChild("user_id", $_SESSION['user_id']);
    $newOrder->addChild("book_id", $_POST['book_id']);
    $newOrder->addChild("date", date("Y-m-d H:i:s"));

    $orders->asXML("orders.xml");

    echo "Order Placed Successfully! <a href='index.php'>Back</a>";
}
?>