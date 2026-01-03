--
-- Table structure for table `LoanApplications`
--
-- This schema is designed for MySQL. To use it, create a database
-- and a user with privileges, then run this SQL command to create the table.
--

CREATE TABLE `LoanApplications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hubCategory` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fullName` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `membershipNumber` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loanPurpose` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loanAmount` decimal(10,2) DEFAULT NULL,
  `monthlyIncome` decimal(10,2) DEFAULT NULL,
  `existingSavings` decimal(10,2) DEFAULT NULL,
  `guarantor1Name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guarantor1MemberId` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guarantor2Name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guarantor2MemberId` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
