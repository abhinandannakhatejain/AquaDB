<?php

session_start();

require_once "../config/database.php";

/* Protect page */
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "CITIZEN") {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$user_name = $_SESSION["user_name"];


/* Get complaint statistics */

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM complaints
    WHERE user_id = ?
");

$stmt->execute([$user_id]);

$total_complaints = $stmt->fetchColumn();


$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM complaints
    WHERE user_id = ?
    AND status = 'PENDING'
");

$stmt->execute([$user_id]);

$pending_complaints = $stmt->fetchColumn();


$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM complaints
    WHERE user_id = ?
    AND status = 'RESOLVED'
");

$stmt->execute([$user_id]);

$resolved_complaints = $stmt->fetchColumn();


/* Get recent complaints */

$stmt = $pdo->prepare("
    SELECT
        c.complaint_id,
        cc.category_name,
        l.area_name,
        c.description,
        c.priority,
        c.status,
        c.created_at
    FROM complaints c

    JOIN complaint_categories cc
        ON c.category_id = cc.category_id

    JOIN locations l
        ON c.location_id = l.location_id

    WHERE c.user_id = ?

    ORDER BY c.created_at DESC

    LIMIT 10
");

$stmt->execute([$user_id]);

$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Citizen Dashboard | AquaGuard</title>

<style>

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}

body {
    background: #f4f9fc;
    color: #123;
}


/* NAVBAR */

.navbar {

    height: 70px;

    background: white;

    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 0 6%;

    box-shadow:
        0 2px 10px
        rgba(0,0,0,0.08);
}

.logo {

    font-size: 27px;

    font-weight: bold;

    color: #087ea4;
}

.logo span {
    color: #00a896;
}

.logout {

    text-decoration: none;

    background: #e85d5d;

    color: white;

    padding: 10px 18px;

    border-radius: 8px;

    font-weight: bold;
}


/* MAIN */

.container {

    width: 88%;

    max-width: 1200px;

    margin: 40px auto;
}


/* WELCOME */

.welcome {

    margin-bottom: 30px;
}

.welcome h1 {

    color: #073b4c;

    margin-bottom: 8px;
}

.welcome p {

    color: #65727b;

    font-size: 16px;
}


/* STAT CARDS */

.stats {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 20px;

    margin-bottom: 35px;
}

.stat-card {

    background: white;

    padding: 25px;

    border-radius: 15px;

    box-shadow:
        0 5px 18px
        rgba(0,0,0,0.07);
}

.stat-card h3 {

    color: #65727b;

    font-size: 15px;

    margin-bottom: 10px;
}

.stat-number {

    font-size: 36px;

    font-weight: bold;

    color: #087ea4;
}


/* REPORT BUTTON */

.report-section {

    background:
        linear-gradient(
            135deg,
            #087ea4,
            #00a896
        );

    padding: 30px;

    border-radius: 16px;

    color: white;

    display: flex;

    align-items: center;

    justify-content: space-between;

    margin-bottom: 35px;
}

.report-section h2 {

    margin-bottom: 8px;
}

.report-section p {

    opacity: 0.9;
}

.report-btn {

    background: white;

    color: #087ea4;

    text-decoration: none;

    padding: 13px 22px;

    border-radius: 8px;

    font-weight: bold;

    white-space: nowrap;
}


/* COMPLAINTS */

.section-title {

    margin-bottom: 18px;

    color: #073b4c;
}

.table-container {

    background: white;

    border-radius: 15px;

    overflow-x: auto;

    box-shadow:
        0 5px 18px
        rgba(0,0,0,0.07);
}

table {

    width: 100%;

    border-collapse: collapse;
}

th {

    background: #eaf6fa;

    color: #073b4c;

    text-align: left;

    padding: 15px;
}

td {

    padding: 15px;

    border-top:
        1px solid #edf1f3;

    color: #52616b;
}


/* STATUS */

.status {

    padding: 6px 10px;

    border-radius: 20px;

    font-size: 12px;

    font-weight: bold;

    display: inline-block;
}

.PENDING {

    background: #fff3cd;

    color: #856404;
}

.ASSIGNED {

    background: #d9f1ff;

    color: #087ea4;
}

.IN_PROGRESS {

    background: #e5ddff;

    color: #6040a0;
}

.RESOLVED {

    background: #d8f5e8;

    color: #137a4b;
}

.REJECTED {

    background: #ffe0e0;

    color: #b00020;
}


/* PRIORITY */

.priority {

    font-weight: bold;
}


/* EMPTY */

.empty {

    text-align: center;

    padding: 40px;

    color: #65727b;
}


/* MOBILE */

@media(max-width: 750px) {

    .stats {

        grid-template-columns: 1fr;
    }

    .report-section {

        flex-direction: column;

        align-items: flex-start;

        gap: 20px;
    }

    .navbar {

        padding: 0 4%;
    }

    .container {

        width: 94%;
    }
}

</style>

</head>


<body>


<!-- NAVBAR -->

<nav class="navbar">

    <div class="logo">

        💧 Aqua<span>Guard</span>

    </div>

    <a
        href="logout.php"
        class="logout">

        Logout

    </a>

</nav>


<!-- MAIN -->

<main class="container">


    <!-- WELCOME -->

    <section class="welcome">

        <h1>

            Welcome,
            <?= htmlspecialchars($user_name) ?> 👋

        </h1>

        <p>

            Manage your water complaints
            and track their status.

        </p>

    </section>


    <!-- STATISTICS -->

    <section class="stats">


        <div class="stat-card">

            <h3>
                TOTAL COMPLAINTS
            </h3>

            <div class="stat-number">

                <?= $total_complaints ?>

            </div>

        </div>


        <div class="stat-card">

            <h3>
                PENDING
            </h3>

            <div class="stat-number">

                <?= $pending_complaints ?>

            </div>

        </div>


        <div class="stat-card">

            <h3>
                RESOLVED
            </h3>

            <div class="stat-number">

                <?= $resolved_complaints ?>

            </div>

        </div>


    </section>


    <!-- REPORT -->

    <section class="report-section">

        <div>

            <h2>
                Facing a water problem?
            </h2>

            <p>
                Report it to the Water Supply Department.
            </p>

        </div>

        <a
            href="report.php"
            class="report-btn">

            + Report Water Problem

        </a>

    </section>


    <!-- COMPLAINTS -->

    <h2 class="section-title">

        My Recent Complaints

    </h2>


    <div class="table-container">

        <?php if (count($complaints) > 0): ?>

        <table>

            <thead>

                <tr>

                    <th>
                        ID
                    </th>

                    <th>
                        Category
                    </th>

                    <th>
                        Area
                    </th>

                    <th>
                        Description
                    </th>

                    <th>
                        Priority
                    </th>

                    <th>
                        Status
                    </th>

                    <th>
                        Date
                    </th>

                </tr>

            </thead>


            <tbody>

            <?php foreach ($complaints as $complaint): ?>

                <tr>

                    <td>
                        #<?= $complaint["complaint_id"] ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $complaint["category_name"]
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $complaint["area_name"]
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $complaint["description"]
                        ) ?>
                    </td>

                    <td class="priority">

                        <?= htmlspecialchars(
                            $complaint["priority"]
                        ) ?>

                    </td>

                    <td>

                        <span
                            class="status <?= htmlspecialchars(
                                $complaint["status"]
                            ) ?>">

                            <?= htmlspecialchars(
                                $complaint["status"]
                            ) ?>

                        </span>

                    </td>

                    <td>

                        <?= date(
                            "d M Y",
                            strtotime(
                                $complaint["created_at"]
                            )
                        ) ?>

                    </td>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

        <?php else: ?>

            <div class="empty">

                You haven't submitted any complaints yet.

            </div>

        <?php endif; ?>

    </div>


</main>

</body>

</html>