-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 27, 2026 at 11:03 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `campus_animal_adoption`
--

-- --------------------------------------------------------

--
-- Table structure for table `adoption_request`
--

CREATE TABLE `adoption_request` (
  `adoption_id` int(11) NOT NULL,
  `animal_id` int(11) NOT NULL,
  `report_id` int(11) DEFAULT NULL,
  `adopter_id` int(11) NOT NULL,
  `food_habit` varchar(500) NOT NULL,
  `home_details` text DEFAULT NULL,
  `request_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `animal`
--

CREATE TABLE `animal` (
  `animal_id` int(11) NOT NULL,
  `species` varchar(100) NOT NULL,
  `location_found` varchar(500) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `date_registered` date NOT NULL,
  `gender` varchar(50) NOT NULL,
  `name` varchar(200) NOT NULL,
  `status` enum('reported','rescued','under_treatment','available','adopted') NOT NULL DEFAULT 'reported',
  `pattern` varchar(200) DEFAULT NULL,
  `body_colour` varchar(100) DEFAULT NULL,
  `eye_colour` varchar(100) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `animal  status`
--

CREATE TABLE `animal  status` (
  `animal_ID` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `animal_status_history`
--

CREATE TABLE `animal_status_history` (
  `status_id` int(11) NOT NULL,
  `animal_id` int(11) NOT NULL,
  `status` varchar(100) NOT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `changed_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donation item details`
--

CREATE TABLE `donation item details` (
  `donation_ID` int(11) NOT NULL,
  `item_details` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donation purpose`
--

CREATE TABLE `donation purpose` (
  `donation_ID` int(11) NOT NULL,
  `purpose` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `donation_id` int(11) NOT NULL,
  `donor_id` int(11) NOT NULL,
  `status` enum('pending','received','cancelled') NOT NULL DEFAULT 'received',
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `donation_date` date NOT NULL,
  `receipt` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donation type`
--

CREATE TABLE `donation type` (
  `donation_ID` int(11) NOT NULL,
  `donation_type` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donation_details`
--

CREATE TABLE `donation_details` (
  `donation_detail_id` int(11) NOT NULL,
  `donation_id` int(11) NOT NULL,
  `donation_type` enum('money','food','medicine','supplies','other') NOT NULL DEFAULT 'money',
  `purpose` varchar(500) DEFAULT NULL,
  `item_details` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donation_usage`
--

CREATE TABLE `donation_usage` (
  `expense_id` int(11) NOT NULL,
  `donation_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense description`
--

CREATE TABLE `expense description` (
  `expense_ID` int(11) NOT NULL,
  `description` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `expense_id` int(11) NOT NULL,
  `paid_by` int(11) NOT NULL,
  `animal_id` int(11) DEFAULT NULL,
  `expense_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` varchar(500) NOT NULL,
  `expense_type` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense type`
--

CREATE TABLE `expense type` (
  `expense_ID` int(11) NOT NULL,
  `expense_type` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feeding schedule notes`
--

CREATE TABLE `feeding schedule notes` (
  `feeding_ID` int(11) NOT NULL,
  `food consumed` varchar(200) NOT NULL,
  `behavior` varchar(200) NOT NULL,
  `observation` varchar(200) NOT NULL,
  `others` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feeding_notes`
--

CREATE TABLE `feeding_notes` (
  `note_id` int(11) NOT NULL,
  `feeding_id` int(11) NOT NULL,
  `food_consumed` varchar(200) DEFAULT NULL,
  `behavior` varchar(200) DEFAULT NULL,
  `observation` varchar(500) DEFAULT NULL,
  `others` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feeding_schedule`
--

CREATE TABLE `feeding_schedule` (
  `feeding_id` int(11) NOT NULL,
  `animal_id` int(11) NOT NULL,
  `food_type` varchar(200) NOT NULL,
  `quantity` varchar(100) NOT NULL,
  `feeding_time` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `provides`
--

CREATE TABLE `provides` (
  `UID` int(11) NOT NULL,
  `RID` int(11) NOT NULL,
  `AID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `reported_by` int(11) NOT NULL,
  `animal_id` int(11) DEFAULT NULL,
  `handled_by` int(11) DEFAULT NULL,
  `report_type` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(500) NOT NULL,
  `report_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('open','in_progress','closed') NOT NULL DEFAULT 'open'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_type`
--

CREATE TABLE `report_type` (
  `report_type_id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `type` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `treatment`
--

CREATE TABLE `treatment` (
  `treatment_id` int(11) NOT NULL,
  `vet_id` int(11) NOT NULL,
  `animal_id` int(11) NOT NULL,
  `treatment_time` datetime NOT NULL,
  `treatment_type` enum('vaccination','surgery','medication','other') NOT NULL,
  `status` enum('ongoing','completed','cancelled') NOT NULL DEFAULT 'ongoing',
  `medication` varchar(200) DEFAULT NULL,
  `others` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `treatment_description`
--

CREATE TABLE `treatment_description` (
  `description_id` int(11) NOT NULL,
  `treatment_id` int(11) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `treats`
--

CREATE TABLE `treats` (
  `VID` int(11) NOT NULL,
  `TID` int(11) NOT NULL,
  `ANID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `used for`
--

CREATE TABLE `used for` (
  `expense_ID` int(11) NOT NULL,
  `donation_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `bracu_id` varchar(50) DEFAULT NULL,
  `join_date` date NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('generalUser','volunteer') NOT NULL DEFAULT 'generalUser',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vet`
--

CREATE TABLE `vet` (
  `vet_ID` int(11) NOT NULL,
  `availibility_status` varchar(100) NOT NULL,
  `specialization` varchar(200) NOT NULL,
  `qualification` varchar(200) NOT NULL,
  `experienced years` int(11) NOT NULL,
  `email` int(11) NOT NULL,
  `Vet_Name` varchar(200) NOT NULL,
  `license_no` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vet phone number`
--

CREATE TABLE `vet phone number` (
  `Vet_ID` int(11) NOT NULL,
  `Phone_numbers` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vets`
--

CREATE TABLE `vets` (
  `vet_id` int(11) NOT NULL,
  `vet_name` varchar(200) NOT NULL,
  `availability_status` varchar(100) NOT NULL DEFAULT 'Available',
  `specialization` varchar(200) NOT NULL,
  `qualification` varchar(200) NOT NULL,
  `experienced_years` int(11) NOT NULL DEFAULT 0,
  `email` varchar(200) NOT NULL,
  `license_no` varchar(100) NOT NULL,
  `phone_number` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `volunteer_cases _handled`
--

CREATE TABLE `volunteer_cases _handled` (
  `user_ID` int(11) NOT NULL,
  `numberOfCasesHandled` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `volunteer_stats`
--

CREATE TABLE `volunteer_stats` (
  `user_id` int(11) NOT NULL,
  `number_of_cases_handled` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adoption_request`
--
ALTER TABLE `adoption_request`
  ADD PRIMARY KEY (`adoption_id`),
  ADD KEY `fk_adoption_animal` (`animal_id`),
  ADD KEY `fk_adoption_report` (`report_id`),
  ADD KEY `fk_adoption_adopter` (`adopter_id`),
  ADD KEY `fk_adoption_reviewer` (`reviewed_by`);

--
-- Indexes for table `animal`
--
ALTER TABLE `animal`
  ADD PRIMARY KEY (`animal_id`),
  ADD KEY `fk_animal_user` (`user_id`);

--
-- Indexes for table `animal_status_history`
--
ALTER TABLE `animal_status_history`
  ADD PRIMARY KEY (`status_id`),
  ADD KEY `fk_status_animal` (`animal_id`),
  ADD KEY `fk_status_user` (`changed_by`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`donation_id`),
  ADD KEY `fk_donation_donor` (`donor_id`),
  ADD KEY `fk_donation_user` (`user_id`);

--
-- Indexes for table `donation_details`
--
ALTER TABLE `donation_details`
  ADD PRIMARY KEY (`donation_detail_id`),
  ADD KEY `fk_donationdetail_donation` (`donation_id`);

--
-- Indexes for table `donation_usage`
--
ALTER TABLE `donation_usage`
  ADD PRIMARY KEY (`expense_id`,`donation_id`),
  ADD KEY `fk_usage_donation` (`donation_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`expense_id`),
  ADD KEY `fk_expense_user` (`paid_by`),
  ADD KEY `fk_expense_animal` (`animal_id`);

--
-- Indexes for table `feeding_notes`
--
ALTER TABLE `feeding_notes`
  ADD PRIMARY KEY (`note_id`),
  ADD KEY `fk_note_feeding` (`feeding_id`);

--
-- Indexes for table `feeding_schedule`
--
ALTER TABLE `feeding_schedule`
  ADD PRIMARY KEY (`feeding_id`),
  ADD KEY `fk_feeding_animal` (`animal_id`),
  ADD KEY `fk_feeding_user` (`user_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `fk_report_reporter` (`reported_by`),
  ADD KEY `fk_report_animal` (`animal_id`),
  ADD KEY `fk_report_handler` (`handled_by`);

--
-- Indexes for table `report_type`
--
ALTER TABLE `report_type`
  ADD PRIMARY KEY (`report_type_id`),
  ADD KEY `fk_reporttype_report` (`report_id`);

--
-- Indexes for table `treatment`
--
ALTER TABLE `treatment`
  ADD PRIMARY KEY (`treatment_id`),
  ADD KEY `fk_treatment_vet` (`vet_id`),
  ADD KEY `fk_treatment_animal` (`animal_id`);

--
-- Indexes for table `treatment_description`
--
ALTER TABLE `treatment_description`
  ADD PRIMARY KEY (`description_id`),
  ADD KEY `fk_treatmentdesc_treatment` (`treatment_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `bracu_id` (`bracu_id`);

--
-- Indexes for table `vets`
--
ALTER TABLE `vets`
  ADD PRIMARY KEY (`vet_id`),
  ADD UNIQUE KEY `license_no` (`license_no`);

--
-- Indexes for table `volunteer_stats`
--
ALTER TABLE `volunteer_stats`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adoption_request`
--
ALTER TABLE `adoption_request`
  MODIFY `adoption_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `animal`
--
ALTER TABLE `animal`
  MODIFY `animal_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `animal_status_history`
--
ALTER TABLE `animal_status_history`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `donation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donation_details`
--
ALTER TABLE `donation_details`
  MODIFY `donation_detail_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feeding_notes`
--
ALTER TABLE `feeding_notes`
  MODIFY `note_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feeding_schedule`
--
ALTER TABLE `feeding_schedule`
  MODIFY `feeding_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `report_type`
--
ALTER TABLE `report_type`
  MODIFY `report_type_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `treatment`
--
ALTER TABLE `treatment`
  MODIFY `treatment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `treatment_description`
--
ALTER TABLE `treatment_description`
  MODIFY `description_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vets`
--
ALTER TABLE `vets`
  MODIFY `vet_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adoption_request`
--
ALTER TABLE `adoption_request`
  ADD CONSTRAINT `fk_adoption_adopter` FOREIGN KEY (`adopter_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_adoption_animal` FOREIGN KEY (`animal_id`) REFERENCES `animal` (`animal_id`),
  ADD CONSTRAINT `fk_adoption_report` FOREIGN KEY (`report_id`) REFERENCES `reports` (`report_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_adoption_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `animal`
--
ALTER TABLE `animal`
  ADD CONSTRAINT `fk_animal_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `animal_status_history`
--
ALTER TABLE `animal_status_history`
  ADD CONSTRAINT `fk_status_animal` FOREIGN KEY (`animal_id`) REFERENCES `animal` (`animal_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_status_user` FOREIGN KEY (`changed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `fk_donation_donor` FOREIGN KEY (`donor_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_donation_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `donation_details`
--
ALTER TABLE `donation_details`
  ADD CONSTRAINT `fk_donationdetail_donation` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`donation_id`) ON DELETE CASCADE;

--
-- Constraints for table `donation_usage`
--
ALTER TABLE `donation_usage`
  ADD CONSTRAINT `fk_usage_donation` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`donation_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_usage_expense` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`expense_id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `fk_expense_animal` FOREIGN KEY (`animal_id`) REFERENCES `animal` (`animal_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_expense_user` FOREIGN KEY (`paid_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `feeding_notes`
--
ALTER TABLE `feeding_notes`
  ADD CONSTRAINT `fk_note_feeding` FOREIGN KEY (`feeding_id`) REFERENCES `feeding_schedule` (`feeding_id`) ON DELETE CASCADE;

--
-- Constraints for table `feeding_schedule`
--
ALTER TABLE `feeding_schedule`
  ADD CONSTRAINT `fk_feeding_animal` FOREIGN KEY (`animal_id`) REFERENCES `animal` (`animal_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_feeding_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `fk_report_animal` FOREIGN KEY (`animal_id`) REFERENCES `animal` (`animal_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_report_handler` FOREIGN KEY (`handled_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_report_reporter` FOREIGN KEY (`reported_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `report_type`
--
ALTER TABLE `report_type`
  ADD CONSTRAINT `fk_reporttype_report` FOREIGN KEY (`report_id`) REFERENCES `reports` (`report_id`) ON DELETE CASCADE;

--
-- Constraints for table `treatment`
--
ALTER TABLE `treatment`
  ADD CONSTRAINT `fk_treatment_animal` FOREIGN KEY (`animal_id`) REFERENCES `animal` (`animal_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_treatment_vet` FOREIGN KEY (`vet_id`) REFERENCES `vets` (`vet_id`);

--
-- Constraints for table `treatment_description`
--
ALTER TABLE `treatment_description`
  ADD CONSTRAINT `fk_treatmentdesc_treatment` FOREIGN KEY (`treatment_id`) REFERENCES `treatment` (`treatment_id`) ON DELETE CASCADE;

--
-- Constraints for table `volunteer_stats`
--
ALTER TABLE `volunteer_stats`
  ADD CONSTRAINT `fk_volunteer_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
