<?php
// Database connection
$host = 'localhost';
$dbname = 'budgeting';
$username = 'root'; // change to your database user
$password = ''; // change to your database password

// Create a connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Process budget request if "process" action is triggered
if (isset($_GET['process_id'])) {
    $processId = $_GET['process_id'];
    
    // Update the status to 'processing'
    $updateQuery = "UPDATE requests SET review_status = 'processing' WHERE id = $processId";
    
    if (mysqli_query($conn, $updateQuery)) {
        echo "<p style='color: green;'>Budget request with ID $processId has been set to processing.</p>";
    } else {
        echo "<p style='color: red;'>Error updating record: " . mysqli_error($conn) . "</p>";
    }
}

// Fetch budgets with 'requested' status
$query = "SELECT * FROM requests WHERE review_status = 'requested'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Requests Review</title>
</head>
<body>

<h2>Requested Budgets</h2>

<?php
// Check if any records were found
if (mysqli_num_rows($result) > 0) {
    echo "<table border='1' cellpadding='10'>
            <tr>
                <th>ID</th>
                <th>Department ID</th>
                <th>Date Created</th>
                <th>Requested By</th>
                <th>Status</th>
                <th>Item ID</th>
                <th>Action</th>
            </tr>";
    
    // Loop through the result and display records
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['department_id']}</td>
                <td>{$row['date_created']}</td>
                <td>{$row['requested_by']}</td>
                <td>{$row['review_status']}</td>
                <td>{$row['item_id']}</td>
                <td><a href='review.php?process_id={$row['id']}'>Process</a></td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No requested budgets found.</p>";
}
?>

</body>
</html>

<?php
// Close the connection
mysqli_close($conn);
?>
