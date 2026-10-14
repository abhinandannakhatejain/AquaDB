# AquaGuard — Water Quality Monitoring & Complaint Management System

**Team Name:** AquaDB

## Project Overview

AquaGuard is a database-driven web application designed to connect water-quality monitoring, citizen complaints, and administrative response in one platform.

The system stores water-quality readings and supports complaint submission, complaint tracking, officer assignment, and administrative management. It provides a centralized database for water-related information to help organize monitoring and complaint handling.

## Features

- Citizen login and complaint submission
- Complaint tracking and status management
- Admin dashboard and complaint management
- Assignment of complaints to water officers
- Water-quality readings for pH, turbidity, total dissolved solids (TDS), and temperature
- Storage and management of critical water-quality alerts
- MySQL database integration
- Database views for water-quality and complaint dashboards

## Technology Stack

| Component | Technology |
|---|---|
| Frontend | HTML, CSS |
| Backend | PHP |
| Database | MySQL / MariaDB |
| Database access | PHP PDO |
| Local development environment | XAMPP |
| Database administration | phpMyAdmin |
| Version control | Git and GitHub |

## Project Structure

- `admin/` — administrative pages and complaint management
- `citizen/` — citizen login, dashboard, and complaint submission
- `config/` — database connection configuration
- `index.php` — application entry page
- `alerts.php` — water-quality alert page
- `water_quality.php` — water-quality functionality
- `aquaguard_backup.sql` — database backup containing database definitions and data
- `test.php` — application testing page
- `README.md` — project documentation

## Database Overview

The database contains tables for users, locations, complaint categories, complaints, officers, complaint assignments, water-quality readings, water alerts, feedback, and area statistics.

It also includes the `vw_complaint_dashboard` and `vw_water_dashboard` views. Database triggers support water-reading processing and alert-related operations. Refer to `aquaguard_backup.sql` for the actual database definitions.

## Setup Instructions

### Requirements

- XAMPP with Apache and MySQL/MariaDB
- A web browser
- The AquaGuard project files

### Installation

1. Install XAMPP and start Apache and MySQL/MariaDB.
2. Copy the project folder into the XAMPP `htdocs` directory.
3. Create a database named `aquaguard` using phpMyAdmin.
4. Select the database and import `aquaguard_backup.sql`.
5. Open `config/database.php` and verify the database connection settings.
6. Open the application in a browser. If the project folder is named `aquaguard`, the usual local address is `http://localhost/aquaguard/`.

The exact database host, port, username, and password depend on the local environment.

## Testing

The database implementation includes SQL operations for:

- CRUD operations on database records
- JOIN queries combining complaint, citizen, category, location, and officer information
- Aggregate queries for area-wise complaint statistics
- Dashboard view queries
- Water-quality trigger demonstrations
- Transaction handling using COMMIT and ROLLBACK
- Query execution-plan inspection using EXPLAIN
- Stored-procedure execution and verification

The query documentation and corresponding result screenshots can be used as evidence of the database testing performed.

## Team Members

1. Mohammed Faizan — 24BCE2617
2. Ashad Rizwan — 24BCE0431
3. Abhinandan Nakhate Jain — 24BCE2872

## Future Scope

- Integration with IoT sensors for real-time water-quality monitoring
- SMS and email notifications for critical alerts and complaint updates
- Map-based visualization of monitoring locations
- Analytics for complaint trends and area-wise water statistics

---

Developed by **Team AquaDB** as a DBMS hackathon project.
