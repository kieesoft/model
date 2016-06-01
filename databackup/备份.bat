@echo off
echo  数据备份开始
set h=%time:~0,2%
set h=%h: =0%
set bak_filename=%date:~0,4%%date:~5,2%%date:~8,2%%h%%time:~3,2%%time:~6,2%
call mysqldump --add-drop-table --port 3306 -uroot -pcomefirst  model |gzip >%bak_filename%.sql.gz
echo  数据备份结束