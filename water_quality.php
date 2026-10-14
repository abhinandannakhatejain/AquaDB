<?php

/* =========================
   DATABASE CONNECTION
   ========================= */

$conn = new mysqli("localhost", "root", "", "aquaguard");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}


/* =========================
   WATER QUALITY COUNTS
   ========================= */

// Total readings
$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM water_quality_readings
");

$total_readings = $result->fetch_assoc()['total'];


// Safe readings
$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM water_quality_readings
    WHERE risk_level = 'SAFE'
");

$safe_readings = $result->fetch_assoc()['total'];


// Warning readings
$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM water_quality_readings
    WHERE risk_level = 'WARNING'
");

$warning_readings = $result->fetch_assoc()['total'];


// Critical readings
$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM water_quality_readings
    WHERE risk_level = 'CRITICAL'
");

$critical_readings = $result->fetch_assoc()['total'];


/* =========================
   LATEST WATER READINGS
   ========================= */

$readings = $conn->query("
    SELECT
        w.reading_id,
        w.location_id,
        l.area_name,
        l.city,
        w.ph,
        w.turbidity,
        w.tds,
        w.temperature,
        w.risk_level,
        w.recorded_at
    FROM water_quality_readings w
    LEFT JOIN locations l
        ON w.location_id = l.location_id
    ORDER BY w.reading_id DESC
");


/* =========================
   LATEST READING
   ========================= */

$latest = $conn->query("
    SELECT
        w.reading_id,
        l.area_name,
        l.city,
        w.ph,
        w.turbidity,
        w.tds,
        w.temperature,
        w.risk_level,
        w.recorded_at
    FROM water_quality_readings w
    LEFT JOIN locations l
        ON w.location_id = l.location_id
    ORDER BY w.reading_id DESC
    LIMIT 1
");

$latest_data = null;

if ($latest && $latest->num_rows > 0) {
    $latest_data = $latest->fetch_assoc();
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>AquaGuard - Water Quality</title>


<style>

/* =========================
   GENERAL
   ========================= */

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    background: #eef8fc;
    color: #123f50;
}


/* =========================
   HEADER
   ========================= */

.header {

    height: 90px;

    background: white;

    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 0 5%;

    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}


.logo {

    font-size: 30px;

    font-weight: bold;

    color: #0785ad;
}


.logo span {

    color: #08a997;

}


.header-right {

    display: flex;

    align-items: center;

    gap: 25px;

}


.dashboard-link {

    text-decoration: none;

    color: #0785ad;

    font-weight: bold;

    font-size: 17px;

}


.logout {

    background: #ef5350;

    color: white;

    text-decoration: none;

    padding: 13px 25px;

    border-radius: 8px;

    font-weight: bold;

}


.logout:hover {

    background: #d84343;

}


/* =========================
   MAIN CONTAINER
   ========================= */

.container {

    width: 90%;

    max-width: 1450px;

    margin: 45px auto;

}


.title {

    font-size: 38px;

    font-weight: bold;

    margin-bottom: 10px;

}


.subtitle {

    font-size: 18px;

    color: #617985;

    margin-bottom: 35px;

}


/* =========================
   STAT CARDS
   ========================= */

.stats {

    display: grid;

    grid-template-columns: repeat(4, 1fr);

    gap: 20px;

    margin-bottom: 35px;

}


.card {

    background: white;

    border-radius: 18px;

    padding: 28px;

    min-height: 135px;

    box-shadow: 0 8px 25px rgba(0,0,0,0.07);

}


.card-title {

    color: #607985;

    font-size: 16px;

    font-weight: bold;

    margin-bottom: 18px;

}


.card-number {

    font-size: 40px;

    font-weight: bold;

    color: #0785ad;

}


.safe-number {

    color: #16843d;

}


.warning-number {

    color: #ed8b00;

}


.critical-number {

    color: #d71919;

}


/* =========================
   LATEST READING
   ========================= */

.latest-box {

    background: white;

    border-radius: 18px;

    padding: 30px;

    margin-bottom: 35px;

    box-shadow: 0 8px 25px rgba(0,0,0,0.07);

}


.latest-title {

    font-size: 27px;

    margin-bottom: 25px;

}


.latest-grid {

    display: grid;

    grid-template-columns: repeat(6, 1fr);

    gap: 15px;

}


.measurement {

    background: #f3fafc;

    border-radius: 12px;

    padding: 20px;

}


.measurement-title {

    color: #617985;

    font-size: 14px;

    font-weight: bold;

    margin-bottom: 10px;

}


.measurement-value {

    font-size: 25px;

    font-weight: bold;

    color: #0785ad;

}


/* =========================
   TABLE
   ========================= */

.table-section {

    background: white;

    border-radius: 18px;

    padding: 30px;

    box-shadow: 0 8px 25px rgba(0,0,0,0.07);

}


.table-title {

    font-size: 28px;

    margin-bottom: 25px;

}


.table-container {

    overflow-x: auto;

}


table {

    width: 100%;

    border-collapse: collapse;

}


thead {

    background: #e5f5fa;

}


th {

    text-align: left;

    padding: 17px 14px;

    font-size: 15px;

}


td {

    padding: 17px 14px;

    border-bottom: 1px solid #e7eef1;

    font-size: 15px;

}


tr:hover {

    background: #f8fcfd;

}


/* =========================
   RISK BADGES
   ========================= */

.risk {

    display: inline-block;

    padding: 8px 15px;

    border-radius: 20px;

    font-size: 13px;

    font-weight: bold;

}


.risk-safe {

    background: #d9f5df;

    color: #15803d;

}


.risk-warning {

    background: #fff0c5;

    color: #9a6a00;

}


.risk-critical {

    background: #ffdede;

    color: #c62828;

}


/* =========================
   BACK BUTTON
   ========================= */

.back-button {

    display: inline-block;

    margin-top: 30px;

    background: #0789b5;

    color: white;

    text-decoration: none;

    padding: 14px 25px;

    border-radius: 8px;

    font-weight: bold;

}


.back-button:hover {

    background: #056f94;

}


/* =========================
   RESPONSIVE
   ========================= */

@media (max-width: 1100px) {

    .stats {

        grid-template-columns: repeat(2, 1fr);

    }

    .latest-grid {

        grid-template-columns: repeat(3, 1fr);

    }

}


@media (max-width: 700px) {

    .stats {

        grid-template-columns: 1fr;

    }

    .latest-grid {

        grid-template-columns: repeat(2, 1fr);

    }

    .header {

        padding: 0 20px;

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


<!-- =========================
     HEADER
     ========================= -->

<header class="header">

    <div class="logo">

        💧 Aqua<span>Guard</span>

    </div>


    <div class="header-right">

        <a href="admin/dashboard.php"
           class="dashboard-link">

            ← Admin Dashboard

        </a>


        <a href="admin/logout.php"
           class="logout">

            Logout

        </a>

    </div>

</header>



<!-- =========================
     MAIN
     ========================= -->

<main class="container">


    <h1 class="title">

        💧 Water Quality Monitoring

    </h1>


    <p class="subtitle">

        Monitor water quality readings and identify unsafe water conditions.

    </p>



    <!-- =========================
         STATISTICS
         ========================= -->

    <section class="stats">


        <div class="card">

            <div class="card-title">

                TOTAL READINGS

            </div>

            <div class="card-number">

                <?php echo $total_readings; ?>

            </div>

        </div>



        <div class="card">

            <div class="card-title">

                🟢 SAFE

            </div>

            <div class="card-number safe-number">

                <?php echo $safe_readings; ?>

            </div>

        </div>



        <div class="card">

            <div class="card-title">

                🟡 WARNING

            </div>

            <div class="card-number warning-number">

                <?php echo $warning_readings; ?>

            </div>

        </div>



        <div class="card">

            <div class="card-title">

                🔴 CRITICAL

            </div>

            <div class="card-number critical-number">

                <?php echo $critical_readings; ?>

            </div>

        </div>


    </section>



    <!-- =========================
         LATEST READING
         ========================= -->

    <?php if ($latest_data) { ?>


    <section class="latest-box">


        <h2 class="latest-title">

            📊 Latest Water Quality Reading

        </h2>


        <p style="margin-bottom:20px; color:#617985;">

            📍

            <?php echo htmlspecialchars($latest_data['area_name']); ?>

            ,

            <?php echo htmlspecialchars($latest_data['city']); ?>

            &nbsp; | &nbsp;

            Reading #

            <?php echo htmlspecialchars($latest_data['reading_id']); ?>

        </p>



        <div class="latest-grid">


            <div class="measurement">

                <div class="measurement-title">

                    pH

                </div>

                <div class="measurement-value">

                    <?php echo htmlspecialchars($latest_data['ph']); ?>

                </div>

            </div>



            <div class="measurement">

                <div class="measurement-title">

                    TURBIDITY

                </div>

                <div class="measurement-value">

                    <?php echo htmlspecialchars($latest_data['turbidity']); ?>

                </div>

            </div>



            <div class="measurement">

                <div class="measurement-title">

                    TDS

                </div>

                <div class="measurement-value">

                    <?php echo htmlspecialchars($latest_data['tds']); ?>

                </div>

            </div>



            <div class="measurement">

                <div class="measurement-title">

                    TEMPERATURE

                </div>

                <div class="measurement-value">

                    <?php echo htmlspecialchars($latest_data['temperature']); ?> °C

                </div>

            </div>



            <div class="measurement">

                <div class="measurement-title">

                    RISK LEVEL

                </div>

                <div>

                    <?php

                    $latest_risk = strtolower($latest_data['risk_level']);

                    ?>

                    <span class="risk risk-<?php echo $latest_risk; ?>">

                        <?php echo htmlspecialchars($latest_data['risk_level']); ?>

                    </span>

                </div>

            </div>



            <div class="measurement">

                <div class="measurement-title">

                    RECORDED

                </div>

                <div style="font-size:16px; font-weight:bold;">

                    <?php

                    echo date(
                        "d M Y",
                        strtotime($latest_data['recorded_at'])
                    );

                    ?>

                    <br>

                    <?php

                    echo date(
                        "H:i:s",
                        strtotime($latest_data['recorded_at'])
                    );

                    ?>

                </div>

            </div>


        </div>


    </section>


    <?php } ?>



    <!-- =========================
         ALL READINGS
         ========================= -->

    <section class="table-section">


        <h2 class="table-title">

            📋 Water Quality Readings

        </h2>


        <div class="table-container">


            <table>


                <thead>

                    <tr>

                        <th>
                            Reading ID
                        </th>

                        <th>
                            Area
                        </th>

                        <th>
                            City
                        </th>

                        <th>
                            pH
                        </th>

                        <th>
                            Turbidity
                        </th>

                        <th>
                            TDS
                        </th>

                        <th>
                            Temperature
                        </th>

                        <th>
                            Risk Level
                        </th>

                        <th>
                            Recorded At
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php

                if ($readings && $readings->num_rows > 0) {

                    while ($row = $readings->fetch_assoc()) {

                        $risk =
                            strtolower($row['risk_level']);

                ?>


                    <tr>


                        <td>

                            #

                            <?php

                            echo htmlspecialchars(
                                $row['reading_id']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['area_name']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['city']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['ph']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['turbidity']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['tds']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['temperature']
                            );

                            ?>

                            °C

                        </td>


                        <td>

                            <span class="risk risk-<?php echo $risk; ?>">

                                <?php

                                echo htmlspecialchars(
                                    $row['risk_level']
                                );

                                ?>

                            </span>

                        </td>


                        <td>

                            <?php

                            echo date(
                                "d M Y H:i",
                                strtotime(
                                    $row['recorded_at']
                                )
                            );

                            ?>

                        </td>


                    </tr>


                <?php

                    }

                } else {

                ?>


                    <tr>

                        <td colspan="9"
                            style="text-align:center;">

                            No water quality readings found.

                        </td>

                    </tr>


                <?php

                }

                ?>


                </tbody>


            </table>


        </div>


    </section>



    <a href="admin/dashboard.php"
       class="back-button">

        ← Back to Admin Dashboard

    </a>


</main>


</body>

</html>


<?php

$conn->close();

?>