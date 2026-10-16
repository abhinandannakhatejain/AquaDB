<?php
// =====================================================
// AquaGuard - Water Quality Alerts
// =====================================================

require_once __DIR__ . "/config/database.php";


// =====================================================
// RESOLVE ALERT
// =====================================================

if (isset($_POST['resolve_alert'])) {

    $alert_id = intval($_POST['alert_id']);

    if ($alert_id > 0) {

        $stmt = $conn->prepare("
            UPDATE water_alerts
            SET is_resolved = 1
            WHERE alert_id = ?
        ");

        $stmt->bind_param("i", $alert_id);

        $stmt->execute();

        $stmt->close();
    }

    // Refresh page
    header("Location: alerts.php");
    exit;
}


// =====================================================
// GET ALERTS
// =====================================================

$alerts = [];

$sql = "
    SELECT
        wa.alert_id,
        wa.location_id,
        wa.reading_id,
        wa.alert_type,
        wa.severity,
        wa.reason,
        wa.detected_at,
        wa.is_resolved,
        l.area_name,
        l.city
    FROM water_alerts wa

    LEFT JOIN locations l
        ON wa.location_id = l.location_id

    ORDER BY wa.alert_id DESC
";

$result = $conn->query($sql);

if ($result) {

    while ($row = $result->fetch_assoc()) {

        $alerts[] = $row;

    }
}


// =====================================================
// COUNTS
// =====================================================

$total_alerts = count($alerts);

$critical_alerts = 0;

$active_alerts = 0;

$resolved_alerts = 0;


foreach ($alerts as $alert) {

    if (strtoupper($alert['severity']) === 'CRITICAL') {
        $critical_alerts++;
    }

    if ((int)$alert['is_resolved'] === 0) {
        $active_alerts++;
    }

    if ((int)$alert['is_resolved'] === 1) {
        $resolved_alerts++;
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Water Quality Alerts | AquaGuard</title>


<style>

/* =====================================================
   RESET
===================================================== */

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}


/* =====================================================
   BODY
===================================================== */

body {

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    background: #eef8fc;

    color: #123f50;
}


/* =====================================================
   HEADER
===================================================== */

header {

    height: 88px;

    background: white;

    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 0 7%;

    box-shadow:
        0 2px 12px rgba(0,0,0,0.08);
}


.logo {

    font-size: 30px;

    font-weight: bold;

    color: #0785b2;
}


.logo-green {

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


.dashboard-link {

    text-decoration: none;

    color: #0785b2;

    font-weight: bold;

    font-size: 17px;
}


.logout {

    text-decoration: none;

    background: #f45b5b;

    color: white;

    padding: 13px 24px;

    border-radius: 9px;

    font-weight: bold;
}


/* =====================================================
   MAIN
===================================================== */

.container {

    width: 90%;

    max-width: 1400px;

    margin: 45px auto;
}


.page-title {

    font-size: 40px;

    margin-bottom: 8px;
}


.page-subtitle {

    font-size: 18px;

    color: #607d8b;

    margin-bottom: 35px;
}


/* =====================================================
   STATISTICS
===================================================== */

.stats {

    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    gap: 24px;

    margin-bottom: 35px;
}


.stat-card {

    background: white;

    padding: 28px;

    border-radius: 17px;

    box-shadow:
        0 7px 20px rgba(0,0,0,0.07);
}


.stat-title {

    font-size: 15px;

    font-weight: bold;

    color: #607d8b;

    margin-bottom: 12px;
}


.stat-number {

    font-size: 40px;

    font-weight: bold;

    color: #0785b2;
}


.critical-number {

    color: #e53935;
}


.resolved-number {

    color: #18833b;
}


/* =====================================================
   ALERT SECTION
===================================================== */

.alert-section {

    background: white;

    border-radius: 18px;

    padding: 30px;

    box-shadow:
        0 7px 25px rgba(0,0,0,0.07);

    overflow-x: auto;
}


.section-title {

    font-size: 27px;

    margin-bottom: 25px;
}


/* =====================================================
   TABLE
===================================================== */

table {

    width: 100%;

    border-collapse: collapse;

    min-width: 1200px;
}


th {

    background: #e5f5fa;

    padding: 16px 13px;

    text-align: left;

    font-size: 14px;
}


td {

    padding: 17px 13px;

    border-bottom:
        1px solid #e5e5e5;

    font-size: 14px;

    color: #244f5f;
}


tr:hover {

    background: #f8fcfd;
}


/* =====================================================
   BADGES
===================================================== */

.badge {

    display: inline-block;

    padding: 7px 13px;

    border-radius: 20px;

    font-size: 12px;

    font-weight: bold;
}


.critical {

    background: #ffe0e0;

    color: #d92323;
}


.high {

    background: #ffe5e5;

    color: #e53935;
}


.medium {

    background: #fff0cf;

    color: #c78300;
}


.active {

    background: #fff0c7;

    color: #9b6b00;
}


.resolved {

    background: #d9f5df;

    color: #18833b;
}


/* =====================================================
   RESOLVE BUTTON
===================================================== */

.resolve-btn {

    background: #16a05d;

    color: white;

    border: none;

    padding: 9px 15px;

    border-radius: 7px;

    cursor: pointer;

    font-weight: bold;

    font-size: 13px;
}


.resolve-btn:hover {

    background: #12864f;
}


/* =====================================================
   ALREADY RESOLVED
===================================================== */

.resolved-text {

    color: #18833b;

    font-weight: bold;

    font-size: 13px;
}


/* =====================================================
   BACK BUTTON
===================================================== */

.back-area {

    margin-top: 30px;
}


.back-btn {

    display: inline-block;

    text-decoration: none;

    background: #0785b2;

    color: white;

    padding: 14px 24px;

    border-radius: 9px;

    font-weight: bold;
}


/* =====================================================
   NO ALERTS
===================================================== */

.no-alerts {

    text-align: center;

    padding: 50px;

    color: #607d8b;

    font-size: 18px;
}


/* =====================================================
   RESPONSIVE
===================================================== */

@media (max-width: 900px) {

    .stats {

        grid-template-columns:
            repeat(2, 1fr);

    }

}


@media (max-width: 600px) {

    .stats {

        grid-template-columns: 1fr;

    }

    .dashboard-link {

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

        Aqua<span class="logo-green">Guard</span>

    </div>


    <div class="header-right">

        <a href="admin/dashboard.php"
           class="dashboard-link">

            ← Dashboard

        </a>


        <a href="index.php"
           class="logout">

            Logout

        </a>

    </div>

</header>



<!-- =====================================================
     MAIN
===================================================== -->

<div class="container">


    <h1 class="page-title">

        🚨 Water Quality Alerts

    </h1>


    <p class="page-subtitle">

        Monitor critical water-quality conditions and active alerts.

    </p>



    <!-- =================================================
         STAT CARDS
    ================================================= -->

    <div class="stats">


        <div class="stat-card">

            <div class="stat-title">

                TOTAL ALERTS

            </div>

            <div class="stat-number">

                <?php
                echo $total_alerts;
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



        <div class="stat-card">

            <div class="stat-title">

                ACTIVE ALERTS

            </div>

            <div class="stat-number">

                <?php
                echo $active_alerts;
                ?>

            </div>

        </div>



        <div class="stat-card">

            <div class="stat-title">

                ✅ RESOLVED ALERTS

            </div>

            <div class="stat-number resolved-number">

                <?php
                echo $resolved_alerts;
                ?>

            </div>

        </div>


    </div>



    <!-- =================================================
         ALERT TABLE
    ================================================= -->

    <div class="alert-section">


        <h2 class="section-title">

            🚨 Water Quality Alerts

        </h2>


        <?php if (count($alerts) > 0): ?>


        <table>


            <thead>

                <tr>

                    <th>Alert ID</th>

                    <th>Area</th>

                    <th>City</th>

                    <th>Reading ID</th>

                    <th>Alert Type</th>

                    <th>Severity</th>

                    <th>Reason</th>

                    <th>Detected At</th>

                    <th>Status</th>

                    <th>Action</th>

                </tr>

            </thead>


            <tbody>


            <?php foreach ($alerts as $alert): ?>


                <tr>


                    <!-- ALERT ID -->

                    <td>

                        #

                        <?php
                        echo htmlspecialchars(
                            $alert['alert_id']
                        );
                        ?>

                    </td>



                    <!-- AREA -->

                    <td>

                        <?php
                        echo htmlspecialchars(
                            $alert['area_name']
                            ?? 'Unknown'
                        );
                        ?>

                    </td>



                    <!-- CITY -->

                    <td>

                        <?php
                        echo htmlspecialchars(
                            $alert['city']
                            ?? 'Unknown'
                        );
                        ?>

                    </td>



                    <!-- READING ID -->

                    <td>

                        #

                        <?php
                        echo htmlspecialchars(
                            $alert['reading_id']
                        );
                        ?>

                    </td>



                    <!-- ALERT TYPE -->

                    <td>

                        <?php
                        echo htmlspecialchars(
                            $alert['alert_type']
                        );
                        ?>

                    </td>



                    <!-- SEVERITY -->

                    <td>


                        <?php

                        $severity =
                            strtoupper(
                                $alert['severity']
                            );


                        if ($severity === 'CRITICAL') {

                            $severity_class =
                                'critical';

                        } elseif ($severity === 'HIGH') {

                            $severity_class =
                                'high';

                        } else {

                            $severity_class =
                                'medium';

                        }

                        ?>


                        <span class="badge
                                     <?php
                                     echo $severity_class;
                                     ?>">

                            <?php
                            echo htmlspecialchars(
                                $severity
                            );
                            ?>

                        </span>


                    </td>



                    <!-- REASON -->

                    <td>

                        <?php
                        echo htmlspecialchars(
                            $alert['reason']
                        );
                        ?>

                    </td>



                    <!-- DETECTED AT -->

                    <td>

                        <?php

                        echo date(
                            "d M Y H:i",
                            strtotime(
                                $alert['detected_at']
                            )
                        );

                        ?>

                    </td>



                    <!-- STATUS -->

                    <td>


                        <?php

                        if (
                            (int)$alert['is_resolved']
                            === 1
                        ):

                        ?>


                            <span class="badge resolved">

                                RESOLVED

                            </span>


                        <?php else: ?>


                            <span class="badge active">

                                ACTIVE

                            </span>


                        <?php endif; ?>


                    </td>



                    <!-- ACTION -->

                    <td>


                        <?php

                        if (
                            (int)$alert['is_resolved']
                            === 0
                        ):

                        ?>


                            <form method="POST"
                                  action="alerts.php"
                                  onsubmit="
                                    return confirm(
                                      'Are you sure you want to resolve this alert?'
                                    );
                                  ">


                                <input
                                    type="hidden"
                                    name="alert_id"
                                    value="<?php
                                    echo htmlspecialchars(
                                        $alert['alert_id']
                                    );
                                    ?>"
                                >


                                <button
                                    type="submit"
                                    name="resolve_alert"
                                    class="resolve-btn"
                                >

                                    ✓ Resolve

                                </button>


                            </form>


                        <?php else: ?>


                            <span class="resolved-text">

                                ✓ Completed

                            </span>


                        <?php endif; ?>


                    </td>


                </tr>


            <?php endforeach; ?>


            </tbody>


        </table>


        <?php else: ?>


            <div class="no-alerts">

                ✅ No water-quality alerts found.

            </div>


        <?php endif; ?>


    </div>



    <!-- =================================================
         BACK BUTTON
    ================================================= -->

    <div class="back-area">

        <a href="admin/dashboard.php"
           class="back-btn">

            ← Back to Admin Dashboard

        </a>

    </div>


</div>


</body>

</html>


<?php

$conn->close();

?>