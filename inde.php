<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

$xmlFile = 'books.xml';
$uploadDir = 'uploads/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
if ($_SESSION['role'] == 'user') {
    // Order button show karo
}
// --- PHP: Book Add Logic ---
if (isset($_POST['add_book'])) {
    $xml = simplexml_load_file($xmlFile);
    
    $imageName = $_FILES['book_image']['name'];
    $tempName = $_FILES['book_image']['tmp_name'];
    $targetPath = $uploadDir . time() . "_" . basename($imageName);
    
   
    if (move_uploaded_file($tempName, $targetPath)) {
        $newBook = $xml->addChild('book');
        $newBook->addChild('id', time());
        $newBook->addChild('title', $_POST['title']);
        $newBook->addChild('author', $_POST['author']);
        $newBook->addChild('genre', $_POST['genre']);
        $newBook->addChild('price', $_POST['price']);
        $newBook->addChild('image', $targetPath);

        $xml->asXML($xmlFile);
        header("Location: index.php?success=1");
        exit;
    }
}

$library = simplexml_load_file($xmlFile);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Book Portfolio Admin</title>

<style>
body { 
    font-family: 'Segoe UI', sans-serif; 
    background: #f0f2f5; 
    margin: 0; 
}

/* Navbar */
.navbar { 
    background: #0c5eaf; 
    color: white; 
    padding: 12px 5%; 
    height: 60px;
    display: flex; 
    justify-content: space-between; 
    align-items: center;
    position: sticky; 
    top: 0; 
    z-index: 1000; 
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.navbar h2 {
    margin: 0;
    font-size: 22px;
}

/* Buttons */
.nav-links button, .nav-links a { 
    background: #e67e22; 
    border: none; 
    color: white; 
    padding: 8px 15px; 
    border-radius: 5px; 
    cursor: pointer; 
    text-decoration: none; 
    margin-left: 10px; 
    font-weight: bold;
    font-size: 14px;
}

.logout-link { background: #c0392b !important; }

/* Modal */
.modal { 
    display: none; 
    position: fixed; 
    z-index: 2000; 
    left: 0; 
    top: 0; 
    width: 100%; 
    height: 100%; 
    background: rgba(0,0,0,0.7); 
}

.modal-content { 
    background: white; 
    margin: 8% auto; 
    padding: 25px; 
    width: 420px; 
    border-radius: 10px; 
    position: relative;
}

.close-btn { 
    position: absolute; 
    right: 15px; 
    top: 10px; 
    font-size: 24px; 
    cursor: pointer; 
    color: #666; 
}

/* Form */
.modal-content input { 
    width: 95%; 
    padding: 10px; 
    margin: 10px 0; 
    border: 1px solid #ddd; 
    border-radius: 5px; 
}

.save-btn { 
    width: 100%; 
    padding: 12px; 
    background: #27ae60; 
    color: white; 
    border: none; 
    border-radius: 5px; 
    cursor: pointer; 
}

/* Grid */
.grid { 
    display: grid; 
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); 
    gap: 20px; 
    padding: 40px 5%; 
}

/* Card */
.card { 
    background: white; 
    border-radius: 10px; 
    overflow: hidden; 
    box-shadow: 0 4px 10px rgba(0,0,0,0.1); 
    transition: 0.3s; 
}

.card:hover { 
    transform: translateY(-5px); 
}

/* Image Size Fix */
.card img { 
    width: 100%; 
    height: 250px;
    object-fit: cover;
}

/* Card Content */
.card-info { 
    padding: 15px; 
    text-align: center; 
}

.card-info h3 {
    margin: 5px 0;
    font-size: 18px;
}

.price { 
    color: #27ae60; 
    font-weight: bold; 
    font-size: 18px; 
}

   .footer{
   color: #faf9f9;
    padding:0;
    height:500%;
    
    text-align:center;
    border: 5px;
    background: #050505; 

   } 
</style>
</head>

<body>

<div class="navbar">
    <h2>MyLibrary 📚</h2>
    
    <div class="nav-links">

        <!-- ✅ Sirf ADMIN ke liye -->
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <a href="orders.php">View Orders</a>
        <?php endif; ?>

        <button onclick="openModal()">+ Add New Book</button>
        <a href="login.php?logout=1" class="logout-link">Logout</a>

    </div>
</div>

<!-- Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h2>Add New Book</h2>

        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Book Title" required>
            <input type="text" name="author" placeholder="Author Name" required>
            <input type="text" name="genre" placeholder="Genre" required>
            <input type="number" name="price" placeholder="Price (INR)" required>
            
            <label style="font-size: 12px; color: #777;">Upload Cover Image:</label>
            <input type="file" name="book_image" accept="image/*" required>

            <button type="submit" name="add_book" class="save-btn">Save Book</button>
        </form>
        <form method="POST" action="order.php">
    <input type="hidden" name="book_id" value="<?php echo $b->id; ?>">
    <button type="submit" name="order">Order Now</button>
</form>
    </div>
</div>

<div class="grid">
<?php if($library): foreach($library->book as $b): ?>
    <div class="card">
        <img src="<?php echo $b->image; ?>" alt="Cover">

        <div class="card-info">
            <h3><?php echo $b->title; ?></h3>
            <p style="color:#666; font-size:14px;">By: <?php echo $b->author; ?></p>
            <div class="price">₹<?php echo $b->price; ?></div>

            <!-- ✅ ORDER BUTTON (sirf user ke liye) -->
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'user'): ?>
                <form method="POST" action="order.php">
                    <input type="hidden" name="book_id" value="<?php echo $b->id; ?>">
                    <button type="submit" name="order" 
                        style="margin-top:10px; padding:8px; width:100%; background:#3498db; color:white; border:none; border-radius:5px;">
                        Order Now
                    </button>
                </form>
            <?php endif; ?>

        </div>
    </div>
<?php endforeach; endif; ?>
</div>

<script>
var modal = document.getElementById("addModal");

function openModal() {
    modal.style.display = "block";
}

function closeModal() {
    modal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == modal) {
        closeModal();
    }
}
</script>
<footer class="footer"><h1><b>We Care About Your PrivacyWe and our 710 partners store and access personal data, like browsing data or unique identifiers, on your device.</b></h1> Selecting I Accept enables tracking technologies to support the purposes shown under we and our partners process data to provide. If trackers are disabled, some content and ads you see may not be as relevant to you. You can resurface this menu to change your choices or withdraw consent at any time by clicking the Consent Management link on the bottom of the webpage. Your choices will have effect within our Website. For more details, refer to our Privacy Policy.Privacy and Cookies Policy
We and our partners process data to provide:
Use precise geolocation data. Actively scan device characteristics for identification. Store and/or access information on a device. Personalised advertising and content, advertising and content measurement, audience research and services development.List of Partners (vendors) </footer>
           
</body>
</html>