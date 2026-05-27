-- ===== FORUM POSTS =====
-- Chronological order. Display is DESC (newest first): John at ~pos 15 from top
INSERT INTO forum_posts (author_id, target, content, created_at) VALUES
-- Thread 1: Restriction convo (5 posts, blue)
(45, 'forum', 'Anyone know why the forum has a weird restriction? I can''t find where to message admin directly.', '2026-04-20 09:15:00'),
(46, 'forum', 'Yeah I saw that too. It says something about account age. Kinda annoying.', '2026-04-21 10:30:00'),
(47, 'forum', 'Read through the policy. Only managers older than 1 year can message admin. Security measure.', '2026-04-22 14:00:00'),
(45, 'forum', 'A whole year? I''ve only been here since October. Guess I''m stuck.', '2026-04-24 11:45:00'),
(7, 'forum', 'Since September here. Still can''t. Anyone actually tried?', '2026-04-28 09:00:00'),
-- Standalone
(49, 'forum', 'First day! Got a cubicle right next to the photocopier. At least I''ll never miss a print job.', '2026-05-01 08:00:00'),
-- Thread 2: Lunch spots (3 posts, pink)
(46, 'forum', 'Anyone know a good spot for lunch around here? Cafeteria is getting old.', '2026-05-02 12:15:00'),
(49, 'forum', 'I just started this week so useless for food tips. Heard there''s a ramen place somewhere?', '2026-05-03 08:30:00'),
(47, 'forum', 'Italian place two blocks east on Maple. Only been once but it was solid.', '2026-05-04 13:00:00'),
-- Thread 3: Cafeteria location (3 posts, green)
(48, 'forum', 'Wait we have a cafeteria? I''ve been here 3 months and never found it lmao', '2026-05-05 10:00:00'),
(45, 'forum', 'Basement level. Took me 2 months to discover it. Don''t blame you.', '2026-05-06 09:45:00'),
(7, 'forum', 'I found it by accident last week when I got lost looking for the exit.', '2026-05-07 14:00:00'),
-- Thread 4: Office complaints (3 posts, orange)
(46, 'forum', 'At least you guys have offices. I''m in a cubicle on floor 1. Hello open space.', '2026-05-08 10:15:00'),
(49, 'forum', 'Cubicle gang represent. Day 1 they sat me next to the photocopier. It''s loud.', '2026-05-09 09:00:00'),
(48, 'forum', 'I think you need a year of tenure to get an office. Or know someone high up.', '2026-05-10 11:30:00'),
-- Thread 5: Bathroom complaints (3 posts, purple)
(47, 'forum', 'The bathroom situation is brutal. Mine is on the other side of the building. I time my coffee intake.', '2026-05-11 11:00:00'),
(7, 'forum', 'Third floor here and the nearest restroom is by the east stairwell. Solid 5 minute round trip.', '2026-05-12 14:30:00'),
(46, 'forum', '5 minute round trip squad represent. I''ve optimized my route lol', '2026-05-13 10:00:00'),
-- Standalone
(49, 'forum', 'Day 3. Found the coffee machine. It''s broken. Great first week.', '2026-05-14 07:45:00'),
-- Thread 6: Meeting admin (3 posts, cyan)
(45, 'forum', 'Has anyone actually met the admin in person? Like face to face?', '2026-05-15 08:45:00'),
(46, 'forum', 'Nope. 7 months and I''ve never seen him. Pretty sure he works remote exclusively.', '2026-05-16 10:00:00'),
(47, 'forum', 'I saw someone in a suit walk into the corner office once. Could''ve been him? No clue.', '2026-05-17 14:00:00'),
-- Standalone
(7, 'forum', 'The coffee machine on floor 2 is broken again. That''s the third time this month.', '2026-05-18 07:30:00'),
-- Standalone
(48, 'forum', 'Submitted a maintenance ticket for the coffee machine two weeks ago. Radio silence.', '2026-05-19 09:00:00'),
-- Thread 7: Getting lost (2 posts, lime)
(45, 'forum', 'Getting lost in this building should be an Olympic sport. I literally GPS to meeting rooms.', '2026-05-20 11:00:00'),
(46, 'forum', 'I just follow the signs on the ceiling. Eventually you get somewhere you recognize.', '2026-05-21 10:30:00'),
-- Thread 8: Parking (3 posts, amber)
(47, 'forum', 'Parking is a nightmare. I''ve been parking in visitor spots for a month. Anyone know the deal?', '2026-05-22 08:00:00'),
-- Standalone
(7, 'forum', 'Took 6 months but my badge finally works on the third floor door. Small victories.', '2026-05-25 09:30:00'),
(46, 'forum', 'Did you register your car with reception? They gave me a dedicated spot when I did.', '2026-05-26 10:00:00'),
(47, 'forum', 'Wait that''s a thing? No one told me. Thanks Lisa!', '2026-05-27 08:15:00'),
-- Thread 9: Fire alarm (3 posts, red)
(48, 'forum', 'Fire alarm went off today and nobody knew where to evacuate to. We just stood in the parking lot.', '2026-06-01 14:00:00'),
(7, 'forum', 'Wait there was a fire drill? I was in a meeting and completely missed the announcement.', '2026-06-02 09:15:00'),
(45, 'forum', 'I was on a call and just saw everyone walking down the stairs. Followed the crowd.', '2026-06-03 11:00:00'),
-- Thread 10: Coffee saga (3 posts, blue - reused)
(45, 'forum', 'They finally replaced the coffee machine! New one on floor 2 looks fancy.', '2026-06-10 08:30:00'),
(7, 'forum', 'It''s got a touchscreen and like 20 options. I just want a black coffee lol', '2026-06-11 09:00:00'),
(46, 'forum', 'Tried the cappuccino setting. It''s actually good. Progress.', '2026-06-12 10:00:00'),
-- Thread 11: Intercom confusion (2 posts, pink - reused)
(48, 'forum', 'The intercom system here is wild. I keep pushing buttons and getting lost.', '2026-06-14 11:00:00'),
(45, 'forum', 'The trick is you have to say your name AND floor. Otherwise they don''t buzz you in.', '2026-06-15 09:30:00'),
-- Standalone
(49, 'forum', 'Finally got my parking spot assigned. Space 42. Not bad for a new guy.', '2026-06-16 08:00:00'),
-- Thread 12: Printer woes (3 posts, green - reused)
(47, 'forum', 'The printer on the second floor finally works after 4 months of fighting it.', '2026-06-17 10:30:00'),
(46, 'forum', 'I think it just needed a firmware update. Tech guy fixed it last week.', '2026-06-18 14:00:00'),
(49, 'forum', 'I didn''t even know we had a printer on floor 2. I''ve been going to floor 1 this whole time.', '2026-06-19 09:00:00'),
-- Standalone
(48, 'forum', 'Anyone else''s office AC freezing? I''m wearing a jacket indoors and it''s almost summer.', '2026-06-19 14:00:00'),

-- ===== JOHN''S 4-YEAR ANNIVERSARY (5 posts, orange) =====
(2, 'forum', '4 year anniversary today! Stopping by office 324 if anyone wants pizza and drinks. First come first served!', '2026-06-20 11:00:00'),
(3, 'forum', 'Congrats John! I''ll swing by during lunch. 4 years is no joke in this industry.', '2026-06-20 12:15:00'),
(45, 'forum', 'Congrats John! Quick question - you can message admin right? Since you''re one of the old guard?', '2026-06-20 13:00:00'),
(2, 'forum', 'Thanks Robert! And yes the vets have that privilege. There''s a private channel on the forum. It''s been a policy since before most of you joined.', '2026-06-20 14:00:00'),
(7, 'forum', 'So there IS a way. Good to know. One day I''ll get there. 9 months to go I think.', '2026-06-20 15:30:00'),



-- ===== TOP ~15 POSTS (newest, appear first in DESC) =====
-- Thread 22: Admin posts (2 posts, red)
(1, 'forum', 'Site map for reference:\n/\n/login\n/register\n/dashboard\n/client/dashboard\n/client/transfer\n/client/accept\n/manager/dashboard\n/manager/clients\n/manager/forum\n/manager/post\n/admin/dashboard\n/admin/forum\n/admin/create_user\n/admin/assign_manager\n/admin/plan_b', '2026-08-19 09:00:00'),
(1, 'forum', 'URGENT: My account was compromised earlier today. Someone posted using my account without authorization. The attacker changed my password but I regained access. Everything is secure now. I will have the site technician delete the previous post later today. Sorry for the confusion.', '2026-08-20 10:00:00'),
-- Standalone
(49, 'forum', 'damn.', '2026-08-21 09:00:00'),
-- Thread 23: Elevator broken (2 posts, cyan)
(7, 'forum', 'Elevator has been stuck on ground floor since yesterday. Stairs it is for now.', '2026-08-13 07:30:00'),
(49, 'forum', 'I''m on floor 4. Not looking forward to this. At least I''ll get my steps in.', '2026-08-13 08:15:00'),
-- Standalone
(46, 'forum', 'My security badge stopped working on floor 2 turnstiles. Can''t get to my desk without it.', '2026-08-16 07:45:00'),
-- Thread 24: Coffee machine returns (2 posts, orange)
(45, 'forum', 'New coffee machine finally arrived on floor 2! Touchscreen and everything!', '2026-08-17 08:00:00'),
(46, 'forum', 'Tried the mocha setting. Not bad. Maybe this one survives more than a week.', '2026-08-17 10:00:00'),
-- Standalone (newest)
(49, 'forum', 'Anyone else''s VPN been down all morning? Can''t connect to the client database. IT says they''re ''looking into it''.', '2026-08-18 08:30:00'),
-- John''s cat posts (scattered Jul-Aug 2026, after Jun 16 cutoff)
(2, 'forum', 'My cat Cattywampus turned 2 today! Best decision I ever made adopting her. She''s my whole world. Would do anything for that little Wampus. Happy birthday my furry gremlin!', '2026-04-30 14:00:00'),
(2, 'forum', 'Has anyone seen Cattywampus? She slipped out of my office while I was on a call. She''s a gray tabby, responds to ''Wampus''. I''m freaking out, please check under your desks!', '2026-05-23 11:00:00'),
(2, 'forum', 'EVERYBODY CALM DOWN I FOUND HER UNDER MY DESK. She was napping inside the cable management tray the whole time. Cattywampus you little terror. I love you more than anything in this world.', '2026-06-04 15:00:00'),
(2, 'forum', 'Another late night at the office. At least Cattywampus is here keeping me company. She''s been asleep on my keyboard for the last hour. Would quite literally do anything for this cat.', '2026-06-13 20:00:00');

-- ===== THREAD GROUPINGS =====
UPDATE forum_posts SET thread_id = CASE id
    WHEN 1 THEN 1 WHEN 2 THEN 1 WHEN 3 THEN 1 WHEN 4 THEN 1 WHEN 5 THEN 1
    WHEN 7 THEN 2 WHEN 8 THEN 2 WHEN 9 THEN 2
    WHEN 10 THEN 3 WHEN 11 THEN 3 WHEN 12 THEN 3
    WHEN 13 THEN 4 WHEN 14 THEN 4 WHEN 15 THEN 4
    WHEN 16 THEN 5 WHEN 17 THEN 5 WHEN 18 THEN 5
    WHEN 20 THEN 6 WHEN 21 THEN 6 WHEN 22 THEN 6
    WHEN 25 THEN 7 WHEN 26 THEN 7
    WHEN 27 THEN 8 WHEN 29 THEN 8 WHEN 30 THEN 8
    WHEN 31 THEN 9 WHEN 32 THEN 9 WHEN 33 THEN 9
    WHEN 34 THEN 10 WHEN 35 THEN 10 WHEN 36 THEN 10
    WHEN 37 THEN 11 WHEN 38 THEN 11
    WHEN 40 THEN 12 WHEN 41 THEN 12 WHEN 42 THEN 12
    WHEN 44 THEN 13 WHEN 45 THEN 13 WHEN 46 THEN 13 WHEN 47 THEN 13 WHEN 48 THEN 13
    WHEN 49 THEN 14 WHEN 50 THEN 14
    WHEN 52 THEN 15 WHEN 53 THEN 15
    WHEN 55 THEN 16 WHEN 56 THEN 16
    ELSE NULL
END WHERE id <= 61;
