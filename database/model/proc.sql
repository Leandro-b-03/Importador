SET @sql = NULL;
SELECT 
    GROUP_CONCAT(DISTINCT CONCAT('(case when ct.name = \'',
                ct.name,
                '\' then ct.value else null end) AS `',
                ct.name,
                '`'))
INTO @sql FROM
    (SELECT 
        cf.name, dcf.value
    FROM
        ip_custom_fields cf
    LEFT JOIN ip_data_custom_fields dcf ON (dcf.custom_field_id = cf.id)) ct;

SET @sql = CONCAT('SELECT dt.*, ', @sql, ' 
				  from ip_datas dt
				  left join ip_folders fd on (dt.folder_id = fd.id)
				  left join ip_data_custom_fields dcf ON (dt.id = dcf.data_id)
				  left join ip_custom_fields cf on (dcf.custom_field_id = cf.id)
				  left join (select dcf.data_id as data_id, cf.id as custom_field_id, cf.name as name, dcf.value as value
				  from ip_custom_fields cf
				  left join ip_data_custom_fields dcf on (dcf.custom_field_id = cf.id)) ct on (cf.id = ct.custom_field_id and dt.id = ct.data_id) group by dt.id');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;