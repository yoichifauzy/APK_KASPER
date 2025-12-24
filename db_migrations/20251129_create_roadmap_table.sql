-- Migration: create table for roadmap items / milestones
CREATE TABLE IF NOT EXISTS roadmap_item (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT NULL,
  owner_id INT NULL,
  start_date DATE NULL,
  end_date DATE NULL,
  status ENUM('planned','inprogress','blocked','done') NOT NULL DEFAULT 'planned',
  progress TINYINT NOT NULL DEFAULT 0,
  tags VARCHAR(255) NULL,
  created_by INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
);
