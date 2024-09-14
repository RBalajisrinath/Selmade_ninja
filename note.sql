-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `note`;
USE `note`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Table structure for table `notes`
--

CREATE TABLE IF NOT EXISTS `notes` (
  `sno` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `fingerprint` varchar(255) DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`sno`, `title`, `description`, `date`, `fingerprint`) VALUES
(1, 'Study', 'You have to complete this project within 2 days', '2024-07-13 13:30:11', NULL),
(2, 'Study for Math Exam', 'Review chapters 5 to 8 and complete practice problems. Focus on understanding key concepts and formulas, and take practice tests to gauge your readiness.\r\n', '2024-07-13 13:32:36', NULL),
(3, 'Complete Science Project', 'Finalize the research and prepare the presentation for the science project. Ensure all experiments are documented, and create visual aids to support your findings.', '2024-07-13 13:32:53', NULL),
(4, 'Group Study Session', 'Organize a study session with classmates to review for the upcoming physics test. Prepare a list of topics to cover and gather study materials to share with the group.', '2024-07-13 13:33:26', NULL),
(5, 'hashshhshs', 'akjhsadhsadk;hsa', '2024-07-13 13:58:40', NULL),
(6, 'Play Cricke', 'Cricket 07 in pc22222', '0000-00-00 00:00:00', NULL),
(7, 'Play foot ball', 'Cricket 07ss', '0000-00-00 00:00:00', NULL),
(9, 'Submit History Essay', 'Write and proofread the history essay on the topic of the Industrial Revolution. Make sure to include all required references and submit it by the due date.', '0000-00-00 00:00:00', NULL),
(10, 'Submit History Essay', 'Write and proofread the history essay on the topic of the Industrial Revolution. Make sure to include all required references and submit it by the due date.', '0000-00-00 00:00:00', NULL),
(11, 'Prepare for Oral Presentation', 'Create slides and practice for the oral presentation on the Civil War. Focus on key events and their impact, and prepare to answer potential questions.\r\n', '0000-00-00 00:00:00', NULL),
(12, 'Attend Club Meeting', 'Join the weekly meeting of the Robotics Club. Participate in the discussion about the upcoming competition and contribute ideas for the project.', '0000-00-00 00:00:00', NULL),
(13, 'Revise Literature Notes', 'Review and organize notes from literature class. Highlight important themes and prepare for the upcoming quiz on classic novels.', '0000-00-00 00:00:00', NULL),
(15, 'Play with ball', 'I plays with bal', '0000-00-00 00:00:00', NULL),
(17, 'HTML Structure', 'The script is wrapped in a basic HTML structure.', '0000-00-00 00:00:00', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`sno`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `sno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;