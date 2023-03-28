INSERT INTO report (id,name,idReportCategory,file,sortOrder,idle,orientation,hasCsv,hasView,hasPrint,hasPdf,hasToday,hasFavorite,hasWord,hasExcel) VALUES (2000,'reportPlanResourceWeeklyCOEXYA',2,'resourcePlanWeekly.php',240,0,'L',0,1,1,1,1,1,0,1);
INSERT INTO reportparameter (id,idReport,name,paramType,sortOrder,idle,defaultValue,multiple) VALUES (5000,2000,'month','month',10,0,'currentMonth',0);
INSERT INTO reportparameter (id,idReport,name,paramType,sortOrder,idle,defaultValue,multiple) VALUES (5001,2000,'idProject','projectList',1,0,'currentProject',0);
INSERT INTO reportparameter (id,idReport,name,paramType,sortOrder,idle,multiple) VALUES (5002,2000,'idTeam','teamList',5,0,0);
INSERT INTO reportparameter (id,idReport,name,paramType,sortOrder,idle,multiple) VALUES (5003,2000,'includeNextMonth','boolean',50,0,0);
INSERT INTO reportparameter (id,idReport,name,paramType,sortOrder,idle,multiple) VALUES (5004,2000,'idOrganization','organizationList',3,0,0);
INSERT INTO reportparameter (id,idReport,name,paramType,sortOrder,idle,multiple) VALUES (5005,2000,'includeThreeMonth','boolean',50,0,0);
INSERT INTO reportparameter (id,idReport,name,paramType,sortOrder,idle,multiple) VALUES (5006,2000,'includeSixMonth','boolean',50,0,0);
