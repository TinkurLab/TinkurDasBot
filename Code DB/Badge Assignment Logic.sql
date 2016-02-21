/* First Liter */
SELECT IF((SELECT sum(volume)/(SELECT value FROM dasbot.ref_data WHERE name = 'ticks_per_liter') AS 'literspoured' FROM dasbot.drinks
GROUP BY userid) > 1, 1, 0 ) AS 'badgequalify', 2 AS 'badgeid';

/* Second Liter */
SELECT IF((SELECT sum(volume)/(SELECT value FROM dasbot.ref_data WHERE name = 'ticks_per_liter') AS 'literspoured' FROM dasbot.drinks
WHERE userid = 1) > 2, 1, 0 ) AS 'badgequalify', 2 AS 'badgeid';

/* 1 Liter Single Beer Keg 1 */
SELECT IF((SELECT sum(volume)/(SELECT value FROM dasbot.ref_data WHERE name = 'ticks_per_liter') AS 'literspoured' FROM dasbot.drinks
WHERE userid = 1 and kegid = 1) > 1, 1, 0 ) AS 'badgequalify', 2 AS 'badgeid';

/* 1 Liter Single Beer Keg 2 */
SELECT IF((SELECT sum(volume)/(SELECT value FROM dasbot.ref_data WHERE name = 'ticks_per_liter') AS 'literspoured' FROM dasbot.drinks
WHERE userid = 1 and kegid = 2) > 1, 1, 0 ) AS 'badgequalify', 2 AS 'badgeid';

/* 1 Liter Single Beer Keg 3 */
SELECT IF((SELECT sum(volume)/(SELECT value FROM dasbot.ref_data WHERE name = 'ticks_per_liter') AS 'literspoured' FROM dasbot.drinks
WHERE userid = 1 and kegid = 3) > 1, 1, 0 ) AS 'badgequalify', 2 AS 'badgeid';


SET @userid = 1;
SET @badgeid = 1;

SELECT IF(
	
((SELECT sum(volume)/(SELECT value FROM dasbot.ref_data WHERE name = 'ticks_per_liter') AS 'literspoured' FROM dasbot.drinks
	WHERE userid = @userid) > 1)
AND
((SELECT CASE (SELECT badgeqty FROM dasbot.badges WHERE badgeid = @badgeid) 
	WHEN 0 Then 1
	ELSE IF ((SELECT Count(*) FROM dasbot.badgesawarded WHERE badgeid = @badgeid) < (SELECT badgeqty FROM dasbot.badges WHERE badgeid = @badgeid), 1, 0)
	END) = 1)
, 1, 0 ) AS 'badgequalify';








