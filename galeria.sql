CREATE TABLE ext_translations (id INT NOT NULL, locale VARCHAR(8) NOT NULL, object_class VARCHAR(255) NOT NULL, field VARCHAR(32) NOT NULL, foreign_key VARCHAR(64) NOT NULL, content TEXT DEFAULT NULL, PRIMARY KEY(id));
CREATE INDEX translations_lookup_idx ON ext_translations (locale, object_class, foreign_key);
CREATE UNIQUE INDEX lookup_unique_idx ON ext_translations (locale, object_class, field, foreign_key);
CREATE TABLE ext_log_entries (id INT NOT NULL, action VARCHAR(8) NOT NULL, logged_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, object_id VARCHAR(32) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data TEXT DEFAULT NULL, username VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id));
CREATE INDEX log_class_lookup_idx ON ext_log_entries (object_class);
CREATE INDEX log_date_lookup_idx ON ext_log_entries (logged_at);
CREATE INDEX log_user_lookup_idx ON ext_log_entries (username);
COMMENT ON COLUMN ext_log_entries.data IS '(DC2Type:array)';
CREATE TABLE user__email_change_request (id INT NOT NULL, user_id INT NOT NULL, requested_email VARCHAR(90) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, confirmation_token VARCHAR(60) NOT NULL, PRIMARY KEY(id));
CREATE UNIQUE INDEX UNIQ_6EAAF2B4A76ED395 ON user__email_change_request (user_id);
CREATE TABLE user__notifications (id SERIAL NOT NULL, notification_group_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id));
CREATE INDEX IDX_A4D52FA4AB44E1E2 ON user__notifications (notification_group_id);
CREATE TABLE user__notification_groups (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id));
CREATE TABLE user__notification_group_intervals (id SERIAL NOT NULL, notification_group_id INT DEFAULT NULL, user_id INT DEFAULT NULL, interval INT NOT NULL, PRIMARY KEY(id));
CREATE INDEX IDX_7FFEAFF2AB44E1E2 ON user__notification_group_intervals (notification_group_id);
CREATE INDEX IDX_7FFEAFF2A76ED395 ON user__notification_group_intervals (user_id);
CREATE TABLE user__notification_queue (id INT NOT NULL, send_after TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, from_address VARCHAR(90) NOT NULL, from_name VARCHAR(60) NOT NULL, to_address VARCHAR(90) NOT NULL, subject VARCHAR(90) NOT NULL, body_html TEXT NOT NULL, body_plain TEXT NOT NULL, PRIMARY KEY(id));
CREATE TABLE user__references (id INT NOT NULL, ref_user_id INT NOT NULL, new_user_id INT DEFAULT NULL, new_user_fb_id BIGINT DEFAULT NULL, new_user_email VARCHAR(255) DEFAULT NULL, active BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, activated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, joined_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id));
CREATE UNIQUE INDEX UNIQ_554E1471E7824ECE ON user__references (new_user_fb_id);
CREATE INDEX IDX_554E1471637A8045 ON user__references (ref_user_id);
CREATE INDEX IDX_554E14717C2D807B ON user__references (new_user_id);
CREATE TABLE user__unsubscribers (id INT NOT NULL, hashed_email VARCHAR(40) NOT NULL, PRIMARY KEY(id));
CREATE UNIQUE INDEX UNIQ_E9530E3458E7F24 ON user__unsubscribers (hashed_email);
CREATE TABLE user__users (id SERIAL NOT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled BOOLEAN NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, locked BOOLEAN NOT NULL, expired BOOLEAN NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, roles TEXT NOT NULL, credentials_expired BOOLEAN NOT NULL, credentials_expire_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, facebookId VARCHAR(255) DEFAULT NULL, twitterID VARCHAR(255) DEFAULT NULL, twitter_username VARCHAR(255) DEFAULT NULL, activity INT DEFAULT NULL, ask_again BOOLEAN DEFAULT NULL, registeredWith VARCHAR(8) NOT NULL, registeredWithId VARCHAR(100) NOT NULL, PRIMARY KEY(id));
CREATE UNIQUE INDEX UNIQ_FBE9524892FC23A8 ON user__users (username_canonical);
CREATE UNIQUE INDEX UNIQ_FBE95248A0D96FBF ON user__users (email_canonical);
COMMENT ON COLUMN user__users.roles IS '(DC2Type:array)';
CREATE TABLE user__users__notifications (user_id INT NOT NULL, notification_id INT NOT NULL, PRIMARY KEY(user_id, notification_id));
CREATE INDEX IDX_CC2DAA03A76ED395 ON user__users__notifications (user_id);
CREATE INDEX IDX_CC2DAA03EF1A9D84 ON user__users__notifications (notification_id);
CREATE TABLE user__friends_invitations (id INT NOT NULL, invitation_by INT DEFAULT NULL, invitation_for INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, accepted BOOLEAN DEFAULT NULL, PRIMARY KEY(id));
CREATE INDEX IDX_48BFF79723AFCFE2 ON user__friends_invitations (invitation_by);
CREATE INDEX IDX_48BFF79739BFCE0D ON user__friends_invitations (invitation_for);
CREATE TABLE user__regular (id SERIAL NOT NULL, user_id INT DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) DEFAULT NULL, gender INT DEFAULT NULL, birthday DATE DEFAULT NULL, about_me TEXT DEFAULT NULL, link VARCHAR(100) DEFAULT NULL, facebook_publish BOOLEAN DEFAULT NULL, pref_locale VARCHAR(4) DEFAULT NULL, PRIMARY KEY(id));
CREATE UNIQUE INDEX UNIQ_DEB406A9A76ED395 ON user__regular (user_id);
CREATE TABLE user__friends (user_regular_id INT NOT NULL, friend_user_regular_id INT NOT NULL, PRIMARY KEY(user_regular_id, friend_user_regular_id));
CREATE INDEX IDX_B52B751447898C13 ON user__friends (user_regular_id);
CREATE INDEX IDX_B52B7514FE5CA101 ON user__friends (friend_user_regular_id);
CREATE TABLE galleries (id SERIAL NOT NULL, regular_id INT DEFAULT NULL, user_id INT DEFAULT NULL, notice_id INT DEFAULT NULL, original_id INT DEFAULT NULL, PRIMARY KEY(id));
CREATE UNIQUE INDEX UNIQ_F70E6EB7E0EE4AFF ON galleries (regular_id);
CREATE UNIQUE INDEX UNIQ_F70E6EB7A76ED395 ON galleries (user_id);
CREATE UNIQUE INDEX UNIQ_F70E6EB77D540AB ON galleries (notice_id);
CREATE UNIQUE INDEX UNIQ_F70E6EB7108B7592 ON galleries (original_id);
CREATE TABLE images (id SERIAL NOT NULL, gallery_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, sequence INT DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id));
CREATE INDEX IDX_E01FBE6A4E7AF8F ON images (gallery_id);
CREATE TABLE notice__dictionary (id SERIAL NOT NULL, text VARCHAR(255) NOT NULL, tag BOOLEAN NOT NULL, quantity_search INT NOT NULL, quantity_tag INT NOT NULL, search_activated BOOLEAN NOT NULL, tag_activated BOOLEAN NOT NULL, disabled BOOLEAN NOT NULL, PRIMARY KEY(id));
CREATE UNIQUE INDEX UNIQ_A9627E2D3B8BA7C7 ON notice__dictionary (text);
CREATE TABLE notice__facebook_feed (id INT NOT NULL, notice_id INT DEFAULT NULL, fbUserId VARCHAR(20) NOT NULL, photoId VARCHAR(20) DEFAULT NULL, postId VARCHAR(35) NOT NULL, PRIMARY KEY(id));
CREATE INDEX IDX_DB33587C7D540AB ON notice__facebook_feed (notice_id);
CREATE TABLE notice__notices (id SERIAL NOT NULL, type_id INT DEFAULT NULL, user_id INT NOT NULL, title VARCHAR(255) DEFAULT NULL, content TEXT DEFAULT NULL, allowed INT DEFAULT NULL, draft BOOLEAN DEFAULT NULL, link VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, end_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id));
CREATE INDEX IDX_4134CB49C54C8C93 ON notice__notices (type_id);
CREATE INDEX IDX_4134CB49A76ED395 ON notice__notices (user_id);
CREATE TABLE notice__property_types (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, element INT NOT NULL, options VARCHAR(255) DEFAULT NULL, expanded BOOLEAN NOT NULL, multiple BOOLEAN NOT NULL, PRIMARY KEY(id));
CREATE TABLE notice__reviews (id SERIAL NOT NULL, author_id INT NOT NULL, notice_id INT DEFAULT NULL, user_id INT DEFAULT NULL, text VARCHAR(255) NOT NULL, type INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id));
CREATE INDEX IDX_46684194F675F31B ON notice__reviews (author_id);
CREATE INDEX IDX_466841947D540AB ON notice__reviews (notice_id);
CREATE INDEX IDX_46684194A76ED395 ON notice__reviews (user_id);
CREATE TABLE notice__types (id SERIAL NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, sequence INT NOT NULL, PRIMARY KEY(id));
CREATE INDEX IDX_881C2331727ACA70 ON notice__types (parent_id);
CREATE TABLE notice__type__properties (type_id INT NOT NULL, propertytype_id INT NOT NULL, PRIMARY KEY(type_id, propertytype_id));
CREATE INDEX IDX_7E1754C3C54C8C93 ON notice__type__properties (type_id);
CREATE INDEX IDX_7E1754C34208046C ON notice__type__properties (propertytype_id);
CREATE TABLE notice__values (id SERIAL NOT NULL, notice_id INT DEFAULT NULL, property_id INT NOT NULL, value VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id));
CREATE INDEX IDX_4D7150DA7D540AB ON notice__values (notice_id);
CREATE INDEX IDX_4D7150DA549213EC ON notice__values (property_id);
CREATE TABLE message__messages (id INT NOT NULL, sender_user_id INT DEFAULT NULL, receiver_user_id INT DEFAULT NULL, first_message_id INT DEFAULT NULL, prev_message_id INT DEFAULT NULL, next_message_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content TEXT NOT NULL, read BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, sender_deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, receiver_deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, system BOOLEAN NOT NULL, PRIMARY KEY(id));
CREATE INDEX IDX_9F7B5DC02A98155E ON message__messages (sender_user_id);
CREATE INDEX IDX_9F7B5DC0DA57E237 ON message__messages (receiver_user_id);
CREATE INDEX IDX_9F7B5DC0C2E2722E ON message__messages (first_message_id);
CREATE UNIQUE INDEX UNIQ_9F7B5DC05993B147 ON message__messages (prev_message_id);
CREATE UNIQUE INDEX UNIQ_9F7B5DC0ECEDF8EA ON message__messages (next_message_id);
CREATE TABLE message__about_user (message_id INT NOT NULL, about_user_id INT NOT NULL, PRIMARY KEY(message_id, about_user_id));
CREATE INDEX IDX_9DB25398537A1329 ON message__about_user (message_id);
CREATE INDEX IDX_9DB25398D07FE4B4 ON message__about_user (about_user_id);
CREATE TABLE message__about_notice (message_id INT NOT NULL, about_notice_id INT NOT NULL, PRIMARY KEY(message_id, about_notice_id));
CREATE INDEX IDX_4DFCD612537A1329 ON message__about_notice (message_id);
CREATE INDEX IDX_4DFCD61233F78756 ON message__about_notice (about_notice_id);
CREATE TABLE location (id SERIAL NOT NULL, user_id INT DEFAULT NULL, notice_id INT DEFAULT NULL, postcode VARCHAR(10) DEFAULT NULL, city VARCHAR(100) DEFAULT NULL, district VARCHAR(100) DEFAULT NULL, street VARCHAR(100) DEFAULT NULL, country VARCHAR(100) DEFAULT NULL, pgisGeog GEOGRAPHY(Point) DEFAULT NULL, pgisGeom GEOMETRY(Point) DEFAULT NULL, PRIMARY KEY(id));
CREATE UNIQUE INDEX UNIQ_5E9E89CBA76ED395 ON location (user_id);
CREATE UNIQUE INDEX UNIQ_5E9E89CB7D540AB ON location (notice_id);
COMMENT ON COLUMN location.pgisGeog IS '(DC2Type:geography)';
COMMENT ON COLUMN location.pgisGeom IS '(DC2Type:geometry)';
CREATE TABLE stickers (id SERIAL NOT NULL, user_id INT DEFAULT NULL, notice_id INT DEFAULT NULL, review_id INT DEFAULT NULL, reported_by_id INT NOT NULL, reason INT NOT NULL, description TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, discarded_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id));
CREATE INDEX IDX_D88DAC16A76ED395 ON stickers (user_id);
CREATE INDEX IDX_D88DAC167D540AB ON stickers (notice_id);
CREATE INDEX IDX_D88DAC163E2E969B ON stickers (review_id);
CREATE INDEX IDX_D88DAC1671CE806 ON stickers (reported_by_id);
ALTER TABLE user__email_change_request ADD CONSTRAINT FK_6EAAF2B4A76ED395 FOREIGN KEY (user_id) REFERENCES user__users (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE user__notifications ADD CONSTRAINT FK_A4D52FA4AB44E1E2 FOREIGN KEY (notification_group_id) REFERENCES user__notification_groups (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE user__notification_group_intervals ADD CONSTRAINT FK_7FFEAFF2AB44E1E2 FOREIGN KEY (notification_group_id) REFERENCES user__notification_groups (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE user__notification_group_intervals ADD CONSTRAINT FK_7FFEAFF2A76ED395 FOREIGN KEY (user_id) REFERENCES user__users (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE user__references ADD CONSTRAINT FK_554E1471637A8045 FOREIGN KEY (ref_user_id) REFERENCES user__users (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE user__references ADD CONSTRAINT FK_554E14717C2D807B FOREIGN KEY (new_user_id) REFERENCES user__users (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE user__users__notifications ADD CONSTRAINT FK_CC2DAA03A76ED395 FOREIGN KEY (user_id) REFERENCES user__users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE user__users__notifications ADD CONSTRAINT FK_CC2DAA03EF1A9D84 FOREIGN KEY (notification_id) REFERENCES user__notifications (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE user__friends_invitations ADD CONSTRAINT FK_48BFF79723AFCFE2 FOREIGN KEY (invitation_by) REFERENCES user__regular (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE user__friends_invitations ADD CONSTRAINT FK_48BFF79739BFCE0D FOREIGN KEY (invitation_for) REFERENCES user__regular (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE user__regular ADD CONSTRAINT FK_DEB406A9A76ED395 FOREIGN KEY (user_id) REFERENCES user__users (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE user__friends ADD CONSTRAINT FK_B52B751447898C13 FOREIGN KEY (user_regular_id) REFERENCES user__regular (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE user__friends ADD CONSTRAINT FK_B52B7514FE5CA101 FOREIGN KEY (friend_user_regular_id) REFERENCES user__regular (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE galleries ADD CONSTRAINT FK_F70E6EB7E0EE4AFF FOREIGN KEY (regular_id) REFERENCES user__regular (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE galleries ADD CONSTRAINT FK_F70E6EB7A76ED395 FOREIGN KEY (user_id) REFERENCES user__users (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE galleries ADD CONSTRAINT FK_F70E6EB77D540AB FOREIGN KEY (notice_id) REFERENCES notice__notices (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE galleries ADD CONSTRAINT FK_F70E6EB7108B7592 FOREIGN KEY (original_id) REFERENCES galleries (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A4E7AF8F FOREIGN KEY (gallery_id) REFERENCES galleries (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE notice__facebook_feed ADD CONSTRAINT FK_DB33587C7D540AB FOREIGN KEY (notice_id) REFERENCES notice__notices (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE notice__notices ADD CONSTRAINT FK_4134CB49C54C8C93 FOREIGN KEY (type_id) REFERENCES notice__types (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE notice__notices ADD CONSTRAINT FK_4134CB49A76ED395 FOREIGN KEY (user_id) REFERENCES user__users (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE notice__reviews ADD CONSTRAINT FK_46684194F675F31B FOREIGN KEY (author_id) REFERENCES user__users (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE notice__reviews ADD CONSTRAINT FK_466841947D540AB FOREIGN KEY (notice_id) REFERENCES notice__notices (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE notice__reviews ADD CONSTRAINT FK_46684194A76ED395 FOREIGN KEY (user_id) REFERENCES user__users (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE notice__types ADD CONSTRAINT FK_881C2331727ACA70 FOREIGN KEY (parent_id) REFERENCES notice__types (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE notice__type__properties ADD CONSTRAINT FK_7E1754C3C54C8C93 FOREIGN KEY (type_id) REFERENCES notice__types (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE notice__type__properties ADD CONSTRAINT FK_7E1754C34208046C FOREIGN KEY (propertytype_id) REFERENCES notice__property_types (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE notice__values ADD CONSTRAINT FK_4D7150DA7D540AB FOREIGN KEY (notice_id) REFERENCES notice__notices (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE notice__values ADD CONSTRAINT FK_4D7150DA549213EC FOREIGN KEY (property_id) REFERENCES notice__property_types (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE message__messages ADD CONSTRAINT FK_9F7B5DC02A98155E FOREIGN KEY (sender_user_id) REFERENCES user__users (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE message__messages ADD CONSTRAINT FK_9F7B5DC0DA57E237 FOREIGN KEY (receiver_user_id) REFERENCES user__users (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE message__messages ADD CONSTRAINT FK_9F7B5DC0C2E2722E FOREIGN KEY (first_message_id) REFERENCES message__messages (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE message__messages ADD CONSTRAINT FK_9F7B5DC05993B147 FOREIGN KEY (prev_message_id) REFERENCES message__messages (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE message__messages ADD CONSTRAINT FK_9F7B5DC0ECEDF8EA FOREIGN KEY (next_message_id) REFERENCES message__messages (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE message__about_user ADD CONSTRAINT FK_9DB25398537A1329 FOREIGN KEY (message_id) REFERENCES message__messages (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE message__about_user ADD CONSTRAINT FK_9DB25398D07FE4B4 FOREIGN KEY (about_user_id) REFERENCES user__users (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE message__about_notice ADD CONSTRAINT FK_4DFCD612537A1329 FOREIGN KEY (message_id) REFERENCES message__messages (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE message__about_notice ADD CONSTRAINT FK_4DFCD61233F78756 FOREIGN KEY (about_notice_id) REFERENCES notice__notices (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE location ADD CONSTRAINT FK_5E9E89CBA76ED395 FOREIGN KEY (user_id) REFERENCES user__users (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB7D540AB FOREIGN KEY (notice_id) REFERENCES notice__notices (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE stickers ADD CONSTRAINT FK_D88DAC16A76ED395 FOREIGN KEY (user_id) REFERENCES user__users (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE stickers ADD CONSTRAINT FK_D88DAC167D540AB FOREIGN KEY (notice_id) REFERENCES notice__notices (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE stickers ADD CONSTRAINT FK_D88DAC163E2E969B FOREIGN KEY (review_id) REFERENCES notice__reviews (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE stickers ADD CONSTRAINT FK_D88DAC1671CE806 FOREIGN KEY (reported_by_id) REFERENCES user__users (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
