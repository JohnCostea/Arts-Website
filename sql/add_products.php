<?php
$servername = "ionutproject";
$username = "root";
$password = "";
$dbname = "mydb";
$port = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed" . $conn->connect_error);
}

$name = "Caravaggio's Censored Cupid";
$description = "A re-creation of Caravaggio's famous painting with a censored twist. Made with high grade acrylics on a cotton stretched canvas.The dimensions are 60x40cm";
$price = 1099.99;
$category_id = 1; // 1=painting, 2=print, 3=merchandise
$image_url = "img/cupid_censored.jpg";

$stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdis", $name, $description, $price, $category_id, $image_url);

if ($stmt->execute()) {
    echo "Product added successfully! <br>";
} else {
    echo "Error: " . $stmt->error;
}

$name = "Caravaggio's Censored Cupid Print";
$description = "A re-creation of Caravaggio's famous painting with a censored twist. High Quality Print with dimensions of 30x40cm.";
$price = 59.99;
$category_id = 2; // 1=painting, 2=print, 3=merchandise
$image_url = "img/cupid_censored_print.jpg";

$stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdis", $name, $description, $price, $category_id, $image_url);

if ($stmt->execute()) {
    echo "Product added successfully! <br>";
} else {
    echo "Error: " . $stmt->error;
}

$name = "Caravaggio's Censored Cupid Tote Bag";
$description = "A tote bag with the re-creation of Caravaggio's famous painting with a censored twist. Printed on a high quality linen tote bag.";
$price = 29.99;
$category_id = 3; // 1=painting, 2=print, 3=merchandise
$image_url = "img/cupid_tote_bag.jpg";

$stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdis", $name, $description, $price, $category_id, $image_url);

if ($stmt->execute()) {
    echo "Product added successfully! <br>";
} else {
    echo "Error: " . $stmt->error;
}

$name = "Templar Tote Bag";
$description = "Tote bag with a templar knight helmet print. Printed on a high quality linen tote bag.";
$price = 29.99;
$category_id = 3; // 1=painting, 2=print, 3=merchandise
$image_url = "img/templar_tote_bag.jpg";

$stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdis", $name, $description, $price, $category_id, $image_url);

if ($stmt->execute()) {
    echo "Product added successfully! <br>";
} else {
    echo "Error: " . $stmt->error;
}

$name = "Sad Dogs Tote Bag";
$description = "Tote Bag with the sad dogs painting print. Printed on a high quality linen tote bag.";
$price = 29.99;
$category_id = 3; // 1=painting, 2=print, 3=merchandise
$image_url = "img/dogs_tote_bag.jpg";

$stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdis", $name, $description, $price, $category_id, $image_url);

if ($stmt->execute()) {
    echo "Product added successfully! <br>";
} else {
    echo "Error: " . $stmt->error;
}

$name = "Caravaggio's Censored Cupid T-Shirt";
$description = "A printed t-shirt of Caravaggio's famous painting with a censored twist. Printed on a 100% cotton t-shirt.";
$price = 69.99;
$category_id = 3; // 1=painting, 2=print, 3=merchandise
$image_url = "img/cupid_t-shirt.jpg";

$stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdis", $name, $description, $price, $category_id, $image_url);

if ($stmt->execute()) {
    echo "Product added successfully! <br>";
} else {
    echo "Error: " . $stmt->error;
}


$name = "Kids Eating Fruit";
$description = "An original painting going back in time depincting two kids eating fruit. A modern reinterpretation by Bartolome Esteban Murillo. Made with high grade acrylics on a cotton stretched canvas. Comes with a classic design black frame. The dimensions are 80x120cm";
$price = 3499.00;
$category_id = 1; // 1=painting, 2=print, 3=merchandise
$image_url = "img/framed_kids_fruits.jpg";

$stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdis", $name, $description, $price, $category_id, $image_url);

if ($stmt->execute()) {
    echo "Product added successfully! <br>";
} else {
    echo "Error: " . $stmt->error;
}

$name = "Horse in The Clouds";
$description = "An original painting depicting a horse among the clouds. Painted with high grade acrylics on a cotton stretched canvas.The dimensions are 80x120cm";
$price = 1899.00;
$category_id = 1; // 1=painting, 2=print, 3=merchandise
$image_url = "img/horse_clouds.jpg";

$stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdis", $name, $description, $price, $category_id, $image_url);

if ($stmt->execute()) {
    echo "Product added successfully! <br>";
} else {
    echo "Error: " . $stmt->error;
}

$name = "Horse in The Clouds Print";
$description = "A high print depicting a horse among the clouds. Printed on a thick texured paper.The dimensions are 40x65cm";
$price = 1899.00;
$category_id = 2; // 1=painting, 2=print, 3=merchandise
$image_url = "img/horse_clouds_print.jpg";

$stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdis", $name, $description, $price, $category_id, $image_url);

if ($stmt->execute()) {
    echo "Product added successfully! <br>";
} else {
    echo "Error: " . $stmt->error;
}

$name = "Horse in The Clouds T-Shirt";
$description = "T-Shirt with the horse among the clouds print. Printed on a 100% cotton t-shirt.";
$price = 69.99;
$category_id = 3; // 1=painting, 2=print, 3=merchandise
$image_url = "img/horse_tshirt.jpg";

$stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdis", $name, $description, $price, $category_id, $image_url);

if ($stmt->execute()) {
    echo "Product added successfully! <br>";
} else {
    echo "Error: " . $stmt->error;
}

$name = "Kids Eating Fruit Print";
$description = "A high quality print with the Kids Eating Fruit painting. Printed on a thick texured paper. The dimensions are 50x85cm";
$price = 129.99;
$category_id = 2; // 1=painting, 2=print, 3=merchandise
$image_url = "img/kids_eating_fruits_print.jpg";

$stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdis", $name, $description, $price, $category_id, $image_url);

if ($stmt->execute()) {
    echo "Product added successfully! <br>";
} else {
    echo "Error: " . $stmt->error;
}

$name = "Kids Eating Fruit T-Shirt";
$description = "T-shirt with the Kids Eating Fruit painting print. Printed on a 100% cotton t-shirt.";
$price = 69.99;
$category_id = 3; // 1=painting, 2=print, 3=merchandise
$image_url = "img/kids_fruit_tshirt.jpg";

$stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdis", $name, $description, $price, $category_id, $image_url);

if ($stmt->execute()) {
    echo "Product added successfully! <br>";
} else {
    echo "Error: " . $stmt->error;
}

$name = "Knight Attack Original Painting";
$description = "An original painting depicting two knights getting attacked by two horsemen. Painted with high grade acrylics on a cotton stretched canvas.The dimensions are 80x60cm";
$price = 1299.99;
$category_id = 1; // 1=painting, 2=print, 3=merchandise
$image_url = "img/knight_attack.jpg";

$stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdis", $name, $description, $price, $category_id, $image_url);

if ($stmt->execute()) {
    echo "Product added successfully! <br>";
} else {
    echo "Error: " . $stmt->error;
}

$name = "Knight Attack Original Print";
$description = "A print depicting two knights getting attacked by two horsemen. Printed on a thick texured paper. The dimensions are 80x60cm";
$price = 129.99;
$category_id = 2; // 1=painting, 2=print, 3=merchandise
$image_url = "img/knight_attack_print.jpg";

$stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdis", $name, $description, $price, $category_id, $image_url);

if ($stmt->execute()) {
    echo "Product added successfully! <br>";
} else {
    echo "Error: " . $stmt->error;
}

$name = "Knight Duel Original Painting";
$description = "An original painting depicting two knights preparing for a duel. Painted with high grade acrylics on a cotton stretched canvas.The dimensions are 60x110cm";
$price = 1799.99;
$category_id = 1; // 1=painting, 2=print, 3=merchandise
$image_url = "img/knight_duel.jpg";

$stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdis", $name, $description, $price, $category_id, $image_url);

if ($stmt->execute()) {
    echo "Product added successfully! <br>";
} else {
    echo "Error: " . $stmt->error;
}

$name = "Knight Duel Original Print";
$description = "A print depicting two knights preparing for a duel. Printed on a thick texured paper. The dimensions are 40x70cm";
$price = 119.99;
$category_id = 2; // 1=painting, 2=print, 3=merchandise
$image_url = "img/knight_duel_print.jpg";

$stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdis", $name, $description, $price, $category_id, $image_url);

if ($stmt->execute()) {
    echo "Product added successfully! <br>";
} else {
    echo "Error: " . $stmt->error;
}

$name = "Knight Horse Riding Original Painting";
$description = "An original painting depicting a knight riding a horse getting ready for attack. Painted with high grade acrylics on a cotton stretched canvas.The dimensions are 50x80cm";
$price = 999.99;
$category_id = 1; // 1=painting, 2=print, 3=merchandise
$image_url = "img/knight_on_horse.jpg";

$stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdis", $name, $description, $price, $category_id, $image_url);

if ($stmt->execute()) {
    echo "Product added successfully! <br>";
} else {
    echo "Error: " . $stmt->error;
}

$name = "Knight Horse Riding Print";
$description = "A print depicting a knight riding a horse getting ready for attack. Printed on a thick texured paper. The dimensions are 30x50cm";
$price = 99.99;
$category_id = 2; // 1=painting, 2=print, 3=merchandise
$image_url = "img/knight_on_horse_print.jpg";

$stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdis", $name, $description, $price, $category_id, $image_url);

if ($stmt->execute()) {
    echo "Product added successfully! <br>";
} else {
    echo "Error: " . $stmt->error;
}

$name = "Knight Duel T-Shirt";
$description = "T-Shirt with the knight duel print on front. Printed on a 100% cotton t-shirt.";
$price = 69.99;
$category_id = 3; // 1=painting, 2=print, 3=merchandise
$image_url = "img/knight_t-shirt.jpg";

$stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdis", $name, $description, $price, $category_id, $image_url);

if ($stmt->execute()) {
    echo "Product added successfully! <br>";
} else {
    echo "Error: " . $stmt->error;
}

$name = "Sad Dogs Original Painting";
$description = "An original painting depicting three dogs of different breeds. Painted with high grade acrylics on a cotton stretched canvas.The dimensions are 70x50cm";
$price = 1199.99;
$category_id = 1; // 1=painting, 2=print, 3=merchandise
$image_url = "img/sad_dogs.jpg";

$stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdis", $name, $description, $price, $category_id, $image_url);

if ($stmt->execute()) {
    echo "Product added successfully! <br>";
} else {
    echo "Error: " . $stmt->error;
}

$name = "Sad Dogs Original Print";
$description = "A print depicting three dogs of different breeds. Printed on a thick texured paper. The dimensions are 50x30cm";
$price = 119.99;
$category_id = 2; // 1=painting, 2=print, 3=merchandise
$image_url = "img/sad_dogs_print.jpg";

$stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdis", $name, $description, $price, $category_id, $image_url);

if ($stmt->execute()) {
    echo "Product added successfully! <br>";
} else {
    echo "Error: " . $stmt->error;
}

$name = "Templar Original Print";
$description = "A print depicting a templar knight helmet. Printed on a thick texured paper. The dimensions are 50x65cm";
$price = 139.99;
$category_id = 2; // 1=painting, 2=print, 3=merchandise
$image_url = "img/templar_digital_print.jpg";

$stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdis", $name, $description, $price, $category_id, $image_url);

if ($stmt->execute()) {
    echo "Product added successfully! <br>";
} else {
    echo "Error: " . $stmt->error;
}



$name = "Templar Helmet T-Shirt";
$description = "T-Shirt with a templar knight helmet print. Printed on a 100% cotton t-shirt.";
$price = 69.99;
$category_id = 3; // 1=painting, 2=print, 3=merchandise
$image_url = "img/templar_tshirt.jpg";

$stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdis", $name, $description, $price, $category_id, $image_url);

if ($stmt->execute()) {
    echo "Product added successfully! <br>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>