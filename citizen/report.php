<?php
session_start();

$conn = new mysqli("localhost", "root", "", "aquaguard");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

/* Get logged-in user */
$user_id = $_SESSION['user_id'] ?? 2;

/* Get categories */
$categories = $conn->query("SELECT category_id, category_name FROM complaint_categories");

/* Get locations */
$locations = $conn->query("SELECT location_id, area_name FROM locations");

/* Submit complaint */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $category_id = intval($_POST['category_id']);
    $location_id = intval($_POST['location_id']);
    $description = trim($_POST['description']);
    $priority = $_POST['priority'];

    $status = "PENDING";

    $stmt = $conn->prepare("
        INSERT INTO complaints
        (user_id, category_id, location_id, description, priority, status)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "iiisss",
        $user_id,
        $category_id,
        $location_id,
        $description,
        $priority,
        $status
    );

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Failed to submit complaint: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Report Water Problem | AquaGuard</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background: #eef9fc;
            color: #123f50;
        }

        header {
            background: white;
            padding: 20px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #0787b0;
        }

        .logo span {
            color: #00a98f;
        }

        .back {
            color: #0787b0;
            text-decoration: none;
            font-weight: bold;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        }

        h1 {
            margin-bottom: 10px;
            font-size: 32px;
        }

        .subtitle {
            color: #657985;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        select,
        textarea {
            width: 100%;
            padding: 14px;
            border: 1px solid #c8dce3;
            border-radius: 10px;
            font-size: 16px;
            margin-bottom: 22px;
            background: white;
        }

        textarea {
            height: 130px;
            resize: vertical;
        }

        .priority-box {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
        }

        .priority-box input {
            display: none;
        }

        .priority-box label {
            padding: 12px 20px;
            border: 1px solid #c8dce3;
            border-radius: 8px;
            cursor: pointer;
            margin: 0;
        }

        .priority-box input:checked + label {
            background: #0787b0;
            color: white;
            border-color: #0787b0;
        }

        button {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 10px;
            background: #0787b0;
            color: white;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #056d8e;
        }

        .error {
            background: #ffe5e5;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

<header>
    <div class="logo">💧 Aqua<span>Guard</span></div>
    <a href="dashboard.php" class="back">← Back to Dashboard</a>
</header>

<div class="container">

    <h1>Report a Water Problem</h1>

    <p class="subtitle">
        Help us identify and resolve water-related problems in your area.
    </p>

    <?php if (isset($error)): ?>
        <div class="error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label>Complaint Category</label>

        <select name="category_id" required>
            <option value="">Select category</option>

            <?php while ($row = $categories->fetch_assoc()): ?>

                <option value="<?php echo $row['category_id']; ?>">
                    <?php echo htmlspecialchars($row['category_name']); ?>
                </option>

            <?php endwhile; ?>

        </select>


        <label>Area / Location</label>

        <select name="location_id" required>
            <option value="">Select your area</option>

            <?php while ($row = $locations->fetch_assoc()): ?>

                <option value="<?php echo $row['location_id']; ?>">
                    <?php echo htmlspecialchars($row['area_name']); ?>
                </option>

            <?php endwhile; ?>

        </select>


        <label>Describe the Problem</label>

        <textarea
            name="description"
            placeholder="Example: Brown colored water is coming from the tap."
            required
        ></textarea>


        <label>Priority</label>

        <div class="priority-box">

            <input type="radio" name="priority" value="LOW" id="low">
            <label for="low">LOW</label>

            <input type="radio" name="priority" value="MEDIUM" id="medium" checked>
            <label for="medium">MEDIUM</label>

            <input type="radio" name="priority" value="HIGH" id="high">
            <label for="high">HIGH</label>

            <input type="radio" name="priority" value="CRITICAL" id="critical">
            <label for="critical">CRITICAL</label>

        </div>


        <button type="submit">
            Submit Complaint
        </button>

    </form>

</div>

</body>
</html>