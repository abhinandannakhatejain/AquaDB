<?php

session_start();

/* =========================
   ADMIN LOGIN PROTECTION
========================= */

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: login.php");
    exit();
}


/* =========================
   DATABASE CONNECTION
========================= */

require_once __DIR__ . "/../config/database.php";


/* =========================
   HANDLE STATUS UPDATE
========================= */

if (isset($_POST["update_status"])) {

    $complaint_id = intval($_POST["complaint_id"]);
    $status = $_POST["status"];

    $allowed_status = [
        "PENDING",
        "ASSIGNED",
        "IN_PROGRESS",
        "RESOLVED",
        "REJECTED"
    ];

    if (in_array($status, $allowed_status)) {

        $stmt = $conn->prepare("
            UPDATE complaints
            SET status = ?,
                resolved_at =
                CASE
                    WHEN ? = 'RESOLVED'
                    THEN CURRENT_TIMESTAMP
                    ELSE NULL
                END
            WHERE complaint_id = ?
        ");

        $stmt->bind_param(
            "ssi",
            $status,
            $status,
            $complaint_id
        );

        $stmt->execute();

        $stmt->close();
    }

    header("Location: manage_complaints.php");
    exit();
}


/* =========================
   HANDLE OFFICER ASSIGNMENT
========================= */

if (isset($_POST["assign_officer"])) {

    $complaint_id = intval($_POST["complaint_id"]);
    $officer_id = intval($_POST["officer_id"]);


    /* Remove old assignment */

    $stmt = $conn->prepare("
        DELETE FROM complaint_assignments
        WHERE complaint_id = ?
    ");

    $stmt->bind_param(
        "i",
        $complaint_id
    );

    $stmt->execute();

    $stmt->close();


    /* Create new assignment */

    $stmt = $conn->prepare("
        INSERT INTO complaint_assignments
        (complaint_id, officer_id)
        VALUES (?, ?)
    ");

    $stmt->bind_param(
        "ii",
        $complaint_id,
        $officer_id
    );

    $stmt->execute();

    $stmt->close();


    /* Change complaint status */

    $stmt = $conn->prepare("
        UPDATE complaints
        SET status = 'ASSIGNED'
        WHERE complaint_id = ?
    ");

    $stmt->bind_param(
        "i",
        $complaint_id
    );

    $stmt->execute();

    $stmt->close();


    header("Location: manage_complaints.php");
    exit();
}


/* =========================
   GET OFFICERS
========================= */

$officers = $conn->query("
    SELECT
        o.officer_id,
        u.name,
        o.department,
        o.designation
    FROM officers o
    JOIN users u
        ON o.user_id = u.user_id
    ORDER BY u.name
");


/* =========================
   GET COMPLAINTS
========================= */

$complaints = $conn->query("
    SELECT

        c.complaint_id,
        c.description,
        c.priority,
        c.status,
        c.created_at,

        u.name AS citizen,

        cc.category_name,

        l.area_name,

        ca.officer_id,

        officer_user.name AS officer_name

    FROM complaints c

    LEFT JOIN users u
        ON c.user_id = u.user_id

    LEFT JOIN complaint_categories cc
        ON c.category_id = cc.category_id

    LEFT JOIN locations l
        ON c.location_id = l.location_id

    LEFT JOIN complaint_assignments ca
        ON c.complaint_id = ca.complaint_id

    LEFT JOIN officers o
        ON ca.officer_id = o.officer_id

    LEFT JOIN users officer_user
        ON o.user_id = officer_user.user_id

    ORDER BY c.complaint_id DESC
");

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Manage Complaints | AquaGuard</title>


<style>

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}

body {
    background: #f1f8fb;
    color: #123f50;
}


/* HEADER */

.header {

    background: white;

    height: 75px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 0 5%;

    box-shadow:
        0 2px 10px rgba(0,0,0,0.08);
}

.logo {

    font-size: 28px;

    font-weight: bold;

    color: #0785a8;
}

.header-right {

    display: flex;

    align-items: center;

    gap: 20px;
}

.dashboard-link {

    text-decoration: none;

    color: #0785a8;

    font-weight: bold;
}

.logout {

    background: #ef5350;

    color: white;

    padding: 12px 20px;

    border-radius: 8px;

    text-decoration: none;

    font-weight: bold;
}


/* MAIN */

.container {

    width: 94%;

    max-width: 1500px;

    margin: 40px auto;
}

.title {

    font-size: 34px;

    margin-bottom: 8px;
}

.subtitle {

    color: #607d8b;

    margin-bottom: 30px;
}


/* TABLE */

.table-box {

    background: white;

    padding: 25px;

    border-radius: 15px;

    box-shadow:
        0 5px 20px rgba(0,0,0,0.06);

    overflow-x: auto;
}

table {

    width: 100%;

    border-collapse: collapse;

    min-width: 1200px;
}

th {

    background: #e8f6fa;

    padding: 15px;

    text-align: left;

    white-space: nowrap;
}

td {

    padding: 15px;

    border-bottom: 1px solid #eeeeee;

    vertical-align: top;
}


/* STATUS */

.status {

    padding: 6px 12px;

    border-radius: 20px;

    font-size: 12px;

    font-weight: bold;

    display: inline-block;
}

.pending {

    background: #fff1c7;

    color: #9a6b00;
}

.assigned {

    background: #d9f0ff;

    color: #0877a1;
}

.in_progress {

    background: #e5ddff;

    color: #6542b5;
}

.resolved {

    background: #d8f5df;

    color: #237a3b;
}

.rejected {

    background: #ffdede;

    color: #a52626;
}


/* PRIORITY */

.priority {

    font-weight: bold;
}

.high {

    color: #d32f2f;
}

.medium {

    color: #e68a00;
}

.low {

    color: #388e3c;
}


/* FORMS */

select {

    padding: 9px;

    border: 1px solid #ccdfe6;

    border-radius: 7px;

    min-width: 150px;

    margin-bottom: 8px;
}

button {

    border: none;

    padding: 9px 14px;

    border-radius: 7px;

    background: #0785a8;

    color: white;

    font-weight: bold;

    cursor: pointer;
}

button:hover {

    background: #056f8c;
}


/* OFFICER */

.officer-name {

    font-weight: bold;

    margin-bottom: 5px;
}

.no-officer {

    color: #9e9e9e;

    font-style: italic;
}

</style>

</head>


<body>


<!-- HEADER -->

<div class="header">

    <div class="logo">
        💧 AquaGuard
    </div>

    <div class="header-right">

        <a
            href="dashboard.php"
            class="dashboard-link"
        >
            ← Dashboard
        </a>

        <a
            href="logout.php"
            class="logout"
        >
            Logout
        </a>

    </div>

</div>


<!-- MAIN -->

<div class="container">

    <h1 class="title">
        Complaint Management
    </h1>

    <p class="subtitle">
        Assign complaints to officers and update their status.
    </p>


    <div class="table-box">

        <table>

            <tr>

                <th>ID</th>

                <th>Citizen</th>

                <th>Category</th>

                <th>Area</th>

                <th>Description</th>

                <th>Priority</th>

                <th>Status</th>

                <th>Officer</th>

                <th>Assign Officer</th>

                <th>Update Status</th>

            </tr>


            <?php while($row = $complaints->fetch_assoc()): ?>


            <tr>


                <!-- ID -->

                <td>

                    <strong>
                        #<?php
                        echo $row["complaint_id"];
                        ?>
                    </strong>

                </td>


                <!-- CITIZEN -->

                <td>

                    <?php
                    echo htmlspecialchars(
                        $row["citizen"]
                    );
                    ?>

                </td>


                <!-- CATEGORY -->

                <td>

                    <?php
                    echo htmlspecialchars(
                        $row["category_name"]
                    );
                    ?>

                </td>


                <!-- AREA -->

                <td>

                    <?php
                    echo htmlspecialchars(
                        $row["area_name"]
                    );
                    ?>

                </td>


                <!-- DESCRIPTION -->

                <td>

                    <?php
                    echo htmlspecialchars(
                        $row["description"]
                    );
                    ?>

                </td>


                <!-- PRIORITY -->

                <td>

                    <span
                        class="priority
                        <?php
                        echo strtolower(
                            $row["priority"]
                        );
                        ?>"
                    >

                        <?php
                        echo htmlspecialchars(
                            $row["priority"]
                        );
                        ?>

                    </span>

                </td>


                <!-- STATUS -->

                <td>

                    <span
                        class="status
                        <?php
                        echo strtolower(
                            $row["status"]
                        );
                        ?>"
                    >

                        <?php
                        echo str_replace(
                            "_",
                            " ",
                            htmlspecialchars(
                                $row["status"]
                            )
                        );
                        ?>

                    </span>

                </td>


                <!-- CURRENT OFFICER -->

                <td>

                    <?php if ($row["officer_name"]): ?>

                        <div class="officer-name">

                            👮
                            <?php
                            echo htmlspecialchars(
                                $row["officer_name"]
                            );
                            ?>

                        </div>

                    <?php else: ?>

                        <span class="no-officer">
                            Not Assigned
                        </span>

                    <?php endif; ?>

                </td>


                <!-- ASSIGN OFFICER -->

                <td>

                    <form method="POST">

                        <input
                            type="hidden"
                            name="complaint_id"
                            value="<?php
                            echo $row["complaint_id"];
                            ?>"
                        >

                        <select
                            name="officer_id"
                            required
                        >

                            <option value="">
                                Select Officer
                            </option>


                            <?php

                            $officers->data_seek(0);

                            while(
                                $officer =
                                $officers->fetch_assoc()
                            ):

                            ?>

                            <option
                                value="<?php
                                echo $officer["officer_id"];
                                ?>"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $officer["name"]
                                );
                                ?>

                            </option>

                            <?php endwhile; ?>

                        </select>


                        <br>


                        <button
                            type="submit"
                            name="assign_officer"
                        >
                            Assign
                        </button>

                    </form>

                </td>


                <!-- UPDATE STATUS -->

                <td>

                    <form method="POST">

                        <input
                            type="hidden"
                            name="complaint_id"
                            value="<?php
                            echo $row["complaint_id"];
                            ?>"
                        >


                        <select
                            name="status"
                            required
                        >

                            <option value="PENDING">
                                Pending
                            </option>

                            <option value="ASSIGNED">
                                Assigned
                            </option>

                            <option value="IN_PROGRESS">
                                In Progress
                            </option>

                            <option value="RESOLVED">
                                Resolved
                            </option>

                            <option value="REJECTED">
                                Rejected
                            </option>

                        </select>


                        <br>


                        <button
                            type="submit"
                            name="update_status"
                        >
                            Update
                        </button>

                    </form>

                </td>


            </tr>


            <?php endwhile; ?>


        </table>

    </div>

</div>


</body>

</html>

<?php

$conn->close();

?>