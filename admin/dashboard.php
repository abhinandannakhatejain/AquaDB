<?php
// =====================================================
// AquaGuard - Admin Dashboard
// =====================================================

require_once __DIR__ . "/../config/database.php";


// =====================================================
// DASHBOARD COUNTS
// =====================================================

// Total complaints
$total_complaints = 0;

$sql = "SELECT COUNT(*) AS total FROM complaints";
$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $total_complaints = $row['total'];
}


// Pending complaints
$pending_complaints = 0;

$sql = "SELECT COUNT(*) AS total 
        FROM complaints 
        WHERE status = 'PENDING'";

$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $pending_complaints = $row['total'];
}


// In progress complaints
$in_progress = 0;

$sql = "SELECT COUNT(*) AS total 
        FROM complaints 
        WHERE status = 'IN_PROGRESS'";

$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $in_progress = $row['total'];
}


// Resolved complaints
$resolved_complaints = 0;

$sql = "SELECT COUNT(*) AS total 
        FROM complaints 
        WHERE status = 'RESOLVED'";

$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $resolved_complaints = $row['total'];
}


// Critical alerts
$critical_alerts = 0;

$sql = "SELECT COUNT(*) AS total
        FROM water_alerts
        WHERE severity = 'CRITICAL'
        AND is_resolved = 0";

$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $critical_alerts = $row['total'];
}


// =====================================================
// RECENT COMPLAINTS
// =====================================================

$complaints = [];

$sql = "
    SELECT
        c.complaint_id,
        c.status,
        c.description,
        c.priority,
        c.created_at,
        u.name AS citizen,
        cc.category_name,
        l.area_name
    FROM complaints c

    LEFT JOIN users u
        ON c.user_id = u.user_id

    LEFT JOIN complaint_categories cc
        ON c.category_id = cc.category_id

    LEFT JOIN locations l
        ON c.location_id = l.location_id

    ORDER BY c.complaint_id DESC
    LIMIT 10
";

$result = $conn->query($sql);

if ($result) {

    while ($row = $result->fetch_assoc()) {
        $complaints[] = $row;
    }

}


// =====================================================
// CLOSE DATABASE CONNECTION LATER
// =====================================================

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>AquaGuard Admin Dashboard</title>


    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }


        body {

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #eef8fc;

            color: #123f50;

        }


        /* =========================================
           HEADER
        ========================================= */

        header {

            height: 86px;

            background: white;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 5.5%;

            box-shadow:
                0 2px 12px rgba(0,0,0,0.08);

        }


        .logo {

            font-size: 30px;

            font-weight: bold;

            color: #0785b2;

        }


        .logo .green {

            color: #08ad9d;

        }


        .drop {

            font-size: 31px;

            margin-right: 7px;

        }


        .header-right {

            display: flex;

            align-items: center;

            gap: 25px;

        }


        .admin-user {

            font-size: 16px;

            color: #536f7c;

        }


        .logout {

            text-decoration: none;

            background: #f25b5b;

            color: white;

            padding: 13px 24px;

            border-radius: 9px;

            font-weight: bold;

        }


        .logout:hover {

            background: #df4444;

        }


        /* =========================================
           MAIN
        ========================================= */

        .container {

            width: 90%;

            max-width: 1450px;

            margin: 42px auto;

        }


        .title {

            font-size: 40px;

            margin-bottom: 8px;

            color: #123f50;

        }


        .subtitle {

            font-size: 18px;

            color: #607d8b;

            margin-bottom: 34px;

        }


        /* =========================================
           STAT CARDS
        ========================================= */

        .stats {

            display: grid;

            grid-template-columns:
                repeat(5, 1fr);

            gap: 22px;

            margin-bottom: 38px;

        }


        .stat-card {

            background: white;

            border-radius: 17px;

            padding: 27px;

            min-height: 130px;

            box-shadow:
                0 7px 20px rgba(0,0,0,0.07);

        }


        .stat-title {

            color: #607d8b;

            font-size: 15px;

            font-weight: bold;

            margin-bottom: 14px;

        }


        .stat-number {

            color: #0785b2;

            font-size: 40px;

            font-weight: bold;

        }


        .critical-number {

            color: #e53935;

        }


        /* =========================================
           QUICK ACTIONS
        ========================================= */

        .quick-actions {

            background: white;

            padding: 28px;

            border-radius: 17px;

            margin-bottom: 32px;

            box-shadow:
                0 7px 20px rgba(0,0,0,0.07);

        }


        .quick-title {

            font-size: 25px;

            margin-bottom: 20px;

            color: #123f50;

        }


        .action-buttons {

            display: flex;

            gap: 14px;

            flex-wrap: wrap;

        }


        .action-btn {

            display: inline-block;

            text-decoration: none;

            padding: 13px 23px;

            border-radius: 8px;

            color: white;

            font-weight: bold;

            font-size: 15px;

        }


        .manage-btn {

            background: #0785b2;

        }


        .quality-btn {

            background: #0785b2;

        }


        .alert-btn {

            background: #f39a13;

        }


        .action-btn:hover {

            opacity: 0.88;

        }


        /* =========================================
           COMPLAINT SECTION
        ========================================= */

        .section {

            background: white;

            border-radius: 17px;

            padding: 30px;

            box-shadow:
                0 7px 20px rgba(0,0,0,0.07);

            margin-bottom: 35px;

            overflow-x: auto;

        }


        .section-title {

            font-size: 27px;

            margin-bottom: 25px;

            color: #123f50;

        }


        table {

            width: 100%;

            border-collapse: collapse;

            min-width: 950px;

        }


        th {

            background: #e5f5fa;

            color: #123f50;

            padding: 15px;

            text-align: left;

            font-size: 14px;

        }


        td {

            padding: 17px 15px;

            border-bottom:
                1px solid #e7eef1;

            font-size: 15px;

            color: #244f5f;

        }


        tr:hover {

            background: #f8fcfd;

        }


        /* =========================================
           STATUS BADGES
        ========================================= */

        .badge {

            display: inline-block;

            padding: 7px 13px;

            border-radius: 20px;

            font-size: 12px;

            font-weight: bold;

        }


        .pending {

            background: #fff0c7;

            color: #9b6b00;

        }


        .assigned {

            background: #dceffd;

            color: #087da8;

        }


        .progress {

            background: #e8e0ff;

            color: #6741b9;

        }


        .resolved {

            background: #d9f5df;

            color: #18833b;

        }


        .rejected {

            background: #ffe0e0;

            color: #d92323;

        }


        .high {

            color: #e53935;

            font-weight: bold;

        }


        .medium {

            color: #ed8b00;

            font-weight: bold;

        }


        .low {

            color: #16843a;

            font-weight: bold;

        }


        .empty {

            text-align: center;

            padding: 40px;

            color: #607d8b;

        }


        /* =========================================
           RESPONSIVE
        ========================================= */

        @media (max-width: 1100px) {

            .stats {

                grid-template-columns:
                    repeat(3, 1fr);

            }

        }


        @media (max-width: 750px) {

            .stats {

                grid-template-columns: 1fr;

            }


            .title {

                font-size: 32px;

            }


            .header-right {

                gap: 10px;

            }


            .admin-user {

                display: none;

            }


            .container {

                width: 94%;

            }

        }

    </style>

</head>


<body>


<!-- =====================================================
     HEADER
===================================================== -->

<header>

    <div class="logo">

        <span class="drop">💧</span>

        Aqua<span class="green">Guard</span>

    </div>


    <div class="header-right">

        <div class="admin-user">

            👤 Admin User

        </div>


        <a href="../index.php"
           class="logout">

            Logout

        </a>

    </div>

</header>



<!-- =====================================================
     MAIN CONTENT
===================================================== -->

<div class="container">


    <h1 class="title">

        Admin Dashboard

    </h1>


    <p class="subtitle">

        Monitor water quality and manage citizen complaints.

    </p>



    <!-- =================================================
         STATISTICS
    ================================================= -->

    <div class="stats">


        <div class="stat-card">

            <div class="stat-title">

                TOTAL COMPLAINTS

            </div>

            <div class="stat-number">

                <?php
                echo $total_complaints;
                ?>

            </div>

        </div>



        <div class="stat-card">

            <div class="stat-title">

                PENDING

            </div>

            <div class="stat-number">

                <?php
                echo $pending_complaints;
                ?>

            </div>

        </div>



        <div class="stat-card">

            <div class="stat-title">

                IN PROGRESS

            </div>

            <div class="stat-number">

                <?php
                echo $in_progress;
                ?>

            </div>

        </div>



        <div class="stat-card">

            <div class="stat-title">

                RESOLVED

            </div>

            <div class="stat-number">

                <?php
                echo $resolved_complaints;
                ?>

            </div>

        </div>



        <div class="stat-card">

            <div class="stat-title">

                🚨 CRITICAL ALERTS

            </div>

            <div class="stat-number critical-number">

                <?php
                echo $critical_alerts;
                ?>

            </div>

        </div>


    </div>



    <!-- =================================================
         QUICK ACTIONS
    ================================================= -->

    <div class="quick-actions">


        <h2 class="quick-title">

            ⚡ Quick Actions

        </h2>


        <div class="action-buttons">


            <!-- MANAGE COMPLAINTS -->

            <a href="manage_complaints.php"
               class="action-btn manage-btn">

                📋 Manage Complaints

            </a>



            <!-- WATER QUALITY -->

            <a href="../water_quality.php"
               class="action-btn quality-btn">

                💧 Water Quality

            </a>



            <!-- VIEW ALERTS
                 THIS IS THE IMPORTANT CHANGE
            -->

            <a href="../alerts.php"
               class="action-btn alert-btn">

                🚨 View Alerts

            </a>


        </div>

    </div>



    <!-- =================================================
         RECENT CITIZEN COMPLAINTS
    ================================================= -->

    <div class="section">


        <h2 class="section-title">

            📋 Recent Citizen Complaints

        </h2>


        <?php if (count($complaints) > 0): ?>


            <table>


                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Citizen</th>

                        <th>Category</th>

                        <th>Area</th>

                        <th>Description</th>

                        <th>Priority</th>

                        <th>Status</th>

                        <th>Date</th>

                    </tr>

                </thead>


                <tbody>


                <?php foreach ($complaints as $complaint): ?>


                    <tr>


                        <td>

                            #

                            <?php
                            echo htmlspecialchars(
                                $complaint['complaint_id']
                            );
                            ?>

                        </td>


                        <td>

                            <?php
                            echo htmlspecialchars(
                                $complaint['citizen']
                            );
                            ?>

                        </td>


                        <td>

                            <?php
                            echo htmlspecialchars(
                                $complaint['category_name']
                            );
                            ?>

                        </td>


                        <td>

                            <?php
                            echo htmlspecialchars(
                                $complaint['area_name']
                            );
                            ?>

                        </td>


                        <td>

                            <?php
                            echo htmlspecialchars(
                                $complaint['description']
                            );
                            ?>

                        </td>


                        <td>

                            <?php

                            $priority =
                                strtoupper(
                                    $complaint['priority']
                                );

                            if ($priority === 'HIGH') {

                                echo '<span class="high">
                                      HIGH
                                      </span>';

                            } elseif ($priority === 'MEDIUM') {

                                echo '<span class="medium">
                                      MEDIUM
                                      </span>';

                            } else {

                                echo '<span class="low">
                                      LOW
                                      </span>';

                            }

                            ?>

                        </td>


                        <td>


                            <?php

                            $status =
                                strtoupper(
                                    $complaint['status']
                                );


                            if ($status === 'PENDING') {

                                echo '<span class="badge pending">
                                      PENDING
                                      </span>';

                            } elseif ($status === 'ASSIGNED') {

                                echo '<span class="badge assigned">
                                      ASSIGNED
                                      </span>';

                            } elseif ($status === 'IN_PROGRESS') {

                                echo '<span class="badge progress">
                                      IN PROGRESS
                                      </span>';

                            } elseif ($status === 'RESOLVED') {

                                echo '<span class="badge resolved">
                                      RESOLVED
                                      </span>';

                            } elseif ($status === 'REJECTED') {

                                echo '<span class="badge rejected">
                                      REJECTED
                                      </span>';

                            } else {

                                echo htmlspecialchars($status);

                            }

                            ?>

                        </td>


                        <td>

                            <?php

                            if (!empty($complaint['created_at'])) {

                                echo date(
                                    "d M Y",
                                    strtotime(
                                        $complaint['created_at']
                                    )
                                );

                            }

                            ?>

                        </td>


                    </tr>


                <?php endforeach; ?>


                </tbody>


            </table>


        <?php else: ?>


            <div class="empty">

                No complaints found.

            </div>


        <?php endif; ?>


    </div>



    <!-- =================================================
         ACTIVE ALERT LINK
    ================================================= -->

    <div class="section">


        <h2 class="section-title">

            🚨 Active Water Quality Alerts

        </h2>


        <?php if ($critical_alerts > 0): ?>


            <p style="
                font-size:18px;
                color:#d92323;
                margin-bottom:20px;
            ">

                ⚠️ There are

                <strong>
                    <?php echo $critical_alerts; ?>
                </strong>

                active critical water-quality alert(s).

            </p>


            <a href="../alerts.php"
               class="action-btn alert-btn">

                🚨 View All Alerts

            </a>


        <?php else: ?>


            <p style="
                font-size:18px;
                color:#16843a;
            ">

                ✅ No active critical alerts.

            </p>


        <?php endif; ?>


    </div>


</div>



</body>

</html>


<?php

$conn->close();

?>