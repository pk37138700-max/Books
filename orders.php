<?php
session_start();

if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$orders = simplexml_load_file("orders.xml");
$books = simplexml_load_file("books.xml");
$users = simplexml_load_file("users.xml");
?>

<h2>All Orders</h2>

<table border="1">
<tr>
    <th>User</th>
    <th>Book</th>
    <th>Date</th>
</tr>

<?php foreach ($orders->order as $o): ?>
<tr>
    <td>
        <?php foreach ($users->user as $u) {
            if ($u->id == $o->user_id) echo $u->name;
        } ?>
    </td>

    <td>
        <?php foreach ($books->book as $b) {
            if ($b->id == $o->book_id) echo $b->title;
        } ?>
    </td>

    <td><?php echo $o->date; ?></td>
</tr>
<?php endforeach; ?>
</table>