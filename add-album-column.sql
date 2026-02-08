-- Add album column to songs table
ALTER TABLE songs ADD COLUMN album VARCHAR(100) AFTER artist;
