<?php

//===GET===
//old sql

$sql['get_count']="
SELECT FOUND_ROWS() AS total
";

$sql['get_data_user_by_user_name'] = "
SELECT 
   UserId AS user_id,
   UserName AS user_name,
   RealName AS real_name,
   a.Description AS description,
   Active AS is_active,
   a.GroupId AS group_id,
   GroupName AS group_name,
   unitkerjaId,
   unitkerjaNama,
   userunitkerjaRoleId AS role_id,
   unitkerjaKodeSistem
FROM 
   gtfw_user a
   JOIN gtfw_group b ON b.GroupId = a.GroupId
   LEFT JOIN user_unit_kerja ON UserId = userunitkerjaUserId
   LEFT JOIN unit_kerja_ref ON unitkerjaId = userunitkerjaUnitkerjaId
WHERE
   UserName = %s
";

$sql['get_count_data_unitkerja'] = 
   "SELECT 
      count(DISTINCT unitkerjaId) AS total
	FROM unit_kerja_ref
		LEFT JOIN 
			(SELECT 
				unitkerjaId AS tempUnitId,
				unitkerjaKode AS tempUnitKode,
				unitkerjaNama AS tempUnitNama,
				unitkerjaParentId AS tempParentId
			FROM unit_kerja_ref 
			WHERE unitkerjaParentId = 0) tmpUnitKerja ON(unitkerjaParentId=tempUnitId)
		LEFT JOIN 
			gedung ON gedungUnitkerjaId = unitkerjaId
	WHERE 
		(unitkerjaKode/*LPAD(if(tempUnitKode IS NULL,unitkerjaKode*100,tempUnitKode*100+unitkerjaKode),4,'0')*/ LIKE '%s' OR unitkerjaKode/*LPAD(if(tempUnitKode IS NULL,unitkerjaKode*100,tempUnitKode*100+unitkerjaKode),4,'0')*/ LIKE '%s' )
		AND (unitkerjaNama LIKE '%s' OR tempUnitNama LIKE '%s') AND (gedungUnitkerjaId = %s OR unitkerjaParentId = %s OR '1' = %s) 
 ";

$sql['get_data_unitkerja'] = "
	SELECT
		SQL_CALC_FOUND_ROWS 
		LPAD((if(tempUnitKode IS NULL,a.unitkerjaKode,tempUnitKode))*100,4,'0') AS kodesatker,
		(if(tempUnitNama IS NULL,a.unitkerjaNama,tempUnitNama)) AS satker,
		(if(tempUnitId IS NULL,a.unitkerjaId,a.unitkerjaId)) AS id,
		a.unitkerjaKode /*LPAD(if(tempUnitKode IS NULL,unitkerjaKode*100,tempUnitKode*100+unitkerjaKode),4,'0')*/ AS kodeunit,
		(if(tempUnitNama IS NULL,a.unitkerjaNama,a.unitkerjaNama)) AS unit,
		a.unitkerjaParentId AS parentId,
		(SELECT COUNT(b.unitkerjaId) FROM unit_kerja_ref b WHERE b.unitkerjaParentId = a.unitkerjaId) AS isParent
	FROM unit_kerja_ref a
		LEFT JOIN 
			(SELECT 
				unitkerjaId AS tempUnitId,
				unitkerjaKode AS tempUnitKode,
				unitkerjaNama AS tempUnitNama,
				unitkerjaParentId AS tempParentId
			FROM unit_kerja_ref 
			WHERE unitkerjaParentId = 0) tmpUnitKerja ON (a.unitkerjaParentId=tempUnitId)
		LEFT JOIN 
			gedung ON gedungUnitkerjaId = unitkerjaId
	WHERE 
		(a.unitkerjaKode /*LPAD(if(tempUnitKode IS NULL,unitkerjaKode*100,tempUnitKode*100+unitkerjaKode),4,'0')*/ LIKE '%s' OR LPAD((if(tempUnitKode IS NULL,a.unitkerjaKode,tempUnitKode))*100,4,'0') LIKE '%s' )
		AND (a.unitkerjaNama LIKE '%s' OR tempUnitNama LIKE '%s') AND (a.unitkerjaKodeSistem = '%s' OR a.unitkerjaKodeSistem LIKE '%s') GROUP BY a.unitkerjaId
	ORDER BY a.unitkerjaKodeSistem
	LIMIT %s, %s
";

?>
