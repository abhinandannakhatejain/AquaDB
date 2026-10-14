-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 07, 2026 at 02:23 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aquaguard`
--

-- --------------------------------------------------------

--
-- Table structure for table `area_statistics`
--

CREATE TABLE `area_statistics` (
  `statistics_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `total_complaints` int(11) DEFAULT 0,
  `pending_complaints` int(11) DEFAULT 0,
  `resolved_complaints` int(11) DEFAULT 0,
  `risk_level` enum('SAFE','WARNING','CRITICAL') DEFAULT 'SAFE',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `complaint_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `priority` enum('LOW','MEDIUM','HIGH','CRITICAL') DEFAULT 'MEDIUM',
  `status` enum('PENDING','ASSIGNED','IN_PROGRESS','RESOLVED','REJECTED') DEFAULT 'PENDING',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`complaint_id`, `user_id`, `category_id`, `location_id`, `description`, `priority`, `status`, `created_at`, `resolved_at`) VALUES
(1, 2, 1, 1, 'Brown colored water coming from the tap.', 'HIGH', 'ASSIGNED', '2026-10-07 10:50:32', NULL),
(2, 3, 2, 1, 'Water has an unusual smell.', 'HIGH', 'PENDING', '2026-10-07 10:50:32', NULL),
(3, 2, 3, 2, 'Water color appears abnormal.', 'MEDIUM', 'PENDING', '2026-10-07 10:50:32', NULL),
(4, 2, 1, 1, 'Brown colored water is coming from the tap.', 'HIGH', 'RESOLVED', '2026-10-07 11:22:24', '2026-10-07 11:37:54'),
(5, 2, 1, 3, 'the water is so dirty and it is smelling', 'HIGH', 'RESOLVED', '2026-10-07 12:07:14', '2026-10-07 12:10:48');

-- --------------------------------------------------------

--
-- Table structure for table `complaint_assignments`
--

CREATE TABLE `complaint_assignments` (
  `assignment_id` int(11) NOT NULL,
  `complaint_id` int(11) NOT NULL,
  `officer_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaint_assignments`
--

INSERT INTO `complaint_assignments` (`assignment_id`, `complaint_id`, `officer_id`, `assigned_at`) VALUES
(1, 1, 1, '2026-10-07 10:51:13'),
(2, 4, 1, '2026-10-07 11:37:09'),
(3, 5, 1, '2026-10-07 12:09:34');

-- --------------------------------------------------------

--
-- Table structure for table `complaint_categories`
--

CREATE TABLE `complaint_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaint_categories`
--

INSERT INTO `complaint_categories` (`category_id`, `category_name`, `description`) VALUES
(1, 'Dirty Water', 'Water appears dirty or muddy'),
(2, 'Bad Smell', 'Unusual or unpleasant smell'),
(3, 'Unusual Color', 'Water has abnormal color'),
(4, 'Low Pressure', 'Low water pressure'),
(5, 'Contamination', 'Possible contamination'),
(6, 'Other', 'Other water-related problem');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `complaint_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `location_id` int(11) NOT NULL,
  `area_name` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`location_id`, `area_name`, `city`, `latitude`, `longitude`) VALUES
(1, 'Katpadi', 'Vellore', 12.9692000, 79.1559000),
(2, 'Sathuvachari', 'Vellore', 12.9235000, 79.1325000),
(3, 'Gandhi Nagar', 'Vellore', 12.9300000, 79.1400000),
(4, 'Ranipet', 'Ranipet', 12.9249000, 79.3333000),
(5, 'Bagayam', 'Vellore', 12.9100000, 79.1300000);

-- --------------------------------------------------------

--
-- Table structure for table `officers`
--

CREATE TABLE `officers` (
  `officer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `designation` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `officers`
--

INSERT INTO `officers` (`officer_id`, `user_id`, `department`, `designation`) VALUES
(1, 4, 'Water Supply Department', 'Field Officer');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('CITIZEN','ADMIN','OFFICER') NOT NULL DEFAULT 'CITIZEN',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES
(1, 'Admin User', 'admin@aquaguard.com', '9000000001', 'admin123', 'ADMIN', '2026-10-07 10:41:02'),
(2, 'Rahul Sharma', 'rahul@gmail.com', '9000000002', '123456', 'CITIZEN', '2026-10-07 10:41:02'),
(3, 'Priya Kumar', 'priya@gmail.com', '9000000003', '123456', 'CITIZEN', '2026-10-07 10:41:02'),
(4, 'Water Officer 1', 'officer@aquaguard.com', '9000000004', 'officer123', 'OFFICER', '2026-10-07 10:41:02');

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_complaint_dashboard`
-- (See below for the actual view)
--
CREATE TABLE `vw_complaint_dashboard` (
`complaint_id` int(11)
,`citizen` varchar(100)
,`category_name` varchar(100)
,`area_name` varchar(100)
,`city` varchar(100)
,`description` text
,`priority` enum('LOW','MEDIUM','HIGH','CRITICAL')
,`status` enum('PENDING','ASSIGNED','IN_PROGRESS','RESOLVED','REJECTED')
,`created_at` timestamp
,`resolved_at` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_water_dashboard`
-- (See below for the actual view)
--
CREATE TABLE `vw_water_dashboard` (
`reading_id` int(11)
,`area_name` varchar(100)
,`city` varchar(100)
,`ph` decimal(4,2)
,`turbidity` decimal(6,2)
,`tds` decimal(7,2)
,`temperature` decimal(5,2)
,`risk_level` enum('SAFE','WARNING','CRITICAL')
,`recorded_at` timestamp
);

-- --------------------------------------------------------

--
-- Table structure for table `water_alerts`
--

CREATE TABLE `water_alerts` (
  `alert_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `reading_id` int(11) NOT NULL,
  `alert_type` varchar(100) NOT NULL,
  `severity` enum('WARNING','CRITICAL') NOT NULL,
  `reason` text NOT NULL,
  `detected_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_resolved` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `water_alerts`
--

INSERT INTO `water_alerts` (`alert_id`, `location_id`, `reading_id`, `alert_type`, `severity`, `reason`, `detected_at`, `is_resolved`) VALUES
(1, 1, 2, 'WATER QUALITY ANOMALY', 'CRITICAL', 'Critical water-quality anomaly detected. pH=5.70, Turbidity=16.20, TDS=1150.00. Recent complaints in this area: 0', '2026-10-07 10:47:33', 1);

-- --------------------------------------------------------

--
-- Table structure for table `water_quality_readings`
--

CREATE TABLE `water_quality_readings` (
  `reading_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `ph` decimal(4,2) NOT NULL,
  `turbidity` decimal(6,2) NOT NULL,
  `tds` decimal(7,2) NOT NULL,
  `temperature` decimal(5,2) DEFAULT NULL,
  `risk_level` enum('SAFE','WARNING','CRITICAL') DEFAULT 'SAFE',
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `water_quality_readings`
--

INSERT INTO `water_quality_readings` (`reading_id`, `location_id`, `ph`, `turbidity`, `tds`, `temperature`, `risk_level`, `recorded_at`) VALUES
(1, 1, 7.20, 2.50, 350.00, 27.50, 'SAFE', '2026-10-07 10:45:04'),
(2, 1, 5.70, 16.20, 1150.00, 29.00, 'CRITICAL', '2026-10-07 10:47:33');

--
-- Triggers `water_quality_readings`
--
DELIMITER $$
CREATE TRIGGER `trg_after_water_reading` AFTER INSERT ON `water_quality_readings` FOR EACH ROW BEGIN

    DECLARE complaint_count INT DEFAULT 0;
    DECLARE alert_severity VARCHAR(20);
    DECLARE alert_reason TEXT;

    SELECT COUNT(*)
    INTO complaint_count
    FROM complaints
    WHERE location_id = NEW.location_id
      AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY);

    IF NEW.risk_level = 'CRITICAL' THEN

        SET alert_severity = 'CRITICAL';

        SET alert_reason = CONCAT(
            'Critical water-quality anomaly detected. ',
            'pH=', NEW.ph,
            ', Turbidity=', NEW.turbidity,
            ', TDS=', NEW.tds,
            '. Recent complaints in this area: ',
            complaint_count
        );

        INSERT INTO water_alerts
        (
            location_id,
            reading_id,
            alert_type,
            severity,
            reason
        )
        VALUES
        (
            NEW.location_id,
            NEW.reading_id,
            'WATER QUALITY ANOMALY',
            alert_severity,
            alert_reason
        );

    ELSEIF NEW.risk_level = 'WARNING'
            AND complaint_count >= 3 THEN

        SET alert_severity = 'WARNING';

        SET alert_reason = CONCAT(
            'Warning-level water quality detected with ',
            complaint_count,
            ' recent complaints in this area. ',
            'pH=', NEW.ph,
            ', Turbidity=', NEW.turbidity,
            ', TDS=', NEW.tds
        );

        INSERT INTO water_alerts
        (
            location_id,
            reading_id,
            alert_type,
            severity,
            reason
        )
        VALUES
        (
            NEW.location_id,
            NEW.reading_id,
            'WATER QUALITY + COMPLAINT ANOMALY',
            alert_severity,
            alert_reason
        );

    END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_before_water_reading` BEFORE INSERT ON `water_quality_readings` FOR EACH ROW BEGIN

    IF NEW.ph < 6
       OR NEW.ph > 9
       OR NEW.turbidity > 10
       OR NEW.tds > 1000 THEN

        SET NEW.risk_level = 'CRITICAL';

    ELSEIF NEW.ph < 6.5
       OR NEW.ph > 8.5
       OR NEW.turbidity > 5
       OR NEW.tds > 500 THEN

        SET NEW.risk_level = 'WARNING';

    ELSE

        SET NEW.risk_level = 'SAFE';

    END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure for view `vw_complaint_dashboard`
--
DROP TABLE IF EXISTS `vw_complaint_dashboard`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_complaint_dashboard`  AS SELECT `c`.`complaint_id` AS `complaint_id`, `u`.`name` AS `citizen`, `cc`.`category_name` AS `category_name`, `l`.`area_name` AS `area_name`, `l`.`city` AS `city`, `c`.`description` AS `description`, `c`.`priority` AS `priority`, `c`.`status` AS `status`, `c`.`created_at` AS `created_at`, `c`.`resolved_at` AS `resolved_at` FROM (((`complaints` `c` join `users` `u` on(`c`.`user_id` = `u`.`user_id`)) join `complaint_categories` `cc` on(`c`.`category_id` = `cc`.`category_id`)) join `locations` `l` on(`c`.`location_id` = `l`.`location_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `vw_water_dashboard`
--
DROP TABLE IF EXISTS `vw_water_dashboard`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_water_dashboard`  AS SELECT `w`.`reading_id` AS `reading_id`, `l`.`area_name` AS `area_name`, `l`.`city` AS `city`, `w`.`ph` AS `ph`, `w`.`turbidity` AS `turbidity`, `w`.`tds` AS `tds`, `w`.`temperature` AS `temperature`, `w`.`risk_level` AS `risk_level`, `w`.`recorded_at` AS `recorded_at` FROM (`water_quality_readings` `w` join `locations` `l` on(`w`.`location_id` = `l`.`location_id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `area_statistics`
--
ALTER TABLE `area_statistics`
  ADD PRIMARY KEY (`statistics_id`),
  ADD UNIQUE KEY `location_id` (`location_id`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`complaint_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_complaints_status` (`status`),
  ADD KEY `idx_complaints_location` (`location_id`),
  ADD KEY `idx_complaints_created` (`created_at`);

--
-- Indexes for table `complaint_assignments`
--
ALTER TABLE `complaint_assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `complaint_id` (`complaint_id`),
  ADD KEY `officer_id` (`officer_id`);

--
-- Indexes for table `complaint_categories`
--
ALTER TABLE `complaint_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `complaint_id` (`complaint_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `officers`
--
ALTER TABLE `officers`
  ADD PRIMARY KEY (`officer_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Indexes for table `water_alerts`
--
ALTER TABLE `water_alerts`
  ADD PRIMARY KEY (`alert_id`),
  ADD KEY `reading_id` (`reading_id`),
  ADD KEY `idx_alert_location` (`location_id`),
  ADD KEY `idx_alert_severity` (`severity`);

--
-- Indexes for table `water_quality_readings`
--
ALTER TABLE `water_quality_readings`
  ADD PRIMARY KEY (`reading_id`),
  ADD KEY `idx_water_location` (`location_id`),
  ADD KEY `idx_water_recorded` (`recorded_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `area_statistics`
--
ALTER TABLE `area_statistics`
  MODIFY `statistics_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `complaint_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `complaint_assignments`
--
ALTER TABLE `complaint_assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `complaint_categories`
--
ALTER TABLE `complaint_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `officers`
--
ALTER TABLE `officers`
  MODIFY `officer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `water_alerts`
--
ALTER TABLE `water_alerts`
  MODIFY `alert_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `water_quality_readings`
--
ALTER TABLE `water_quality_readings`
  MODIFY `reading_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `area_statistics`
--
ALTER TABLE `area_statistics`
  ADD CONSTRAINT `area_statistics_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `locations` (`location_id`);

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `complaints_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `complaint_categories` (`category_id`),
  ADD CONSTRAINT `complaints_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `locations` (`location_id`);

--
-- Constraints for table `complaint_assignments`
--
ALTER TABLE `complaint_assignments`
  ADD CONSTRAINT `complaint_assignments_ibfk_1` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`complaint_id`),
  ADD CONSTRAINT `complaint_assignments_ibfk_2` FOREIGN KEY (`officer_id`) REFERENCES `officers` (`officer_id`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`complaint_id`),
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `officers`
--
ALTER TABLE `officers`
  ADD CONSTRAINT `officers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `water_alerts`
--
ALTER TABLE `water_alerts`
  ADD CONSTRAINT `water_alerts_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `locations` (`location_id`),
  ADD CONSTRAINT `water_alerts_ibfk_2` FOREIGN KEY (`reading_id`) REFERENCES `water_quality_readings` (`reading_id`);

--
-- Constraints for table `water_quality_readings`
--
ALTER TABLE `water_quality_readings`
  ADD CONSTRAINT `water_quality_readings_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `locations` (`location_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
