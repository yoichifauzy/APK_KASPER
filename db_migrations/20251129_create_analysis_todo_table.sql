-- Migration: create table for analysis todo/kanban items
CREATE TABLE IF NOT EXISTS analysis_todo (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT NULL,
  status ENUM('todo','inprogress','done') NOT NULL DEFAULT 'todo',
  due_date DATE NULL,
  position INT NOT NULL DEFAULT 0,
  created_by INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
);
