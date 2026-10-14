<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AquaGuard | Water Quality & Complaint Management</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f4f9fc;
            color: #123;
        }

        .navbar {
            height: 70px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 7%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #087ea4;
        }

        .logo span {
            color: #00a896;
        }

        .nav-buttons a {
            text-decoration: none;
            padding: 11px 20px;
            margin-left: 10px;
            border-radius: 8px;
            font-weight: bold;
        }

        .citizen {
            color: #087ea4;
            border: 1px solid #087ea4;
        }

        .admin {
            background: #087ea4;
            color: white;
        }

        .hero {
            min-height: 520px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 60px 8%;
            background: linear-gradient(135deg, #e8f8ff, #f5fffc);
        }

        .hero-text {
            max-width: 600px;
        }

        .hero-text h1 {
            font-size: 52px;
            line-height: 1.1;
            margin-bottom: 20px;
            color: #073b4c;
        }

        .hero-text h1 span {
            color: #00a896;
        }

        .hero-text p {
            font-size: 19px;
            line-height: 1.7;
            color: #52616b;
            margin-bottom: 30px;
        }

        .hero-buttons a {
            display: inline-block;
            text-decoration: none;
            padding: 14px 25px;
            border-radius: 9px;
            margin-right: 10px;
            font-weight: bold;
        }

        .primary {
            background: #087ea4;
            color: white;
        }

        .secondary {
            border: 2px solid #087ea4;
            color: #087ea4;
        }

        .water-icon {
            font-size: 180px;
            filter: drop-shadow(0 10px 15px rgba(0,0,0,0.12));
        }

        .features {
            padding: 60px 8%;
            text-align: center;
        }

        .features h2 {
            font-size: 34px;
            margin-bottom: 40px;
            color: #073b4c;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.07);
        }

        .card-icon {
            font-size: 45px;
            margin-bottom: 15px;
        }

        .card h3 {
            margin-bottom: 12px;
            color: #087ea4;
        }

        .card p {
            color: #65727b;
            line-height: 1.6;
        }

        .footer {
            background: #073b4c;
            color: white;
            text-align: center;
            padding: 25px;
            margin-top: 30px;
        }

        @media(max-width: 800px) {

            .hero {
                flex-direction: column;
                text-align: center;
            }

            .hero-text h1 {
                font-size: 40px;
            }

            .cards {
                grid-template-columns: 1fr;
            }

            .water-icon {
                font-size: 100px;
                margin-top: 30px;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar">

        <div class="logo">
            💧 Aqua<span>Guard</span>
        </div>

        <div class="nav-buttons">
            <a href="citizen/login.php" class="citizen">
                Citizen Login
            </a>

            <a href="admin/login.php" class="admin">
                Admin Login
            </a>
        </div>

    </nav>


    <section class="hero">

        <div class="hero-text">

            <h1>
                Smart Water<br>
                <span>Monitoring System</span>
            </h1>

            <p>
                AquaGuard connects water-quality monitoring
                with citizen complaints to help authorities
                identify unsafe water conditions faster.
            </p>

            <div class="hero-buttons">

                <a href="citizen/login.php" class="primary">
                    Report a Problem
                </a>

                <a href="admin/login.php" class="secondary">
                    View Dashboard
                </a>

            </div>

        </div>


        <div class="water-icon">
            💧
        </div>

    </section>


    <section class="features">

        <h2>What AquaGuard Does</h2>

        <div class="cards">

            <div class="card">

                <div class="card-icon">
                    🧪
                </div>

                <h3>
                    Water Quality Monitoring
                </h3>

                <p>
                    Monitor pH, turbidity, TDS and
                    temperature to identify unsafe
                    water conditions.
                </p>

            </div>


            <div class="card">

                <div class="card-icon">
                    📢
                </div>

                <h3>
                    Citizen Complaints
                </h3>

                <p>
                    Citizens can report water-related
                    problems and track their complaints.
                </p>

            </div>


            <div class="card">

                <div class="card-icon">
                    🚨
                </div>

                <h3>
                    Automatic Alerts
                </h3>

                <p>
                    Database triggers automatically
                    detect critical readings and
                    generate alerts.
                </p>

            </div>

        </div>

    </section>


    <footer class="footer">

        <p>
            © 2026 AquaGuard — Smart Water Quality
            & Complaint Management System
        </p>

    </footer>

</body>
</html>